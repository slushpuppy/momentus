<div class="wrap">
  <div id="pluginers-icon-logo" class="icon32"><br></div>
  <h2><?php echo get_admin_page_title(); ?></h2>
  <div id="col-container">
    <textarea style="width:80%;height:500px;"><?php echo $error_log;?></textarea>
  </div>
  <form action="" method="post">
    <input type="submit" name="clear" class="button button-primary" style="width: 120px;" value="Clear Error Log">
  </form>
</div>