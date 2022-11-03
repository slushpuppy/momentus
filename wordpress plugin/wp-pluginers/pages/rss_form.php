<?php
$hook_suffix = "pluginers_settings";

require (__DIR__.'/_metabox.php');

$_meta_box_controller = new _metabox($hook_suffix);

if($source['type'] != 'query' || true )
{
    /*$_meta_box_controller->fillRight([new _box('reading_engine_id', 'Reading engine', function () use ($cats, $source)
    {
        echo '<select name="engineid" aria-required="true" style="display: none">
                <option value="1" selected="selected">Engine #1</option>
<option value="2" >Engine #2</option>
</select>
<p class="howto" style="display: none">Every engine has its own algorithm to parse the feeds so just choose the engine that will prepare your contect successfully.</p>';
    }),*/


}

if($source['type'] != 'rss' || true) {
        $_meta_box_controller->fillLeft([


        /*new _box('query_field_id', 'RSS Query Field', function () use ($cats, $source)
        {
            echo                             '

<select name="field">
                                    <option value="1" '.(($source['field'] == '1')?'selected="selected"':'').'>All Web</option>
<option value="2" '.(($source['field'] == '2')?'selected="selected"':'').'>Article</option>
<option value="3" '.(($source['field'] == '3')?'selected="selected"':'').'>Blogs</option>
<option value="4" '.(($source['field'] == '4')?'selected="selected"':'').'>Videos</option>
<option value="5" '.(($source['field'] == '5')?'selected="selected"':'').'>News</option>
<option value="6" '.(($source['field'] == '6')?'selected="selected"':'').'>Events</option>
<option value="7" '.(($source['field'] == '7')?'selected="selected"':'').'>Organization</option>
<option value="8" '.(($source['field'] == '8')?'selected="selected"':'').'>Business</option>
<option value="9" '.(($source['field'] == '9')?'selected="selected"':'').'>Stores</option>
<option value="10" '.(($source['field'] == '10')?'selected="selected"':'').'>Offers</option>
</select>';
        }),*/

        ]);
}


