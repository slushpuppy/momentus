<form action="<?php echo $pageurl;?>&noheader=1" method="post" id="pluginers_jform" class="validate">
<input type="hidden" name="id" value="<?php echo $cat->term_id;?>">
   <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content" class="edit-form-section">
         <div id="" class="stuffbox">
            <h3><label><?php echo (empty($cat->name))?'Add New Category':$cat->name;?></label></h3>
            <div class="inside">
               <table class="form-table">
                <tbody>
                  <tr valign="top" class="form-required">
                     <td class="first">Name</td>
                     <td>
                     <input name="name" type="text" size="40" value="<?php echo $cat->name;?>" aria-required="true">
                     </td>
                  </tr>
                  <tr valign="top" class="form-required">
                     <td class="first">Description</td>
                     <td>
                       <textarea name="description" cols="60" rows="6"><?php echo $cat->description;?></textarea>
                     </td>
                  </tr>
                  <tr valign="top">
                    <td colspan="2"><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="Save Changes">
                    <img src="<?php echo pluginers_imgpath;?>/wpspin_light.gif" class="pluginers_process" alt="" /></td>
                 </tr>
                </tbody>
              </table>
            </div>
         </div>
      </div>
   </div>
</form>