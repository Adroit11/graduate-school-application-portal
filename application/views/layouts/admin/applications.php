<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo sprintf('<title>%1$s %2$s %3$s</title>', $title, config_item('title_sep') ? config_item('title_sep') : '|', config_item('title') ? config_item('title') : 'Name this website'); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?php echo chrome_frame(); ?>
        <?php echo view_port(); ?>
        <?php echo $meta; ?>
        <?php echo $css; ?>
        <?php echo $js; ?>
        <!--[if lt IE 9]>
        <?php echo add_js(array('respond.min', 'html5shiv')); ?>
        <![endif]-->
    </head>
    <body>
        <!-- Fixed navbar -->
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php echo anchor('/', 'HAUGS Online Application <sup>beta</sup>', array('class' => 'navbar-brand')); ?>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <?php echo $top_nav; ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php echo $role_nav; ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
        <div class="main">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3">
                        <?php echo $applications_nav; ?>
                    </div>
                    <div class="col-sm-9">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $footer; ?>
        <?php echo add_js(array('analytics')); ?>
    </body>
</html>