$_meta_box_controller->_fill([
    new _box('status_publisher_id','Feed Status', function ()  use ($cats,$source)  {
        echo '<input name="status" type="checkbox" class="checkonoff" value="'.$source['status'].'" '.(($source['status']) ? 'checked="checked"':'').'style="float:left"><p class="howto">&nbsp;Select to turn feed on/off</p>';


    },_box::style_red),
    new _box('category_id','Campaign Type',function () use ($cats,$source){ echo '<select name="catid"><option value="0">No Campaign Type</option>';
        //error_log(print_r($source,TRUE),0);
        if (is_array($cats))
            foreach ($cats as $cat) {
                echo '<option value="'.$cat->term_id.'" '.(($source['catid'] == $cat->term_id) ? 'selected="selected"':'').'>'.$cat->name.'</option>';
            }
        echo '</select>';
    },_box::style_green),

    new _box('langauge_id', 'Language', function () use ($cats, $source)
    {
        echo  '<input name="jslang" type="text" size="30" value="'.$source['lang'].'" class="pluginers_lang_autocomplete" placeholder="Search in a certain language">
                                <input name="lang" type="hidden" value="'.$source['lang'].'">
                                <p class="howto">Type in name of language you want to restrict results to(only works for some sites)</p>
                                '                           ;
    },_box::style_green),
    new _box('contry_id', 'Country', function () use ($cats, $source)
    {
        echo '<input name="jscountry" type="text" size="30" value="'.$source['country'].'" class="pluginers_country_autocomplete" placeholder="Restrict results related to country">
                                <input name="country" type="hidden" value="'.$source['country'].'">
                                 <p class="howto">Type in name of country you want to restrict results to(only works for some sites)</p>';
    },_box::style_green),


],'campaign-setup-left');
$_meta_box_controller->_fill([
    new _box('name_id','Campaign Name', function ()  use ($cats,$source) {
        echo '<input name="name" type="text" size="40" value="'.$source['name'].'" aria-required="true">';
    },_box::style_green),
    new _box('feed_type_id','Choose feed type', function ()  use ($cats,$source)  {
        echo '<select name="type" class="pluginers_toggle" id="feed_type_select_id">
                                    <option value="rss">RSS feed</option>
                                    <option value="query" ';
        if($source['type'] == 'query'){
            echo ' selected="selected"';
        }
        echo '>Internet Search</option>
                                </select>';


    },_box::style_green),
            new _box('rss_feed_link_id', 'Feed Link', function () use ($cats, $source)
        {
            echo '<input name="rsslink" type="url" size="40" value="'.$source['rsslink'].'" aria-required="true">
            <select name="engineid" aria-required="true" style="display: none">
                <option value="1" selected="selected">Engine #1</option>
<option value="2" >Engine #2</option>
</select>
           <p class="howto">Enter RSS URL of the page you want to get feed data from</p> 
            ';
        },_box::style_green),

    new _box('search_query_id', 'Web search Query', function () use ($cats, $source)
    {
        echo '<input name="query" type="text" size="40" value="'.$source['query'].'" aria-required="true" placeholder="Search using any query, keyword or domain">
                                <p class="howto">Use the same tricks when you searching using Google search input box to filter the results.</p>';
    },_box::style_green),

    new _box('date_range_id', 'Cut off date', function () use ($cats, $source)
    {
        echo '<select name="date_range">
                                    <option value="d1"  '.(($source['date_range'] == 'd1')?'selected="selected"':'').'>Past Day</option>
<option value="w1"  '.(($source['date_range'] == 'w1')?'selected="selected"':'').'>Past Week</option>
<option value="m1"  '.(($source['date_range'] == 'm1')?'selected="selected"':'').'>Past Month</option>
<option value="m6"  '.(($source['date_range'] == 'm6')?'selected="selected"':'').'>Past 6 Month</option>
<option value="y1"  '.(($source['date_range'] == 'y1')?'selected="selected"':'').'>Past Year</option>
<option value="y2"  '.(($source['date_range'] == 'y2')?'selected="selected"':'').'>Past 2 Years</option>
<option value="y3"  '.(($source['date_range'] == 'y3')?'selected="selected"':'').'>Past 3 Years</option>
</select>';
    },_box::style_green),
],'campaign-setup-right');

    $_meta_box_controller->_fill([
        new _box('grabber_ai_id','Grabber AI', function ()  use ($cats,$source)  {
            echo '<label><input name="smart_grabber_ai" type="checkbox" '.(($source['smart_grabber_ai']==1) ? 'checked="checked"':'').'> Smartly removes unimportant parties</label>
<p class="howto">System tries to recognize on unimportant parties of article and remove them so disable this option if you find system remove them by wrong.</p>';


        },_box::style_blue),
        new _box('title_rewrite','Title Rewrite', function ()  use ($cats,$source)  {
            echo             '<label><input name="rewrite_title" type="checkbox" '.(($source['rewrite_title']==1)? 'checked="checked"':'').'> Rewrite the article title</label>
<p class="howto">Automatically rewrites article title to be friendly with search engines .</p>';


        },_box::style_blue),
        new _box('featured_image_id','Featured Image', function ()  use ($cats,$source)  {
            echo '<label><input name="featured_image" type="checkbox" '.(($source['featured_image']==1)?'checked="checked"':'').'> Set first image in the content as the post featured image</label>';


        },_box::style_blue),
    ],'content-grabber-left');

    $_meta_box_controller->_fill([
        new _box('smart_content_grabber_id','Smart Content Grabber', function ()  use ($cats,$source)  {
            echo '<label><input name="smart_grabber" type="checkbox" '.(($source['smart_grabber']==1)?'checked="checked"':'').' > Grab the items content smartly !</label>
<p class="howto">If source does not provide the full readability content a smart algorithm will analyze every source item and get a full readability content for each one !</p>';


        },_box::style_blue),

        new _box('article_rewrite','Article Rewrite', function ()  use ($cats,$source)  {
            echo                             '<label><input name="rewrite" type="checkbox" '.(($source['rewrite']==1)? 'checked="checked"':'').'> Rewrite the article content</label>
<p class="howto">Automatically rewrites entire sentences and paragraphs to be friendly with search engines .</p>';


        },_box::style_blue),

    ],'content-grabber-right');

    $_meta_box_controller->_fill([

        new _box('remove_hyperlink','Remove Hyperlinks', function ()  use ($cats,$source)  {
            echo     '<label><input name="remove_links" type="checkbox" '.(($source['remove_links']==1)? 'checked="checked"':'').'> Remove any hyperlinks from the article content</label>';


        },_box::style_blue),
        new _box('allowable_tag_id','Allowable Tags', function ()  use ($cats,$source)  {
            echo '<input name="grabber_allow_tags" type="text" size=40 value=" '.$source['grabber_allow_tags'].'">';


        },_box::style_blue),
    ],'post-settings-left');
    $_meta_box_controller->_fill([
        new _box('copyrights_id','Copyrights', function ()  use ($cats,$source)  {
            echo '<label><input name="copyrights" type="checkbox" '.(($source['copyrights']==1)?'checked="checked"':'').'> Activate the mention of copyrights in the bottom of fetched content</label>';


        },_box::style_blue),

        new _box('social_tracker_id','Social Tracker', function ()  use ($cats,$source)  {
            echo '<label><input name="social_tracker" type="checkbox" '.(($source['social_tracker']==1)?'checked="checked"':'').'> Track the source content in the social media and save its stats periodically</label>'
            ;


        },_box::style_blue),
    ],'post-settings-right');

    $_meta_box_controller->_fill([

        new _box('alternative_contents','Alternative Contents', function ()  use ($cats,$source)  {
            echo '<select name="alt_content">
    <option value="">Default tag (content)</option>
    <option value="title" '.(($source['alt_content'] == 'title')? 'selected="selected"':'').'>Title</option>
    <option value="link" '.(($source['alt_content'] == 'link')? 'selected="selected"':'').'>Link</option>
    <option value="media" '.(($source['alt_content'] == 'media')? 'selected="selected"':'').'>Media</option>
</select>
<p class="howto">Determine a special RSS tag to set the content using it .</p>';


        },_box::style_orange),

        new _box('restrictive_search_id','Restrict Search', function ()  use ($cats,$source)  {
            echo  '<select name="grabber_container">
    <option value="">Container type</option>
    <option value="div" '.(($source['grabber_container'] == 'div')? 'selected="selected"':'').'>DIV</option>
    <option value="section" '.(($source['grabber_container'] == 'section')? 'selected="selected"':'').'>SECTION</option>
    <option value="p" '.(($source['grabber_container'] == 'p')? 'selected="selected"':'').'>P</option>
    <option value="span" '.(($source['grabber_container'] == 'span')? 'selected="selected"':'').'>SPAN</option>
</select>
<select name="grabber_element_type">
    <option value="">Container element type</option>
    <option value="class" '.(($source['grabber_element_type'] == 'class')? 'selected="selected"':'').'>Class</option>
    <option value="id" '.(($source['grabber_element_type'] == 'id')? 'selected="selected"':'').'>ID</option>
</select>
<input name="grabber_element_name" type="text" size="20" placeholder="Class name or ID value" value="'.$source['grabber_element_name'].'">
<p class="howto">Restrict grabber algorithm to search into a determined page part by providing a <code>DIV</code> className or ID value</p>';


        },_box::style_orange),
    ],'advance-settings-left');

