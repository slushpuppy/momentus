<div class="wrap">
    <div id="pluginers-icon-logo" class="icon32"><br></div>
    <div id="col-container">
        <div id="col-left" style="width:92%;margin-top: 10px;">
            <div class="col-wrap">
                <form action="<?php echo $pageurl; ?>&noheader=1" method="post" id="pluginers_jform" class="reload_after validate">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content" class="edit-form-section">
                            <div id="" class="stuffbox" style="padding:10px">
                                <div class="inside">

                                    <h2>License key</h2>

                                    <div class="info-box">
                                        <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                                        <div class="info-box-content">
                                            <div class="form-required">
                                                <input name="license" type="text" size="40" style="width:300px" value="<?php echo $license_key; ?>" aria-required="true"><span style="color: <?php echo $license_status_color;?>"><?php echo $license_status;?></span>
                                                <p class="howto">Enter license key provided to you</p>
                                            </div>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>

                                    </div>
                                    <div><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="Activate">
                                        <img src="<?php echo pluginers_imgpath.'/wpspin_light.gif';?>" class="pluginers_process" alt=""></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

        </div>
    </div>
</div>