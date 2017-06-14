<?php echo $header; ?><?php echo $column_left; ?>
<!--
<script>
    var head  = document.getElementsByTagName('head')[0];
    var link  = document.createElement('link');
    link.rel  = 'stylesheet';
    link.href = "<?php echo $link_css; ?>";
    head.appendChild(link);

</script>
-->
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit"  data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">


        <?php if (isset($error_warning)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if( isset($error_curl_not_installed)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_curl ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>


        <?php if( isset($error_login_not_set)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_login_not_set ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if( isset($error_password_not_set)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_password_not_set ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if( isset($error_merchant_id_not_set)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_merchant_id_not_set ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>


        <?php if( isset($error_testConfig)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_testConfig ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if($lemonway_status!='1') { ?>
        <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <?php echo $warning_status ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>




        <?php if( isset($success)) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success ;?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>


        <div class="panel panel-default">

            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit_config; ?> </h3>
            </div>

            <div class="panel-body">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" data-tabs="tabs">
                        <li class="active">  <a href="#aboutus"   role="tab" data-toggle="tab"> <?php echo $text_about_us ;?> </a></li>
                        <li>  <a href="#access_api" role="tab" data-toggle="tab"> <?php echo $text_configuration ;?> </a></li>
                        <li> <a href="#one_click" role="tab" data-toggle="tab"> <?php echo $text_one_click ;?> </a></li>

                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div  class="tab-pane active" id="aboutus"  > <?php echo $about_us; ?>  </div>
                        <div  class="tab-pane" id="access_api" > <?php echo $configure; ?>  </div>
                        <div  class="tab-pane " id="one_click" > <?php echo $one_click; ?>  </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>