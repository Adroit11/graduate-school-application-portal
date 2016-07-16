<div class="page page-faqs">
    <div class="page-header">
        <h1>Frequently Answered Questions (FAQs)</h1>
    </div>
    <?php if (isset($content) && !empty($content)): ?>
        <?php
        echo bs_panel_open(true);
        $idx = 1;
        foreach ($content as $qa) {
            echo bs_panel_item($idx, $qa->question, $qa->answer);
        }
        echo bs_panel_close();
        ?>
    <?php else: ?>
        <div class="alert alert-info">
            There are no FAQs set up yet. Come back later.
        </div>
    <?php endif; ?>
</div>