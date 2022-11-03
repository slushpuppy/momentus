<?php
$hook_suffix = "pluginers_settings";

 require (__DIR__.'/_metabox.php');

 $_meta_box_controller = new _metabox($hook_suffix);
$_meta_box_controller->_fill([     new _box('no_content','No Content', function () {
    echo '<label><input name="ignore_nocontent" type="checkbox"';
    if(self::$settings['ignore_nocontent']==1){
        echo ' checked="checked"';
    }
    echo '>Ignore auto publish any content does not have contents body</label>';
},_box::style_blue),
    new _box('ignore_novideos','No Videos', function () {
        echo '<label><input name="ignore_novideos" type="checkbox"';
        if(self::$settings['ignore_novideos']==1){
            echo ' checked="checked"';
        }
        echo '>Ignore auto publish any content does not have videos</label>';
    },_box::style_blue),
    new _box('images_id','Images', function () {
        echo '<label><input name="save_images_locally" type="checkbox" '.((self::$settings['save_images_locally'] == 1)? 'checked="checked"':'').'> Enable option to copy the content images locally in your blog after published</label>
                                                <p class="howto">This option will save any images in the fetched content locally in your server so you should have a server with a suitable space storage</p>';
    },_box::style_blue),
    ],'basic-config-left');

 $_meta_box_controller->_fill([     new _box('ignore_noimages','No Images', function () {
     echo '<label><input name="ignore_noimages" type="checkbox"';
     if(self::$settings['ignore_noimages']==1){
         echo ' checked="checked"';
     }
     echo '>Ignore auto publish any content does not have images</label>';
 },_box::style_blue),
     new _box('original_date','Original Date', function () {
     echo '<label><input name="original_date" type="checkbox"';
     if(self::$settings['original_date']==1){
         echo 'checked="checked"';
     }
     echo '>Publish the content according to its original publish date</label>';
 },_box::style_blue),
     new _box('update_social_counter','Social Tracker', function () {
         echo '<label><input name="update_social_counter" type="checkbox" '.((self::$settings['update_social_counter'] == 1) ? '?checked="checked"' : '').'> Enable system to track the social counters</label>
                                                <p class="howto">Social tracker which track your sources content in the social media and update its stats</p>';

     },_box::style_blue),
     ],'basic-config-right');

