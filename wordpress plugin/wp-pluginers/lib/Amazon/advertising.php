<?php

class AWSProductAdvertising {
    private $accessKey,$secretKey,$region,$associateTag;
    public function __construct($accessKey,$secretKey,$associateTag) {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->associateTag = $associateTag;
        $this->setRegion("webservices.amazon.com");
    }

    public function searchKeyword($str,$index=1) {

        $uri = "/onca/xml";

        $params = array(
            "Service" => "AWSECommerceService",
            "Operation" => "ItemSearch",
            "AWSAccessKeyId" => $this->accessKey,
            "AssociateTag" => $this->associateTag,
            "SearchIndex" => "All",
            "ResponseGroup" => "Images,ItemAttributes,Offers,SalesRank",
            "Keywords" => $str,
            "ItemPage" => $index
        );

        if (!isset($params["Timestamp"])) {
            $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
        }

        ksort($params);

        $pairs = array();

        foreach ($params as $key => $value) {
            array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
        }

        $canonical_query_string = join("&", $pairs);

        $string_to_sign = "GET\n".$this->region."\n".$uri."\n".$canonical_query_string;

        $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->secretKey, true));
        $request_url = 'http://'.$this->region.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);
        $request = file_get_contents($request_url);
        //var_dump($request);
        $xml=simplexml_load_string($request);
        //Error checking
        $json = json_encode($xml);
        $result = json_decode($json);
        if ($result->Items->Request->IsValid == "False") {
            return ['ErrorCode' => $result->Items->Request->Errors->Error->Code,
                'ErrorMessage' => $result->Items->Request->Errors->Error->Message];
        } else {
            $items = [];
            $items['TotalResults'] = $result->Items->TotalResults;
            $items['TotalPages'] = $result->Items->TotalPages;
            require_once(__DIR__.'/ItemApi.php');
            foreach ($result->Items->Item as $item) {
                $itemApi = new ItemApi($this->associateTag,$this->accessKey,$this->secretKey);
                $itemApi->item_lookup($item);
                $items[] = $itemApi->get_item_data();
              /*  $t = [];
                $t['Id'] = $item->ASIN;
                $t['Name'] = $item->ItemAttributes->Title;
                $t['Description'] = $item->ItemAttributes->Feature;
                $t['Thumbnail'] = $item->SmallImage;
                $t['Link'] = $item->DetailPageURL;
                $t['Price'] = $item->ItemAttributes->ListPrice;
                $t['Date'] = $item->ItemAttributes->ReleaseDate;
                $t['SalesRank'] = $item->SalesRank;
                $items[] = $t;*/
               // var_dump($t);
                //var_dump($item);
                //break;
            }
            return $items;
        }
        //$node = $xml->xpath('/ItemSearchResponse');
        //var_dump($result);

        //echo "Signed URL: \"".$request_url."\"";
    }

    /**
     * @param mixed $region
     * @return AWSProductAdvertising
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getHtml($array) {
        $items = $this->searchKeyword(implode(" ",$array));
        $html = '';
        foreach ($items as $item) {
            $html .= <<<EOD
        <div style="display: flex">
        <a href="{$item->link}">
        <div class="image" style="text-align: center"><img class="image" src="{$item->mediumImage}" /></div>
        <div class="title" style="text-align: center"><strong>{$item->title}</strong></div>
        </a>
</div>
EOD;
        }

        return <<<EOD
    <div class="amazon-flex-container">
    {$html}
</div>
EOD;
    }

    function so_25888630_ad_between_paragraphs($content,$awsAds){
        /**-----------------------------------------------------------------------------
         *
         *  @author       Pieter Goosen <http://stackoverflow.com/users/1908141/pieter-goosen>
         *  @return       Ads in between $content
         *  @link         http://stackoverflow.com/q/25888630/1908141
         *
         *  Special thanks to the following answers on my questions that helped me to
         *  to achieve this
         *     - http://stackoverflow.com/a/26032282/1908141
         *     - http://stackoverflow.com/a/25988355/1908141
         *     - http://stackoverflow.com/a/26010955/1908141
         *     - http://wordpress.stackexchange.com/a/162787/31545
         *
         *------------------------------------------------------------------------------*/
        if( in_the_loop() ){ //Simply make sure that these changes effect the main query only

            /**-----------------------------------------------------------------------------
             *
             *  wptexturize is applied to the $content. This inserts p tags that will help to
             *  split the text into paragraphs. The text is split into paragraphs after each
             *  closing p tag. Remember, each double break constitutes a paragraph.
             *
             *  @todo If you really need to delete the attachments in paragraph one, you want
             *        to do it here before you start your foreach loop
             *
             *------------------------------------------------------------------------------*/
            $closing_p = '</p>';
            $paragraphs = explode( $closing_p, wptexturize($content) );

            /**-----------------------------------------------------------------------------
             *
             *  The amount of paragraphs is counted to determine add frequency. If there are
             *  less than four paragraphs, only one ad will be placed. If the paragraph count
             *  is more than 4, the text is split into two sections, $first and $second according
             *  to the midpoint of the text. $totals will either contain the full text (if
             *  paragraph count is less than 4) or an array of the two separate sections of
             *  text
             *
             *  @todo Set paragraph count to suite your needs
             *
             *------------------------------------------------------------------------------*/
            $count = count( $paragraphs );
            if( 4 >= $count ) {
                $totals = array( $paragraphs );
            }else{
                $midpoint = floor($count / 2);
                $first = array_slice($paragraphs, 0, $midpoint );
                if( $count%2 == 1 ) {
                    $second = array_slice( $paragraphs, $midpoint, $midpoint, true );
                }else{
                    $second = array_slice( $paragraphs, $midpoint, $midpoint-1, true );
                }
                $totals = array( $first, $second );
            }

            $new_paras = array();
            foreach ( $totals as $key_total=>$total ) {
                /**-----------------------------------------------------------------------------
                 *
                 *  This is where all the important stuff happens
                 *  The first thing that is done is a work count on every paragraph
                 *  Each paragraph is is also checked if the following tags, a, li and ul exists
                 *  If any of the above tags are found or the text count is less than 10, 0 is
                 *  returned for this paragraph. ($p will hold these values for later checking)
                 *  If none of the above conditions are true, 1 will be returned. 1 will represent
                 *  paragraphs that qualify for add insertion, and these will determine where an ad
                 *  will go
                 *  returned for this paragraph. ($p will hold these values for later checking)
                 *
                 *  @todo You can delete or add rules here to your liking
                 *
                 *------------------------------------------------------------------------------*/
                $p = array();
                foreach ( $total as $key_paras=>$paragraph ) {
                    $word_count = count(explode(' ', $paragraph));
                    if( preg_match( '~<(?:img|ul|li)[ >]~', $paragraph ) || $word_count < 10 ) {
                        $p[$key_paras] = 0;
                    }else{
                        $p[$key_paras] = 1;
                    }
                }

                /**-----------------------------------------------------------------------------
                 *
                 *  Return a position where an add will be inserted
                 *  This code checks if there are two adjacent 1's, and then return the second key
                 *  The ad will be inserted between these keys
                 *  If there are no two adjacent 1's, "no_ad" is returned into array $m
                 *  This means that no ad will be inserted in that section
                 *
                 *------------------------------------------------------------------------------*/
                $m = array();
                foreach ( $p as $key=>$value ) {
                    if( 1 === $value && array_key_exists( $key-1, $p ) && $p[$key] === $p[$key-1] && !$m){
                        $m[] = $key;
                    }elseif( !array_key_exists( $key+1, $p ) && !$m ) {
                        $m[] = 'no-ad';
                    }
                }

                /**-----------------------------------------------------------------------------
                 *
                 *  Use two different ads, one for each section
                 *  Only ad1 is displayed if there is less than 4 paragraphs
                 *
                 *  @todo Replace "PLACE YOUR ADD NO 1 HERE" with your add or code. Leave p tags
                 *  @todo I will try to insert widgets here to make it dynamic
                 *
                 *------------------------------------------------------------------------------*/
                if( $key_total == 0 ){
                    $ad = array( 'ad1' =>  $awsAds);
                }else{
                    $ad = array( 'ad2' => $awsAds);
                }

                /**-----------------------------------------------------------------------------
                 *
                 *  This code loops through all the paragraphs and checks each key against $mail
                 *  and $key_para
                 *  Each paragraph is returned to an array called $new_paras. $new_paras will
                 *  hold the new content that will be passed to $content.
                 *  If a key matches the value of $m (which holds the array key of the position
                 *  where an ad should be inserted) an add is inserted. If $m holds a value of
                 *  'no_ad', no ad will be inserted
                 *
                 *------------------------------------------------------------------------------*/
                foreach ( $total as $key_para=>$para ) {
                    if( !in_array( 'no_ad', $m ) && $key_para === $m[0] ){
                        $new_paras[key($ad)] = $ad[key($ad)];
                        $new_paras[$key_para] = $para;
                    }else{
                        $new_paras[$key_para] = $para;
                    }
                }
            }

            /**-----------------------------------------------------------------------------
             *
             *  $content should be a string, not an array. $new_paras is an array, which will
             *  not work. $new_paras are converted to a string with implode, and then passed
             *  to $content which will be our new content
             *
             *------------------------------------------------------------------------------*/
            $content =  implode( ' ', $new_paras );
        }
        return $content;
    }
}
/*header('Content-Type: application/json');
$output = [];
if (!isset($_GET['AccessKey'],$_GET['SecretKey'],$_GET['AssociateTag'],$_GET['KeyWords'])) {
    $output['ErrorMessage'] = "Missing AccessKey/SecretKey/AssociateId/KeyWords";
    $output['ErrorCode'] = "Missing.Parameters";
} else {
    $aws = new AWSProductAdvertising($_GET['AccessKey'],$_GET['SecretKey'],$_GET['AssociateTag']);
    $index = 1;
    if ($_GET['PageIndex'] && intval($_GET['PageIndex']) > 0) $index = $_GET['PageIndex'];
    $output = $aws->searchKeyword($_GET['KeyWords'],$index);
}

echo json_encode( $output);*/
?>