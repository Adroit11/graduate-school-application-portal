<div class="page-about">
    <div class="page-header">
        <h1>About</h1>
        <h2>Get to know more about the online application system</h2>
    </div>
    <div class="row">
        <div class="col-md-9">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab"><?php echo autop($content->title_1); ?></a></li>
                <li><a href="#tab2" data-toggle="tab"><?php echo autop($content->title_2); ?></a></li>
                <li><a href="#tab3" data-toggle="tab"><?php echo autop($content->title_3); ?></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab1">
                    <?php echo autop($content->content_1); ?>
                </div>
                <div class="tab-pane" id="tab2">
                    <?php echo autop($content->content_2); ?>
                </div>
                <div class="tab-pane" id="tab3">
                    <?php echo autop($content->content_3); ?>
                </div>
            </div>
        </div>
    </div>
</div>