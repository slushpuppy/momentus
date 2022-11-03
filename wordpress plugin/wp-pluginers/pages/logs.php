<?php
function read_backward_line($filename, $lines, $revers = false)
{
    $offset = -1;
    $c = '';
    $read = '';
    $i = 0;
    $fp = @fopen($filename, "r");
    while( $lines && fseek($fp, $offset, SEEK_END) >= 0 ) {
        $c = fgetc($fp);
        if($c == "\n" || $c == "\r"){
            $lines--;
            if( $revers ){
                $read[$i] = strrev($read[$i]);
                $i++;
            }
        }
        if( $revers ) $read[$i] .= $c;
        else $read .= $c;
        $offset--;
    }
    fclose ($fp);
    if( $revers ){
        if($read[$i] == "\n" || $read[$i] == "\r")
            array_pop($read);
        else $read[$i] = strrev($read[$i]);
        return implode('',$read);
    }
    return strrev(rtrim($read,"\n\r"));
}
echo "<textarea cols='80' rows='40' id='textarea_id'>".read_backward_line(__DIR__.'/../log.txt',300)."</textarea><script type='application/javascript'>var textarea = document.getElementById('textarea_id');
textarea.scrollTop = textarea.scrollHeight;</script>";
?>