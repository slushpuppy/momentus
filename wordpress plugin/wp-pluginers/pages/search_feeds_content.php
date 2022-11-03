<div class="wrap">
    <div id="pluginers-icon-logo" class="icon32"><br></div>
    <h2><?php echo get_admin_page_title(); ?></h2>
    <div id="col-container">
        <div id="col-left" style="width: 100%">
            <form action="<?php echo $pageurl; ?>" method="get">
                <input type="hidden" name="page" value="<?php echo $pagname; ?>" />
                <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" />
                <div class="col-wrap">
                    <div class="tablenav top" style="height:auto">
                        <p class="search-box pluginers-canhide">
                            <label class="screen-reader-text">Search Posts:</label>
                            <input type="search" name="query" value="<?php echo (!empty($_GET['query'])) ? $_GET['query'] : ''; ?>">
                            <input type="submit" id="search-submit" class="button" value="Search">
                        </p>
                        <div class="alignleft actions bulkactions pluginers-canhide">
                            <select name="doaction">
                                <option value="0">Bulk Actions</option>
                                <option value="publish">Publish Now</option>
                                <option value="delete">Delete</option>
                            </select>
                            <input type="submit" name="apply" class="button action" value="Apply">
                        </div>
                        <br class="clear">
                        <div class="alignleft actions pluginers-canhide">
                            <select name="source">
                                <option value="">Filter by source</option>
                                <?php foreach($sources as $source):?>
                                    <option value="<?php echo $source->id; ?>" <?php if (!empty($_GET['source']) && $_GET['source'] == $source->id) echo 'selected="selected"'; ?>><?php echo $source->name; ?></option>
                                <?php endforeach;?>
                            </select>
                            <select name="category">
                                <option value="">Filter by category</option>
                                <?php foreach($cats as $cat):?>
                                    <option value="<?php echo $cat->term_id; ?>" <?php if (!empty($_GET['category']) && $_GET['category'] == $cat->term_id) echo 'selected="selected"'; ?>><?php echo $cat->name; ?></option>
                                <?php endforeach;?>
                            </select>
                            <select name="publish_status">
                                <option value="">Filter by publish status</option>
                                <option value="not_published" <?php if (!empty($_GET['publish_status']) && $_GET['publish_status'] == 'not_published') echo 'selected="selected"'; ?>>Not published yet</option>
                                <option value="published" <?php if (!empty($_GET['publish_status']) && $_GET['publish_status'] == 'published') echo 'selected="selected"'; ?>>Published</option>
                            </select>
                            <input type="text" name="date" placeholder="Filter by date range" value="<?php echo (!empty($_GET['date'])) ? $_GET['date'] : ''; ?>" class="pluginers_date" />
                            <select name="sortby">
                                <option value="">Sort by</option>
                                <option value="topranks" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'topranks') echo 'selected="selected"'; ?>>Top ranks</option>
                                <optgroup label="Shares">
                                    <option value="fb_share" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'fb_share') echo 'selected="selected"'; ?>>Facebook</option>
                                    <option value="gplus_share" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'gplus_share') echo 'selected="selected"'; ?>>Google+</option>
                                    <option value="twt_share" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'twt_share') echo 'selected="selected"'; ?>>Twitter</option>
                                    <option value="linked_share" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'linked_share') echo 'selected="selected"'; ?>>Linkedin</option>
                                    <option value="pinter_share" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'pinter_share') echo 'selected="selected"'; ?>>Pinterest</option>
                                </optgroup>
                                <optgroup label="Likes">
                                    <option value="fb_likes" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'fb_likes') echo 'selected="selected"'; ?>>Facebook</option>
                                </optgroup>
                                <optgroup label="Comments">
                                    <option value="fb_comments" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'fb_comments') echo 'selected="selected"'; ?>>Facebook</option>
                                    <option value="disqus_comments" <?php if (!empty($_GET['sortby']) && $_GET['sortby'] == 'disqus_comments') echo 'selected="selected"'; ?>>Disqus</option>
                                </optgroup>
                            </select>
                            <input type="submit" id="post-query-submit" class="button" value="Filter">
                        </div>
                        <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo self::$paging['result']; ?> items</span></div>
                        <br class="clear">
                    </div>
                    <table class="wp-list-table widefat fixed tags pluginers-tb" cellspacing="0">
                        <thead>
                        <tr>
                            <th rowspan="2" scope="col" id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
                            <th rowspan="2" scope="col" class="manage-column" style="width:130px"><span>Source</span></th>
                            <th rowspan="2" scope="col" class="manage-column"><span>Subject</span></th>
                            <th colspan="3" scope="col" class="manage-column" style="width:200px"><span>Shares</span></th>
                            <th scope="col" class="manage-column" style="width:50px"><span>Likes</span></th>
                            <th colspan="1" scope="col" class="manage-column" style="width:100px"><span>Comments</span></th>
                            <th rowspan="2" scope="col" class="manage-column pluginers-txt-center" style="width:80px"><span>Total</span></th>
                            <th rowspan="2" scope="col" class="manage-column pluginers-txt-center" style="width:50px"><span>Date</span></th>

                        </tr>
                        <tr>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Twitter" src="<?php echo pluginers_imgpath.'/twitter.png';?>" /></span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Facebook" src="<?php echo pluginers_imgpath.'/facebook.png';?>" /></span></th>
                            <!--<th scope="col" class="manage-column pluginers-txt-center"><span><img title="Google+" src="<?php echo pluginers_imgpath.'/gplus.png';?>" /></span></th>
                <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Linkedin" src="<?php echo pluginers_imgpath.'/linkedin.png';?>" /></span></th>-->
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Pinterest" src="<?php echo pluginers_imgpath.'/pinterest.png';?>" /></span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Facebook" src="<?php echo pluginers_imgpath.'/facebook.png';?>" /></span></th>
                            <th scope="col" colspan="1" class="manage-column pluginers-txt-center"><span><img title="Facebook" src="<?php echo pluginers_imgpath.'/facebook.png';?>" /></span></th>
                            <!--<th scope="col" class="manage-column pluginers-txt-center"><span><img title="Disqus" src="<?php echo pluginers_imgpath.'/disqus.png';?>" /></span></th>-->
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th rowspan="2" scope="col" id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
                            <th rowspan="2" scope="col" class="manage-column"><span>Source</span></th>
                            <th rowspan="2" scope="col" class="manage-column"><span>Subject</span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Twitter" src="<?php echo pluginers_imgpath.'/twitter.png';?>" /></span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Facebook" src="<?php echo pluginers_imgpath.'/facebook.png';?>" /></span></th>
                            <!--<th scope="col" class="manage-column pluginers-txt-center"><span><img title="Google+" src="<?php echo pluginers_imgpath.'/gplus.png';?>" /></span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Linkedin" src="<?php echo pluginers_imgpath.'/linkedin.png';?>" /></span></th>-->
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Pinterest" src="<?php echo pluginers_imgpath.'/pinterest.png';?>" /></span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Facebook" src="<?php echo pluginers_imgpath.'/facebook.png';?>" /></span></th>
                            <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Facebook" src="<?php echo pluginers_imgpath.'/facebook.png';?>" /></span></th>
                            <!-- <th scope="col" class="manage-column pluginers-txt-center"><span><img title="Disqus" src="<?php echo pluginers_imgpath.'/disqus.png';?>" /></span></th>-->
                            <th rowspan="2" scope="col" class="manage-column pluginers-txt-center"><span>Total</span></th>
                            <th rowspan="2" scope="col" class="manage-column pluginers-txt-center"><span>Date</span></th>
                            <th rowspan="2" scope="col" class="manage-column column-categories"><span></span></th>
                        </tr>
                        <tr>
                            <th colspan="3" scope="col" class="manage-column"><span>Shares</span></th>
                            <th scope="col" class="manage-column"><span>Likes</span></th>
                            <th colspan="2" scope="col" class="manage-column"><span>Comments</span></th>
                        </tr>
                        </tfoot>
                        <tbody id="the-list" data-wp-lists="list:tag">
                        <?php if ($feeds): $counter = 0;foreach ($feeds AS $feed): $counter++; ?>
                            <tr id="pluginers-service-tab-<?php echo $feed->id; ?>" class="pluginers-service-tab <?php if ($counter % 2 == 0) {echo 'alternate';} ?>">
                                <td scope="row" class="check-column pluginers-txt-center">
                                    <label class="screen-reader-text"></label>
                                    <input type="checkbox" name="ids[]" value="<?php echo $feed->id;?>">
                                    <div class="locked-indicator"></div>
                                </td>
                                <td class="name column-name"><?php echo stripslashes($feed->source); ?></td>
                                <td class="name column-name">
                                    <strong><a href="<?php echo urldecode($feed->url); ?>" target="_blank"><?php echo stripslashes($feed->subject); ?></a></strong>
                                    <?php if(empty($feed->autopub_date) && empty($feed->autopublish)):?><img title="Not published yet" src="<?php echo pluginers_imgpath.'/not_published.png';?>" />
                                    <?php elseif(empty($feed->autopub_date) && !empty($feed->autopublish)):?><img title="Pending in publishing queue" src="<?php echo pluginers_imgpath.'/queue.png';?>" />
                                    <?php else:?><img title="Published" src="<?php echo pluginers_imgpath.'/published.png';?>" />
                                    <?php endif;?>
                                </td>
                                <td class="description column-description pluginers-txt-center"><?php echo ($feed->twitter_share > 1000)? round($feed->twitter_share/1000, 1).'K':$feed->twitter_share;?></td>
                                <td class="description column-description pluginers-txt-center"><?php echo ($feed->fb_share > 1000)? round($feed->fb_share/1000, 1).'K':$feed->fb_share; ?></td>
                                <!--<td class="description column-description pluginers-txt-center"><?php echo ($feed->gplus_share > 1000)? round($feed->gplus_share/1000, 1).'K':$feed->gplus_share; ?></td>
                                <td class="description column-description pluginers-txt-center"><?php echo ($feed->linkedin_share > 1000)? round($feed->linkedin_share/1000, 1).'K':$feed->linkedin_share; ?></td>-->
                                <td class="description column-description pluginers-txt-center"><?php echo ($feed->pinterest_share > 1000)? round($feed->pinterest_share/1000, 1).'K':$feed->pinterest_share; ?></td>
                                <td class="description columpluginers-open-btnn-description pluginers-txt-center"><?php echo ($feed->fb_likes > 1000)? round($feed->fb_likes/1000, 1).'K':$feed->fb_likes;$feed->fb_likes; ?></td>
                                <td class="description column-description pluginers-txt-center"><?php echo ($feed->fb_comments > 1000)? round($feed->fb_comments/1000, 1).'K':$feed->fb_comments; ?></td>
                                <!--<td class="description column-description pluginers-txt-center"><?php echo ($feed->disqus_comments > 1000)? round($feed->disqus_comments/1000, 1).'K':$feed->disqus_comments; ?></td>-->
                                <td class="description column-description pluginers-txt-center"><?php echo ($feed->rank > 1000)? round($feed->rank/1000, 1).'K':$feed->rank; ?></td>
                                <td class="description column-description pluginers-txt-center"><img title="<?php echo date(self::$wpdateformat, strtotime($feed->publishdate)); ?>" src="<?php echo pluginers_imgpath.'/date.png';?>" /></td>
                                <td class="description column-categories">
                                    <a href="<?php echo admin_url();?>admin.php?page=<?php echo $pagname;?>&quickview=<?php echo $feed->id;?>&noheader=1&width=800&height=700" class="button action thickbox">QView</a>
                                    <input type="button" class="button action" value="Delete" onclick="pluginers_delete_service(<?php echo $feed->id; ?>)" />
                                    <img src="<?php echo pluginers_imgpath.'/wpspin_light.gif'; ?>" alt="" class="pluginers_service_<?php echo $feed->id; ?>_loading" style="display:none" />
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr class="no-items"><td class="colspanchange pluginers-txt-center" colspan="13">No items found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="tablenav bottom">
                        <div class="alignleft actions bulkactions">
                            <select name="doaction2">
                                <option value="0">Bulk Actions</option>
                                <option value="publish">Publish Now</option>
                                <option value="delete">Delete</option>
                            </select>
                            <input type="submit" name="apply" class="button action" value="Apply">
                        </div>
                        <div class="tablenav-pages"><span class="displaying-num"><?php echo self::$paging['result']; ?> items</span>
                            <span class="pagination-links"><?php echo paginate_links($paging_args); ?></span>
                        </div>
                        <br class="clear">
                    </div>
                </div>
            </form>
            <div id="col-right" class="pluginers_form_ajax" style="width: 45%"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var pluginers_pageurl = '<?php echo $pageurl; ?>';
</script>