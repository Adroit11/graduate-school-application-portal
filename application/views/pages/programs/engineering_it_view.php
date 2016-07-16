<div class="page page-programs page-programs-engineering-it">
    <div class="container">
        <div class="page-header">
            <h1>Programs: Engineering & IT</h1>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h3>Current offerings</h3>
                <hr />
                <?php if (isset($content->courses) && is_object($content->courses)): ?>
                    <div class="well">
                        <?php
                        foreach ($content->courses as $course) {
                            echo sprintf('<p>%1$s</p>', bs_glyph('chevron-right', $course));
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Courses are still being generated.</div>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <h3>About the program</h3>
                <hr />
                <?php if (isset($content->content) && $content->content != ''): ?>
                    <?php echo autop($content->content); ?>
                <?php else: ?>
                    <div class="alert alert-info">Content under development.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>