$_meta_box_controller->_fill([
        new _box('snippet_content_id','Snippet Content', function ()  use ($cats,$source)  {
            echo   '<input name="truncate_limit" type="number" size="25" value="'.$source['truncate_limit'].'" placeholder="Number of words">
<input name="truncate_replace" type="text" size="25" value="'.$source['truncate_replace'].'" placeholder="End of snippet e.g. (...)">
<p class="howto">Remove any HTML chars then return a snippet text in the number of words that you selected .</p>';


        },_box::style_orange),
    ],'advance-settings-right');


    $_meta_box_controller->fillLeft([





    /*new _box('autopublish_id','Auto Publish', function ()  use ($cats,$source)  {
        echo '<label><input name="autopublish" type="checkbox"  '.(($source['autopublish']==1)?'checked="checked"':'').'> Auto publish any new content from this source</label>';


    }),*/





]);

$_meta_box_controller->_fill( [
new _box('ignore_default_id','Ignore Default', function ()  use ($cats,$source)  {
echo                                 '<label><input name="ignore_public" type="checkbox" '.(($source['ignore_public']==1)? 'checked="checked"':'').'> Ignore the default publish settings when publish any content for this source</label>'
;


},_box::style_green),
new _box('fire_publisher','Fire Publisher', function ()  use ($cats,$source)  {
echo                                 'Auto publish any content its total rank exceeds <input name="post_pubfire" type="number" size="5" step="1000" style="width:80px" value="'.$source['post_pubfire'].'"> value'
;


},_box::style_green),
new _box('post_template','Post Template', function ()  use ($cats,$source)  {
echo                                 '<input name="template_id" type="number" size="20" placeholder="Post ID" value="'.$source['template_id'].'">
<p class="howto">Set a post ID to use it as a template and copy all of its attributes</p>';


},_box::style_green),
new _box('post_status_p','Post Status', function ()  use ($cats,$source)  {
echo  '<select name="post_status" onchange="if(this.value == \'schedule\'){$(\'#smpubapScheduleTime\').show();}else{$(\'#smpubapScheduleTime\').hide();}">
    <option value="draft">Draft</option>
    <option value="publish" '.(($source['post_status'] == 'publish') ? 'selected="selected"':'').'>Published</option>
    <option value="pending" '.(($source['post_status'] == 'pending') ? 'selected="selected"':'').'>Pending Review</option>
    <option value="schedule" '.(($source['post_status'] == 'schedule') ? 'selected="selected"':'').'>Schedule</option>
</select>
<div id="smpubapScheduleTime" '.(($source['post_status'] != 'schedule') ? 'style="display:none"':'').'>
    <input type="number" name="schedule_time[day]" max="31" value="'.((!empty($source['schedule_time']['day'])) ? ['schedule_time']['day']: '').'="Day" />
    <input type="number" name="schedule_time[month]" max="12" value="'.((!empty($source['schedule_time']['month'])) ? ['schedule_time']['month']: '').'="Mon" />
    <input type="number" name="schedule_time[year]" value="'.((!empty($source['schedule_time']['year'])) ? ['schedule_time']['year']: '').'="Year" />
    <input type="number" name="schedule_time[hour]" max="23" value="'.((!empty($source['schedule_time']['hour'])) ? ['schedule_time']['hour']: '').'="Hour" />
    <input type="number" name="schedule_time[minute]" max="59" value="'.((!empty($source['schedule_time']['minute'])) ? ['schedule_time']['minute']: '').'="Min" />
    <br class="clear">
    <p class="howto">Leaving any field empty will set the value of NOW() instead it .</p></div>';


},_box::style_green),
    new _box('post_type_p','Post type', function ()  use ($cats,$source)  {
echo                                 '<select name="post_custom_type" onchange="smpubapPostType(this)">
        <option value=""></option>
        <option value="post" '.(($source['post_type'] == 'post')?'selected="selected"':'').'>Post</option>';

        $post_types = get_post_types(array('_builtin' => false, 'public' => true));
        foreach ($post_types as $post_type) {
            echo ' <option value="'.$post_type.'" '.(($source['post_type'] == $post_type)?'selected="selected"':'').'>'.$post_type.'</option>';

        }
   echo '</select>
    <select name="post_custom_type_cat" id="smpubapPostTaxSelc" onchange="smpubapPostTax(this)">
        <option value=""></option>';
        $taxonomy_objects = get_object_taxonomies($source['post_type'], 'objects');
        foreach ($taxonomy_objects as $type => $object) {
        echo '<option value="'.$type.'" '.(($source['post_type_tax'] == $type)?'selected="selected"':'').'>'.$type.'</option>';
        }

        echo '</select>';


},_box::style_green),
    new _box('categories_id','Post Categories', function ()  use ($cats,$source)  {
    echo  '<div id="taxonomy-category" class="categorydiv">
        <div id="category-all" class="tabs-panel">
            <ul id="categorychecklist" data-wp-lists="list:category" class="smpubapPostTaxDIV categorychecklist form-no-clear" style="  list-style-type: none;">
                '.wp_terms_checklist(0, array('selected_cats' => explode(',', $source['category']),'taxonomy' => $source['post_type_tax'])).'
            </ul>
        </div>
            <p class="howto">Create and manage your post categories by clicking this <a href="edit-tags.php?taxonomy=category">link</a></p>
    </div>

    ';


    },_box::style_green),

],'publish-left');