$_meta_box_controller->_fill([
    new _box('data_display_pg_id','Data Display', function () {
        echo 'Display <input name="data_per_page" type="number" min="1" size="5" style="width:50px" value="'.self::$settings['data_per_page'].'" aria-required="true"> number of data per page';
    },_box::style_green),
    new _box('autopub_cron_time','Auto Publish', function () {
        echo '<div class="form-required">
Run auto publish cron every <input name="autopub_cron_time" type="text" size="5" style="width:50px" value="'.self::$settings['autopub_cron_time'].'" aria-required="true"> hour
                                                <p class="howto">Cron which will run to publish any pending auto publish content from your sources. 0.5 hours is an optimal setting.</p></div>';
    },_box::style_green),
],'data-config-left');
$_meta_box_controller->_fill([
    new _box('crawlers','Crawlers',function () { echo '                                            <div class="form-required">
                                                Run crawlers every <input name="crawler_cron_time" type="text" size="5" style="width:50px" value="'.self::$settings['crawler_cron_time'].'" aria-required="true"> hour
                                                <p class="howto">Crawler engine which will read and fetch any new content from your sources</p>
                                                <p class="howto">It is recommended that you set big intervals if you have a server with low resources</p>
                                            </div>';},_box::style_green),
],'data-config-right');

$_meta_box_controller->_fill([
    new _box('replace_words','Replace Words',function () {
        echo '<div class="cell-field pluginersReplaceWords">';
        if(!empty(self::$settings['word-replace'])) {
            foreach(self::$settings['word-replace']['search'] as $key => $replace) {
                if (self::$settings['word-replace']['search'][$key] != '')
                {
                    echo '<p class="pluginers-clear">
                                                        <input name="word-replace[search][]" value="' . self::$settings['word-replace']['search'][$key] . '" type="text" size="20" style="float:left;margin:0 5px">
                                                        <input name="word-replace[word][]" value="' . self::$settings['word-replace']['word'][$key] . '" type="text" size="20" style="float:left;margin:0 5px">
                                                        <input type="button" class="button button-primary" onclick="$(this).closest(\'p\').remove();" style="float:left;margin:0 5px" value="Remove">
                                                    </p>';
                }
            }}
        echo '<div class="pluginers-clear">
                                                    <input name="word-replace[search][]" type="text" size="20" placeholder="Search About" style="float:left;margin:0 5px">
                                                    <input name="word-replace[word][]" type="text" size="20" placeholder="Replace With" style="float:left;margin:0 5px">
                                                    <input type="button" class="button button-primary" onclick="pluginersRssAddRow(this)" style="float:left;margin:0 5px" value="Add">
                                                    <input type="button" class="button button-primary" onclick="pluginersRssDelRow(this)" style="float:left" value="Remove">
                                                </div>
                                            </div>';

    },_box::style_green),
    new _box('publish_author_id','Author Username', function () {
        echo '<div class="form-required">
                                                <input name="publish_author_id" type="text" size="20" value="'.self::$settings['publish_author_id'].'" aria-required="true">
                                                <p class="howto">Author username to relate to the auto publishing content</p>
                                            </div>';
    },_box::style_green),
],'post-setting-left');
$_meta_box_controller->_fill([

    new _box('post_type_id','Post Settings', function () {
        echo '<div><select name="publish_post_type"><option value="post">Post</option>';
        $post_types = get_post_types(array('_builtin' => false, 'public' => true));
        foreach ($post_types as $post_type) {
            echo '<option value="'.$post_type.'"';
            if (self::$settings['publish_post_type'] == $post_type) { echo 'selected="selected"'; }
            echo '>';
        }
        echo ' </select></div><p class="howto">Set post type</p>';

        echo '                                            <div>
                                                <select name="publish_post_status">
                                                    <option value="draft">Draft</option>
                                                    <option value="publish" '.((self::$settings['publish_post_status'] == 'publish') ?'selected="selected"':'').'>Published</option>
                                                    <option value="pending" '.((self::$settings['publish_post_status'] == 'pending') ? 'selected="selected"':'').'>Pending Review</option>
                                                </select>
                                            </div><p class="howto">Set post status</p>';
        echo '                                                <div id="taxonomy-category" class="categorydiv" style="max-width:550px">
                                                    <div id="category-all" class="tabs-panel">
                                                        <ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">';
        echo wp_category_checklist(0, 0, explode(',', self::$settings['publish_cats']), false, null, false);
        echo '
                                                        </ul>
                                                    </div>
                                                </div><p class="howto">Set post category(if any)</p>';
    },_box::style_green),
],'post-setting-right');

$_meta_box_controller->_fill([
    new _box('amazon_settings_id','Amazon Settings', function () {
        echo '<div class="flex-container">
                                        <div class="cell">
                                            <div class="first">Access key</div>
                                            <div>
                                                <input name="amazon_access_key" type="text" size="40" value="'.self::$settings['amazon_access_key'].'" aria-required="true" />
                                            </div>
                                        </div>

                                        <div class="cell">
                                            <div class="first">Secret key</div>
                                            <div>
                                                <input name="amazon_secret_key" type="text" size="40" value="'.self::$settings['amazon_secret_key'].'" aria-required="true" />
                                            </div>
                                        </div>

                                        <div class="cell">
                                            <div class="first">Associate Tag</div>
                                            <div>
                                                <input name="amazon_associate_tag" type="text" size="40" value="'.self::$settings['amazon_associate_tag'].'" aria-required="true" />
                                            </div>
                                        </div>
                                    </div>';
    },_box::style_orange),

    new _box('rewrite_ervice_provider','Spin your content', function () {
        echo '<div class="first">Spinner Service Providers</div>
                                            <div>
                                                <select name="rewrite_provider" id="smpubap_rewprov">
                                                    <option value=""></option>
                                                    <option value="wordai"'.((!empty(self::$settings['rewrite_provider']) && self::$settings['rewrite_provider'] == 'wordai')? 'selected="selected"':'').'>WordAi</option>
                                                    <option value="spinrewriter" '.((!empty(self::$settings['rewrite_provider']) && self::$settings['rewrite_provider'] == 'spinrewriter')? 'selected="selected"':'').'>Spin Rewriter</option>
                                                    <option value="chimprewriter" '.((!empty(self::$settings['rewrite_provider']) && self::$settings['rewrite_provider'] == 'chimprewriter')?'selected="selected"':'').'>ChimpRewriter</option>
                                                    <option value="contentprofessor" '.((!empty(self::$settings['rewrite_provider']) && self::$settings['rewrite_provider'] == 'contentprofessor')? 'selected="selected"':'').'>ContentProfessor</option>
                                                    <option value="spinnerchief" '.((!empty(self::$settings['rewrite_provider']) && self::$settings['rewrite_provider'] == 'spinnerchief')?'selected="selected"':'').'>SpinnerChief</option>
                                                </select>&nbsp;<a href="#" target="_blank" id="smpubap_rewprov_link">Click here for visit</a>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_wordai" data-url="http://wordai.com/" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'wordai') ? 'style="display:none"':'').'>
                                            <div class="first">Email</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[wordai][email]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['wordai']['email'])) ?  self::$settings['rewrite_api']['wordai']['email']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_wordai" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'wordai')? 'style="display:none"':'').'>
                                            <div class="first">Password</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[wordai][pass]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['wordai']['pass'])) ?  htmlentities(self::$settings['rewrite_api']['wordai']['pass']) : '').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinrewriter" data-url="https://www.spinrewriter.com/" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinrewriter') ? 'style="display:none"':'').'>
                                            <div class="first">Email</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[spinrewriter][email_address]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['spinrewriter']['email_address']))?self::$settings['rewrite_api']['spinrewriter']['email_address']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinrewriter" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinrewriter') ? 'style="display:none"':'').'>
                                            <div class="first">Password</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[spinrewriter][api_key]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['spinrewriter']['api_key']))?self::$settings['rewrite_api']['spinrewriter']['api_key']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinrewriter" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinrewriter') ? 'style="display:none"':'').'>
                                            <div class="first">Protected Terms</div>
                                            <div>
                                                <textarea name="rewrite_api[spinrewriter][protected_terms]" cols="50" rows="8">'.((!empty(self::$settings['rewrite_api']['spinrewriter']['protected_terms']))? self::$settings['rewrite_api']['spinrewriter']['protected_terms']:'').'</textarea>
                                                <p class="howto">A list of keywords and key phrases that you do NOT want to spin. One term per line .</p>
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_chimprewriter" data-url="" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'chimprewriter') ? 'style="display:none"':'').'>
                                            <div class="first">Email</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[chimprewriter][email]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['chimprewriter']['email']))? self::$settings['rewrite_api']['chimprewriter']['email']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_chimprewriter" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'chimprewriter') ? 'style="display:none"':'').'>
                                            <div class="first">API key</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[chimprewriter][apikey]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['chimprewriter']['apikey']))?self::$settings['rewrite_api']['chimprewriter']['apikey']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_chimprewriter" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'chimprewriter') ? 'style="display:none"':'').'>
                                            <div class="first">Application ID</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[chimprewriter][aid]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['chimprewriter']['aid']))? self::$settings['rewrite_api']['chimprewriter']['aid']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_chimprewriter" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'chimprewriter') ? 'style="display:none"':'').'>
                                            <div class="first">Protected Terms</div>
                                            <div>
                                                <textarea name="rewrite_api[chimprewriter][protectedterms]" cols="50" rows="8">'.((!empty(self::$settings['rewrite_api']['chimprewriter']['protectedterms']))? self::$settings['rewrite_api']['chimprewriter']['protectedterms']:'').'</textarea>
                                                <p class="howto">Comma separated list of words or phrases to protect from spin i.e. ‘my main keyword,my second keyword’ .</p>
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_contentprofessor" data-url="http://www.contentprofessor.com/go.php" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'contentprofessor') ? 'style="display:none"':'').'>
                                            <div class="first">Language</div>
                                            <div class="form-required">
                                                <select name="rewrite_api[contentprofessor][lang]">
                                                    <option value="en" '.((!empty(self::$settings['rewrite_api']['contentprofessor']['lang']) && self::$settings['rewrite_api']['contentprofessor']['lang'] == 'en')? 'selected="selected"':'').'>English</option>
                                                    <option value="es" '.((!empty(self::$settings['rewrite_api']['contentprofessor']['lang']) && self::$settings['rewrite_api']['contentprofessor']['lang'] == 'es')? 'selected="selected"':'').'>Spanish</option>
                                                    <option value="fr" '.((!empty(self::$settings['rewrite_api']['contentprofessor']['lang']) && self::$settings['rewrite_api']['contentprofessor']['lang'] == 'fr')? 'selected="selected"':'').'>French</option>
                                                    <option value="de" '.((!empty(self::$settings['rewrite_api']['contentprofessor']['lang']) && self::$settings['rewrite_api']['contentprofessor']['lang'] == 'de')? 'selected="selected"':'').'>German</option>
                                                    <option value="it" '.((!empty(self::$settings['rewrite_api']['contentprofessor']['lang']) && self::$settings['rewrite_api']['contentprofessor']['lang'] == 'it')? 'selected="selected"':'').'>Italian</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_contentprofessor" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'contentprofessor') ? 'style="display:none"':'').'>
                                            <div class="first">Free Membership</div>
                                            <div>
                                                <label><input name="rewrite_api[contentprofessor][free]" type="checkbox" '.((!empty(self::$settings['rewrite_api']['contentprofessor']['free']) && self::$settings['rewrite_api']['contentprofessor']['free'] == 1) ? 'checked="checked"':'').'> Connect with API server as a free member</label>
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_contentprofessor" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'contentprofessor') ? 'style="display:none"':'').'>
                                            <div class="first">Username</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[contentprofessor][login]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['contentprofessor']['login']))?self::$settings['rewrite_api']['contentprofessor']['login']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_contentprofessor" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'contentprofessor') ? 'style="display:none"':'').'>
                                            <div class="first">Password</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[contentprofessor][password]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['contentprofessor']['password']))?self::$settings['rewrite_api']['contentprofessor']['password']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinnerchief" data-url="https://www.whitehatbox.com/" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinnerchief') ? 'style="display:none"':'').'>
                                            <div class="first">Language</div>
                                            <div class="form-required">
                                                <select name="rewrite_api[spinnerchief][lang]" aria-required="true">
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'English')? 'selected="selected"':'').'>English</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Arabic')? 'selected="selected"':'').'>Arabic</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Belarusian')? 'selected="selected"':'').'>Belarusian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Bulgarian')? 'selected="selected"':'').'>Bulgarian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Croatian')? 'selected="selected"':'').'>Croatian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Danish')? 'selected="selected"':'').'>Danish</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Dutch')? 'selected="selected"':'').'>Dutch</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Filipino')? 'selected="selected"':'').'>Filipino</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Finnish')? 'selected="selected"':'').'>Finnish</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'French')? 'selected="selected"':'').'>French</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'German')? 'selected="selected"':'').'>German</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Greek')? 'selected="selected"':'').'>Greek</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Hebrew')? 'selected="selected"':'').'>Hebrew</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Indonesian')? 'selected="selected"':'').'>Indonesian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Italian')? 'selected="selected"':'').'>Italian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Lithuanian')? 'selected="selected"':'').'>Lithuanian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Norwegian')? 'selected="selected"':'').'>Norwegian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Polish')? 'selected="selected"':'').'>Polish</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Portuguese')? 'selected="selected"':'').'>Portuguese</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Romanian')? 'selected="selected"':'').'>Romanian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Slovak')? 'selected="selected"':'').'>Slovak</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Slovenian')? 'selected="selected"':'').'>Slovenian</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Spanish')? 'selected="selected"':'').'>Spanish</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Swedish')? 'selected="selected"':'').'>Swedish</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Turkish')? 'selected="selected"':'').'>Turkish</option>
                                                    <option '.((!empty(self::$settings['rewrite_api']['spinnerchief']['lang']) && self::$settings['rewrite_api']['spinnerchief']['lang'] == 'Vietnamese')? 'selected="selected"':'').'>Vietnamese</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinnerchief" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinnerchief') ? 'style="display:none"':'').'>
                                            <div class="first">Username</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[spinnerchief][username]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['spinnerchief']['username']))?self::$settings['rewrite_api']['spinnerchief']['username']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinnerchief" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinnerchief') ? 'style="display:none"':'').'>
                                            <div class="first">Password</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[spinnerchief][password]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['spinnerchief']['password']))?self::$settings['rewrite_api']['spinnerchief']['password']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinnerchief" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinnerchief') ? 'style="display:none"':'').'>
                                            <div class="first">API Key</div>
                                            <div class="form-required">
                                                <input aria-required="true" name="rewrite_api[spinnerchief][apikey]" type="text" size="40" value="'.((!empty(self::$settings['rewrite_api']['spinnerchief']['apikey']))?self::$settings['rewrite_api']['spinnerchief']['apikey']:'').'" />
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinnerchief" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinnerchief') ? 'style="display:none"':'').'>
                                            <div class="first">Protected Tags</div>
                                            <div>
                                                <input name="rewrite_api[spinnerchief][tagprotect]" type="text" size="40" value="'.((empty(self::$settings['rewrite_api']['spinnerchief']['tagprotect']))? htmlspecialchars('"",<code></code>,<pre></pre>,``,\'\',<blockquote></blockquote>'): htmlspecialchars(self::$settings['rewrite_api']['spinnerchief']['tagprotect'])).'" />
                                                <p class="howto">This will protect the text between tags. i.e. [],(),<- -> , it will protect the text between [ and ], ( and ), <- and -> .</p>
                                            </div>
                                        </div>
                                        <div valign="top" class="smpubap_rewprov_options smpubap_rewprov_spinnerchief" '.((empty(self::$settings['rewrite_provider']) || self::$settings['rewrite_provider'] != 'spinnerchief') ? 'style="display:none"':'').'>
                                            <div class="first">Protected Terms</div>
                                            <div>
                                                <textarea name="rewrite_api[spinnerchief][protectwords]" cols="50" rows="8">'.((!empty(self::$settings['rewrite_api']['spinnerchief']['protectwords']))?self::$settings['rewrite_api']['spinnerchief']['protectwords']:'').'</textarea>
                                                <p class="howto">Comma separated list of words or phrases to protect from spin i.e. ‘word1,word2,word3,phrase1,phrase2’ .</p>
                                            </div>
                                        </div> <p class="howto">To spin your content, you need to purchase and connect to one of the spinner content provider. This is an optional setting.</p>';
    },_box::style_orange),

],'integration-left');
$_meta_box_controller->_fill([
    new _box('fb_access_token_id','Facebook Access Token', function () {
        echo '<input name="fb_access_token" type="text" size="40" value="'.self::$settings['fb_access_token'].'">
                                                <p class="howto">It is required to get Facebook stats for social tracker feature. Get it from this <a href="https://developers.facebook.com/tools/accesstoken/" target="_blank">page</a></p>';
    },_box::style_orange),

    new _box('google_api_key','Google API Key', function () {
        echo '<input name="google_keys[0]" type="text" size="40" value="'.self::$settings['google_keys'][0].'" aria-required="true" />
                                                <p class="howto">Set your own Google API key to avoid the queries limitations. For how <a href="https://youtu.be/xEVrgnvnA2M" target="_blank">click here</a></p><p class="howto">Also watch our training video to learn how to apply for your own Google API key</p>';
    },_box::style_orange),
    new _box('','Alternative Keys', function () {
        echo '                                       <div>
                                                <input name="google_keys[1]" type="text" size="40" value="'.self::$settings['google_keys'][1].'" /><br />
                                                <input name="google_keys[2]" type="text" size="40" value="'.self::$settings['google_keys'][2].'" /><br />
                                                <input name="google_keys[3]" type="text" size="40" value="'.self::$settings['google_keys'][3].'" /><br />
                                                <input name="google_keys[4]" type="text" size="40" value="'.self::$settings['google_keys'][4].'" />
                                                <p class="howto">Alternative Google API keys for avoiding Google\'s limitation .</p>
                                            </div>';
    },_box::style_orange),



        ],'integration-right');


 $_meta_box_controller->fillLeft([





/*
     new _box('','Keep Archive', function () {
        echo '                                            <div class="form-required">
                                                Delete the content from archive which exceeds <input name="keep_archive" type="number" min="1" size="5" style="width:50px" value="'.self::$settings['keep_archive'].'" aria-required="true"> day
                                                <p class="howto">If you want to keep your content for long times you should provide a good server resources to deal with this big database</p>
                                            </div>';
     }),
     new _box('','Copyright String', function () {
        echo '                                            <div class="form-required">
                                                <input name="copyrights_string" type="text" size="40" value="'.self::$settings['copyrights_string'].'" aria-required="true">
                                                <p class="howto">The copyrights message which will appear after any fetched content in case you enable this option</p>
                                            </div>';
     }),*/
     /*new _box('publish_tags','Tags', function () {
         echo '                                        <div>
                                                <input id="pluginers_tags_autocomplete" name="publish_tags" type="text" size="40" value="'.self::$settings['publish_tags'].'">
                                                <p class="howto">Separate tags with commas</p>
                                            </div>';
     }),*/


 ]);


