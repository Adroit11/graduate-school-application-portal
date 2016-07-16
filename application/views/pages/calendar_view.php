<div class="page page-calendar">
    <div class="page-header">
        <h1>Calendar of Activities</h1>
    </div>
    <?php if (isset($content->gcal_url) && $content->gcal_url != ''): ?>
        <iframe src="<?php echo $content->gcal_url; ?>" style=" border-width:0 " width="100%" height="600" frameborder="0" scrolling="no"></iframe>
    <?php else: ?>
        <div class="alert alert-info">
            <?php echo prop('fallback', $content, 'Calendar not loaded. Please try again.'); ?>
        </div>
    <?php endif ?>
</div>