if ($has_amazon) {

    $_meta_box_controller->_fill( [   new _box('amazon_id','Amazon', function ()  use ($cats,$source)  {
        echo                                 '
    <div  style="display:inline-block; text-align:left">
        <div><input name="add_amazon_products_position" type="radio" '.(($source['add_amazon_products_position']=="before")? 'checked="checked"':'').' value="before"> Before</div>
        <div><input name="add_amazon_products_position" type="radio" '.(($source['add_amazon_products_position']=="inbetween")? 'checked="checked"':'').' value="inbetween"> In between</div>
        <div><input name="add_amazon_products_position" type="radio" '.(($source['add_amazon_products_position']=="end")? 'checked="checked"':'').' value="end"> End</div>
    </div>
    <div style="float:left; display:inline-block;">

        <div class="cell-field">
            <label><input name="add_amazon_products" type="checkbox" '.(($source['add_amazon_products']==1)? 'checked="checked"':'').'> Add Amazon Products</label>
        </div>
    </div>
    <div>
        <textarea name="add_amazon_tags" rows="4" cols="40">'.$source['add_amazon_tag'].'</textarea>
        <p class="howto">Add product keywords on each line</p>
    </div>';


    },_box::style_green)],'publish-left');

}




$_meta_box_controller->_fill( [
    new _box('tags_ee_id','Tags', function ()  use ($cats,$source)  {
        echo  '<input id="pluginers_tags_autocomplete" name="tags" type="text" size="40" value="'.$source['tags'].'">
    <p class="howto">Separate tags with commas</p>';


    },_box::style_green),
    new _box('author_username','Author Username', function ()  use ($cats,$source)  {
        echo  '<input name="author_id" type="text" size="30" value="'.$source['author_id'].'">
    <p class="howto">Author username to relate to the auto publishing content</p>';


    },_box::style_green),    new _box('content_wrap_before','Content wrap(Before)', function ()  use ($cats,$source)  {
        echo    '<textarea name="before_html" rows="4" cols="40">'.$source['before_html'].'</textarea>
    <p class="howto">Html before content</p>';


    },_box::style_green),
    new _box('content_wrap_after','Content wrap(After)', function ()  use ($cats,$source)  {
        echo  '<textarea name="after_html" rows="4" cols="40">'.$source['after_html'].'</textarea>
    <p class="howto">Html after content</p>';


    },_box::style_green),
   /* new _box('current_featured_img','Current Featured Image', function ()  use ($cats,$source)  {
        if ($source['featured_image']) {
            echo "<img src='".$source['featured_image']."' style='max-width: 100%;height:auto;'>";
        }
        echo '
    <input type="file" name="featured_image" accept="image/*">
    <p class="howto">Featured image content thumbnail</p>';


    },_box::style_green),*/
],'publish-right');

