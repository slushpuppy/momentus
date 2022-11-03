<div class="wrap">
   <div id="pluginers-icon-logo" class="icon32"><br></div>
   <h2><?php echo get_admin_page_title();?><a href="<?php echo admin_url();?>admin.php?page=pluginers_manage_cats" class="add-new-h2">Manage Campaign Types</a>
   <a href="javascript:" onclick="pluginers_open_service(-1, 2)" class="add-new-h2">Add New Campaign</a>
   <img src="<?php echo pluginers_imgpath.'/wpspin_light.gif';?>" alt="" class="pluginers_service_-1_loading" style="display:none" />
   </h2>
   <div id="col-container">
      <div id="col-left" style="width: 100%">
         <div class="col-wrap">
           <form action="<?php echo $pageurl; ?>" method="get">
            <input type="hidden" name="page" value="<?php echo $pagname; ?>" />
           <div class="tablenav top" style="height:auto">
              <p class="search-box pluginers-canhide">
                <label class="screen-reader-text">Search Posts:</label>
                <input type="search" name="query" value="<?php echo (!empty($_GET['query'])) ? $_GET['query'] : ''; ?>">
                <input type="submit" id="search-submit" class="button" value="Search">
              </p>
              <br class="clear">
              <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo self::$paging['result']; ?> items</span></div>
              <div class="alignleft actions pluginers-canhide">
                <select name="catid">
                  <option value="">Filter by campaign</option>
                  <?php foreach($cats as $cat):?>
                  <option value="<?php echo $cat->term_id; ?>" <?php if (!empty($_GET['catid']) && $_GET['catid'] == $cat->term_id) echo 'selected="selected"'; ?>><?php echo $cat->name; ?></option>
                  <?php endforeach;?>
                </select>
                <select name="read_status">
                  <option value="">Filter by reading status</option>
                  <option value="pending" <?php if (!empty($_GET['read_status']) && $_GET['read_status'] == 'pending') echo 'selected="selected"'; ?>>Pending</option>
                  <option value="1" <?php if (!empty($_GET['read_status']) && $_GET['read_status'] == 1) echo 'selected="selected"'; ?>>Reading without erros</option>
                  <option value="2" <?php if (!empty($_GET['read_status']) && $_GET['read_status'] == 2) echo 'selected="selected"'; ?>>Invalid source</option>
                </select>
                <select name="status">
                  <option value="">Filter by status</option>
                  <option value="1" <?php if (!empty($_GET['status']) && $_GET['status'] == '1') echo 'selected="selected"'; ?>>Active</option>
                  <option value="inactive" <?php if (!empty($_GET['status']) && $_GET['status'] == 'inactive') echo 'selected="selected"'; ?>>Inactive</option>
                </select>
                <input type="submit" id="post-query-submit" class="button" value="Filter">
              </div>
              <br class="clear">
            </div>
           </form>
             <table class="wp-list-table widefat fixed tags" cellspacing="0">
                <thead>
                   <tr>
                      <!--<th scope="col" class="manage-column column-posts" style="width:25px"><span>ID</span></th>-->
                      <th scope="col" class="manage-column"><span>Name</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Posts</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Status</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Last Update</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Active</span></th>
                      <th scope="col" class="manage-column column-categories" style="width:150px"><span></span></th>
                   </tr>
                </thead>
                <tfoot>
                   <tr>
                       <!--<<th scope="col" class="manage-column column-posts"><span>ID</span></th>-->
                      <th scope="col" class="manage-column"><span>Name</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Posts</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Status</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Last Update</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Active</span></th>
                      <th scope="col" class="manage-column column-categories"><span></span></th>
                   </tr>
                </tfoot>
                <tbody id="the-list" data-wp-lists="list:tag">
                <?php if($sources){$counter = 0;foreach($sources AS $source){$counter++;?>
                  <tr id="pluginers-service-tab-<?php echo $source->id;?>" class="pluginers-service-tab <?php if($counter%2 == 0){echo 'alternate';}?>">
                       <!--<td class="name column-name"><?php echo $source->id;?></td>-->
                      <td class="name column-name"><strong><?php echo $source->name;?></strong><br /></td>
                      <td class="description column-description pluginers-canhide"><?php echo $source->items_count;?></td>
                      <td class="description column-description pluginers-canhide">
                        <?php if($source->read_status == 0):?><img title="Pending" src="<?php echo pluginers_imgpath.'/clock.png';?>" />
                        <?php elseif($source->read_status == 1):?><img title="Reading without erros" src="<?php echo pluginers_imgpath.'/valid.png';?>" />
                        <?php elseif($source->read_status == 2):?><img title="Invalid source: <?php echo $source->read_error;?>" src="<?php echo pluginers_imgpath.'/error.png';?>" />
                        <?php endif;?>
                      </td>
                      <td class="description column-description pluginers-canhide">
                        <?php if(empty($source->last_update)):?><img title="Pending" src="<?php echo pluginers_imgpath.'/clock.png';?>" />
                        <?php else:?><?php echo date(self::$wpdateformat, strtotime($source->last_update));?>
                        <?php endif;?>
                      </td>
                      <td class="description column-description pluginers-canhide">
                        <?php if($source->status == 1):?><img title="Active" src="<?php echo pluginers_imgpath.'/valid.png';?>" />
                        <?php else:?><img title="Inactive" src="<?php echo pluginers_imgpath.'/inactive.png';?>" />
                        <?php endif;?>
                      </td>
                      <td class="description column-categories">
                      <input type="button" class="button action pluginers-open-btn" value="Edit" onclick="pluginers_open_service(<?php echo $source->id;?>, 2)" />
                      <input type="button" class="button action pluginers-open-btn" value="Delete" onclick="pluginers_delete_service(<?php echo $source->id;?>)" />
                      <img src="<?php echo pluginers_imgpath.'/wpspin_light.gif';?>" alt="" class="pluginers_service_<?php echo $source->id;?>_loading" style="display:none" />
                      </td>
                   </tr>
                <?php }}else{?>
                <tr class="no-items"><td class="colspanchange pluginers-txt-center" colspan="5">No items found.</td></tr>
                <?php }?>
                </tbody>
             </table>
             <div class="tablenav bottom">
                <div class="tablenav-pages"><span class="displaying-num"><?php echo self::$paging['result'];?> items</span>
                  <span class="pagination-links">
                  <?php echo paginate_links($paging_args);?>
                  </span>
                </div>
            	<br class="clear">
             </div>
             <br class="clear">
         </div>
      </div>
      <div id="col-right" class="pluginers_form_ajax" style="width: 70%"></div>
   </div>
</div>
<script type="text/javascript">
var pluginers_pageurl = '<?php echo $pageurl;?>';
</script>