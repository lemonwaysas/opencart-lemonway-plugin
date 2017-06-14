    <form id="form-lemonway_oneclic" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data" >

            <div class="panel" >

                <div class="panel-heading">
                    <i class="fa fa-cog"></i>			<?php echo $text_configration_one_click ;?>
                </div>


                <div class="form-wrapper">

                    <div class="form-group">

                        <label class="control-label col-lg-3">
                            <?php echo $entry_one_click;?>
                        </label>


                         <div class="col-lg-9">

                                <input type="checkbox" name="lemonway_oneclick_enabled" id="lemonway_cc_oneclic_enabled_on" value="1" <?php if($lemonway_oneclick_enabled=='1') echo 'checked';?> >



                            <p class="help-block">
                              <?php echo $help_oneclick;?>
                            </p>

                       </div>

                    </div>


                     <div class="form-group">

                            <label class="control-label col-lg-3">
                                <?php echo $entry_css;?>
                            </label>



                            <div class="col-lg-6">

                                <div class="input-group">
                                        <span class="input-group-addon">
                                                  <i class="fa fa-css3"></i>
                                                </span>
                                    <input type="text" name="lemonway_css_url"   id="LEMONWAY_CSS_URL" class="form-control"  <?php echo 'value="'.$lemonway_css_url.'"' ;?> >
                                </div>

                            </div>

                     </div>
                </div>

                <div class="panel-footer clearfix">
                    <button type="submit"  class="btn btn-default pull-right">
                        <i class="fa fa-floppy-o"></i> <?php echo $text_save; ?>
                    </button>
                </div>
            </div>
    </form>

