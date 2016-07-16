<div class="page page-tos">
    <div class="page-header">
        <h1>Terms of Service</h1>
        <h2><?php echo $content->title; ?></h2>
    </div>
    <?php echo autop($content->content); ?>
    <?php echo autop(sprintf('<em>Last updated: %1$s</em>', date_nice($content->date))); ?>
</div>