/*
 *


                        <div class="cell">
                            <div class="title"></div>
                            <div class="cell-field">

                            </div>
                        </div>

                        <?php if ($has_amazon) {?>
                            <div class="cell">


                            </div>
                        <?php } ?>
 *
 * */




//do_action( 'add_meta_boxes', $hook_suffix );



?>

<div class="wrap">


    <div class="fx-settings-meta-box-wrap">

        <form action="<?php echo $pageurl;?>&noheader=1" method="post" id="pluginers_jform" class="validate">
            <input type="hidden" name="id" value="<?php echo $source['id'];?>">
            <input name="field" type="hidden" value="1">

           <input name="autopublish" type="checkbox"  checked="checked" style="display:none">
            <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

            <div id="poststuff">

                <div class="metabox-holder plugineer-columns-2">


               <h2 style="clear:both;">Publish settings</h2>

                <div class="metabox-holder plugineer-columns-2">




                   <h2 class="accordion active" style="clear:both;">Campaign Setup</h2>
                    <div class="accordion-panel" style="display:block;">

                        <div id="postbox-container-campaign-setup-left" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'campaign-setup-left', null ); ?>
                            <!-- #side-sortables -->

                        </div><!-- #postbox-container-1 -->

                        <div id="postbox-container-campaign-setup-right" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'campaign-setup-right', null ); ?>
                            <!-- #normal-sortables -->
                            <!-- #advanced-sortables -->

                        </div><!-- #postbox-container-2 -->
                    </div>

                   <h2 class="accordion active" style="clear:both;">Publish Settings</h2>

                    <div class="accordion-panel" style="display:block;">

                        <div id="postbox-container-publish-left" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'publish-left', null ); ?>
                            <!-- #side-sortables -->

                        </div><!-- #postbox-container-1 -->

                        <div id="postbox-container-publish-right" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'publish-right', null ); ?>
                            <!-- #normal-sortables -->
                            <!-- #advanced-sortables -->

                        </div><!-- #postbox-container-2 -->
                    </div>


                   <h2 class="accordion active" style="clear:both;">Content Grabber</h2>

                    <div class="accordion-panel" style="display:block;">

                        <div id="postbox-container-content-grabber-left" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'content-grabber-left', null ); ?>
                            <!-- #side-sortables -->

                        </div><!-- #postbox-container-1 -->

                        <div id="postbox-container-content-grabber-right" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'content-grabber-right', null ); ?>
                            <!-- #normal-sortables -->
                            <!-- #advanced-sortables -->

                        </div><!-- #postbox-container-2 -->
                    </div>


                   <h2 class="accordion active" style="clear:both;">Post Settings</h2>

                    <div class="accordion-panel" style="display:block;">

                        <div id="postbox-container-post-settings-left" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'post-settings-left', null ); ?>
                            <!-- #side-sortables -->

                        </div><!-- #postbox-container-1 -->

                        <div id="postbox-container-post-settings-right" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'post-settings-right', null ); ?>
                            <!-- #normal-sortables -->
                            <!-- #advanced-sortables -->

                        </div><!-- #postbox-container-2 -->
                    </div>


                   <h2 class="accordion active" style="clear:both;">Advance Settings</h2>

                    <div class="accordion-panel" style="display:block;">

                        <div id="postbox-container-advance-settings-left" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'advance-settings-left', null ); ?>
                            <!-- #side-sortables -->

                        </div><!-- #postbox-container-1 -->

                        <div id="postbox-container-advance-settings-right" class="postbox-container">

                            <?php do_meta_boxes( $hook_suffix, 'advance-settings-right', null ); ?>
                            <!-- #normal-sortables -->
                            <!-- #advanced-sortables -->

                        </div><!-- #postbox-container-2 -->
                    </div>
                </div><!-- #post-body -->


                <div><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="<?php if (isset($source['id']) && \intval($source['id']) > 0) { echo 'Update Campaign'; } else { echo 'Create Campaign';} ?>">
                    <img src="<?php echo pluginers_imgpath; ?>/wpspin_light.gif" class="pluginers_process" alt="" /></div>
                <br class="clear">

            </div><!-- #poststuff -->

        </form>

    </div><!-- .fx-settings-meta-box-wrap -->

</div><!-- .wrap -->

<script>jQuery(document).ready(function(){ postboxes.add_postbox_toggles(pagenow); });

    jQuery(document).ready( function($) {
        $('.meta-box-sortables').sortable({
            disabled: true
        });

        $('.postbox .hndle').css('cursor', 'pointer');
    });
    $('#feed_type_select_id').change(function() {
        updateBoxes();
    });
    function updateBoxes() {
        var type = $('#feed_type_select_id').val();

        if (type=='query') {
            $("#rss_feed_link_id,#rss_query_id,#query_field_id").hide(1000);
            $("#search_query_id").show(1000);
        } else if (type=='rss') {

            $("#rss_feed_link_id,#rss_query_id,#query_field_id").show(1000);
            $("#search_query_id").hide(1000);
        }

    }
    updateBoxes();
</script>