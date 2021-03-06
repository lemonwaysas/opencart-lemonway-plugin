<?= $header ?>
<?= $column_left ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" data-toggle="tooltip" title="<?= $button_save ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?= $cancel_link ?>" data-toggle="tooltip" title="<?= $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?= $heading_title ?></h1>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Alerts  -->
        <?php if ($no_permission) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_permission ?></div>
        <?php } ?>

        <?php if ($no_method) { ?>
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <?= $error_no_method ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if ($lemonway_is_test_mode) { ?>
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <?= $error_test_mode ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $error_success ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if ($api_error) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_api ?></div>
        <?php } ?>
        <!-- End alerts -->

        <div class="panel panel-default">
            <div class="panel-body">
                <form id="form-lemonway" class="form-horizontal" method="post" enctype="multipart/form-data">
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist" data-tabs="tabs">
                            <li class="active"><a id="aboutus_tab" href="#aboutus" role="tab" data-toggle="tab"><i class="fa fa-info-circle"></i> <?= $tab_about_us ?></a></li>
                            <li><a id="config_tab" href="#config" role="tab" data-toggle="tab"><i class="fa fa-cog"></i> <?= $tab_config ?></a></li>
                            <li><a id="cc_tab" href="#cc" role="tab" data-toggle="tab"><i class="fa fa-credit-card"></i> <?= $tab_cc ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="aboutus"><?= $about_us ?></div>
                            <div class="tab-pane fade" id="config"><?= $config ?></div>
                            <div class="tab-pane fade" id="cc"><?= $cc ?></div>
                        </div>
                    </div>
                </form>
                <div class="text-right">
                    <small>v<?= LEMONWAY_VERSION ?></small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->
<?= $footer ?>

<script>
    $(function()
    {   
        // Show config tab
        $("#config_link").click(function(e)
        {
            e.preventDefault();
            $("#config_tab").tab('show');
        });

        // Show cc tab
        $("#cc_link").click(function(e)
        {
            e.preventDefault();
            $("#cc_tab").tab('show');
        });
    });
</script>