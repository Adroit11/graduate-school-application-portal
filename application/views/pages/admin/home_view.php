<?php echo heading('What do you want to do?', 2, 'class="text-center"'); ?>
<?php
$btn_props = array(
    'class' => 'btn-default btn-lg'
);
$items = array(
    array(
        'text' => 'Manage applicants',
        'href' => 'admin/applications'
    ),
    array(
        'text' => 'Manage course offerings',
        'href' => 'admin/course_offerings'
    ),
);
$btn_applications = bs_button_dropdown(bs_glyph('user', 'Applications Module'), $items, $btn_props, true);

$items = array(
    array(
        'text' => 'Manage calendar',
        'href' => 'http://calendar.google.com',
        'data' => array(
            'target' => '_blank',
        ),
    ),
    array(
        'text' => 'Contact developer',
        'href' => 'admin/contact_developer'
    ),
);
$btn_others = bs_button_dropdown(bs_glyph('wrench', 'Other Settings'), $items, $btn_props, true);
?>
<div class="well" style="max-width: 400px; margin: 0 auto">
    <div class="form-group">
        <?php echo $btn_applications; ?>
    </div>
    <div class="form-group">
        <?php echo $btn_others; ?>
    </div>
</div>