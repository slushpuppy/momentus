<div class="wrap">
   <div id="pluginers-icon-logo" class="icon32"><br></div>
   <h2><?php echo get_admin_page_title();?>
    <a href="<?php echo admin_url();?>admin.php?page=pluginers_manage_rss" class="add-new-h2">Manage Sources</a>
    <a href="javascript:" onclick="pluginers_open_service(-1, 2)" class="add-new-h2">Add New Campaign Type</a>
   <img src="<?php echo pluginers_imgpath.'/wpspin_light.gif';?>" alt="" class="pluginers_service_-1_loading" style="display:none" />
   </h2>
   <div id="col-container">
      <div id="col-left" style="width: 70%">
         <div class="col-wrap">
             <table class="wp-list-table widefat fixed tags" cellspacing="0">
                <thead>
                   <tr>
                      <th scope="col" class="manage-column"><span>Name</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Description</span></th>
                      <th scope="col" class="manage-column column-categories" style="width:150px"><span></span></th>
                   </tr>
                </thead>
                <tfoot>
                   <tr>
                      <th scope="col" class="manage-column"><span>Name</span></th>
                      <th scope="col" class="manage-column pluginers-canhide"><span>Description</span></th>
                      <th scope="col" class="manage-column column-categories"><span></span></th>
                   </tr>
                </tfoot>
                <tbody id="the-list" data-wp-lists="list:tag">
                <?php if($cats){$counter = 0;foreach($cats AS $cat){$counter++;?>
                   <tr id="pluginers-service-tab-<?php echo $cat->term_id;?>" class="pluginers-service-tab <?php if($counter%2 == 0){echo 'alternate';}?>">
                      <td class="name column-name"><strong><?php echo $cat->name;?></strong></td>
                      <td class="name column-name pluginers-canhide"><?php echo $cat->description;?></td>
                      <td class="description column-categories">
                      <input type="button" class="button action pluginers-open-btn" value="Edit" onclick="pluginers_open_service(<?php echo $cat->term_id;?>, 2)" />
                      <input type="button" class="button action pluginers-open-btn" value="Delete" onclick="pluginers_delete_service(<?php echo $cat->term_id;?>)" />
                      <img src="<?php echo pluginers_imgpath.'/wpspin_light.gif';?>" alt="" class="pluginers_service_<?php echo $cat->term_id;?>_loading" style="display:none" />
                      </td>
                   </tr>
                <?php }}else{?>
                <tr class="no-items"><td class="colspanchange pluginers-txt-center" colspan="3">No items found.</td></tr>
                <?php }?>
                </tbody>
             </table>
             <br class="clear">
         </div>
      </div>
      <div id="col-right" class="pluginers_form_ajax" style="width: 70%"></div>
   </div>
</div>
<script type="text/javascript">
var pluginers_pageurl = '<?php echo $pageurl;?>';
</script>