<form id="form-lemonway_configure" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" >



    <div class="panel" id="fieldset_0">

        <div class="panel-heading">
            <i class="fa fa-cog"></i> <?php echo $text_configuration   ?>
        </div>


        <div class="form-wrapper">

            <div class="form-group">

                <label class="control-label col-lg-3">
                   <?php echo $entry_login;?>

                </label>

                <div class="col-lg-3">
                    <div class="input-group">
                          <span class="input-group-addon">
                                          <i class="fa fa-user "></i>
                           </span>
                        <input type="text" name="lemonway_api_login" id="lemonway_api_login" <?php if (!empty($lemonway_api_login)) echo 'value="'.$lemonway_api_login.'"';?> class="form-control" required>

                    </div>

                    <p class="help-block">
                        <?php echo $help_login_prod; ?>
                    </p>

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3">
                    <?php echo $entry_password; ?>
                </label>


                <div class="col-lg-3">

                    <div class="input-group fixed-width-lg">
                                        <span class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </span>
                        <input type="password" id="lemonway_api_password" name="lemonway_api_password"   class="form-control"  <?php if (empty($lemonway_api_password))  echo 'required';?> >
                    </div>


                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3">
                    <?php echo $entry_wallet ;?>
                </label>


                <div class="col-lg-3">

                    <div class="input-group">
                          <span class="input-group-addon">
                                          <i class="fa fa-google-wallet"></i>
                           </span>
                        <input type="text" name="lemonway_merchant_id" id="lemonway_merchant_id"  <?php if(!empty($lemonway_merchant_id)) echo 'value="'.$lemonway_merchant_id.'"'; ?> class="form-control" required>

                    </div>


                    <p class="help-block">
                      <?php echo $help_wallet ;?>
                    </p>

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3">
                   <?php echo $entry_test; ?>
                </label>


                <div class="col-lg-9">
                             <input type="checkbox" name="lemonway_is_test_mode"  id="lemonway_is_test_mode_on"   value="1" <?php if($lemonway_is_test_mode=='1') echo 'checked';?> >

                    <p class="help-block">
                        <?php echo $help_test; ?>
                    </p>

                </div>

            </div>

            <div class="form-group">

                <label class="control-label col-lg-3">
                    <?php echo $entry_status; ?>
                </label>


                <div class="col-lg-9">
                    <input type="checkbox" name="lemonway_status"  id="lemonway_status"   value="1" <?php if($lemonway_status=='1') echo 'checked';?> >


                </div>

            </div>

            <!-- ACTIVE DEBUG MODE  -->

            <div class="form-group">

                <label class="control-label col-lg-3">
                    <?php echo $entry_debug; ?>
                </label>


                <div class="col-lg-9">
                    <input type="checkbox" name="lemonway_debug"  id="lemonway_debug"   value="1" <?php if($lemonway_debug=='1') echo 'checked';?> >


                </div>

            </div>





        </div><!-- /.form-wrapper -->


        <div class="panel-footer clearfix">
            <button type="submit" value="1" id="module_form_submit_btn" name="submitLemonwayApiConfig"
                    class="btn btn-default pull-right">
                <i class="fa fa-floppy-o"></i> <?php echo $text_save; ?>
            </button>
        </div>

    </div>


    <div class="panel" id="fieldset_1_1">

        <div class="panel-heading">
            <i class="fa fa-cog"></i> <?php echo $text_advanced_configuration; ?>
        </div>


        <div class="form-wrapper">

            <div class="form-group">

                <label class="control-label col-lg-3">
                   <?php echo $entry_directkit_json_url ; ?>
                </label>


                <div class="col-lg-6">

                    <div class="input-group">
                        <span class="input-group-addon">
                              <i class="fa fa-cloud-upload"></i>
                         </span>
                        <input type="text" name="lemonway_directkit_url" id="lemonway_directkit_url"  <?php if(!empty($lemonway_directkit_url)) echo 'value="'.$lemonway_directkit_url.'"' ; ?>  class="form-control">

                    </div>


                    <p class="help-block">
                       <?php echo $help_leave_empty; ?>
                    </p>

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3">
                   <?php echo $entry_webkit_url ; ?>
                </label>


                <div class="col-lg-6">

                    <div class="input-group">
                               <span class="input-group-addon">
                                          <i class="fa fa-cloud-upload"></i>
                                </span>
                        <input type="text" name="lemonway_webkit_url" id="lemonway_webkit_url"  <?php if(!empty($lemonway_webkit_url)) echo 'value="'.$lemonway_webkit_url.'"' ; ?> class="form-control">

                    </div>


                    <p class="help-block">
                       <?php echo $help_leave_empty; ?>
                    </p>

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3">
                    <?php echo $entry_directkit_json_url_test; ?>
                </label>


                <div class="col-lg-6">

                    <div class="input-group">
                           <span class="input-group-addon">
                                          <i class="fa fa-cloud-upload"></i>
                           </span>
                        <input type="text" name="lemonway_directkit_url_test" id="lemonway_directkit_url_test"   <?php if(!empty($lemonway_directkit_url_test)) echo 'value="'.$lemonway_directkit_url_test.'"' ; ?>  class="form-control">

                    </div>


                    <p class="help-block">
                       <?php echo $help_leave_empty; ?>
                    </p>

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3">
                    <?php echo $entry_webkit_url_test ; ?>
                </label>


                <div class="col-lg-6">

                    <div class="input-group">
                         <span class="input-group-addon">
                                          <i class="fa fa-cloud-upload"></i>
                          </span>
                        <input type="text" name="lemonway_webkit_url_test" id="lemonway_webkit_url_test"  <?php if(!empty($lemonway_webkit_url_test)) echo 'value="'.$lemonway_webkit_url_test.'"' ; ?>    class="form-control">

                    </div>


                    <p class="help-block">
                       <?php echo $help_leave_empty; ?>
                    </p>

                </div>

            </div>


        </div><!-- /.form-wrapper -->


        <div class="panel-footer clearfix">
            <button type="submit" value="1" id="module_form_submit_btn_1" name="submitLemonwayApiConfig"
                    class="btn btn-default pull-right">
                <i class="fa fa-floppy-o"></i> <?php echo $text_save; ?>
            </button>
        </div>

    </div>


</form>