$_meta_box_controller->fillRight([








]);
//do_action( 'add_meta_boxes', $hook_suffix );



    ?>

    <div class="wrap">

        <h2><?php echo get_admin_page_title(); ?></h2>


        <div class="fx-settings-meta-box-wrap">

            <form action="<?php echo $pageurl; ?>" method="post" id="pluginers_jform" class="validate">

            <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

                <div id="poststuff">

                    <div id="plugineer-settings-page" class="metabox-holder columns-2">


                        <h2 class="accordion active" style="clear:both;">Konfigurasi Asas</h2>
                            <div class="accordion-panel" style="display:block;">
                                <div id="postbox-container-basic-config-left" class="postbox-container">

                                    <?php do_meta_boxes( $hook_suffix, 'basic-config-left', null ); ?>
                                    <!-- #side-sortables -->

                                </div><!-- #postbox-container-1 -->

                                <div id="postbox-container-basic-config-right" class="postbox-container">

                                    <?php do_meta_boxes( $hook_suffix, 'basic-config-right', null ); ?>
                                    <!-- #normal-sortables -->
                                    <!-- #advanced-sortables -->

                                </div><!-- #postbox-container-2 -->
                            </div>




                       <h2 class="accordion active" style="clear:both;">Konfigurasi Data</h2>

                        <div class="accordion-panel" style="display:block;">
                            <div id="postbox-container-data-config-left" class="postbox-container">

                                <?php do_meta_boxes( $hook_suffix, 'data-config-left', null ); ?>
                                <!-- #side-sortables -->

                            </div><!-- #postbox-container-1 -->

                            <div id="postbox-container-data-config-right" class="postbox-container">

                                <?php do_meta_boxes( $hook_suffix, 'data-config-right', null ); ?>
                                <!-- #normal-sortables -->
                                <!-- #advanced-sortables -->

                            </div><!-- #postbox-container-2 -->
                        </div>

                       <h2 class="accordion active" style="clear:both;">Tetapan Post</h2>

                        <div class="accordion-panel" style="display:block;">
                            <div id="postbox-container-post-setting-left" class="postbox-container">

                                <?php do_meta_boxes( $hook_suffix, 'post-setting-left', null ); ?>
                                <!-- #side-sortables -->

                            </div><!-- #postbox-container-1 -->

                            <div id="postbox-container-post-setting-right" class="postbox-container">

                                <?php do_meta_boxes( $hook_suffix, 'post-setting-right', null ); ?>
                                <!-- #normal-sortables -->
                                <!-- #advanced-sortables -->

                            </div><!-- #postbox-container-2 -->
                        </div>

                       <h2 class="accordion active" style="clear:both;">Integrasi</h2>

                        <div class="accordion-panel" style="display:block;">
                            <div id="postbox-container-basic-integration-left" class="postbox-container">

                                <?php do_meta_boxes( $hook_suffix, 'integration-left', null ); ?>
                                <!-- #side-sortables -->

                            </div><!-- #postbox-container-1 -->

                            <div id="postbox-container-basic-integration-right" class="postbox-container">

                                <?php do_meta_boxes( $hook_suffix, 'integration-right', null ); ?>
                                <!-- #normal-sortables -->
                                <!-- #advanced-sortables -->

                            </div><!-- #postbox-container-2 -->
                        </div>

                    </div><!-- #post-body -->
                    <div><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="Update Settings">
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
</script>