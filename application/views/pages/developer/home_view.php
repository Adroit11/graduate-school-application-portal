<div class="page page-developer page-developer-home">
    <?php echo heading('What do you want to do?', 2, 'class="text-center"'); ?>
    <?php
    $btn_props = array(
        'class' => 'btn-default btn-lg'
    );
    $items = array(
        array(
            'text' => 'Manage site settings',
            'href' => 'developer/site_settings'
        ),
        array(
            'text' => 'Manage pages',
            'href' => 'developer/pages'
        ),
    );
    $btn_frontend = bs_button_dropdown(bs_glyph('home', 'Front-end Module'), $items, $btn_props, true);

    $items = array(
        array(
            'text' => 'Manage admins',
            'href' => 'developer/admins'
        ),
        array(
            'text' => 'Manage email settings',
            'href' => 'developer/emails'
        ),
        array(
            'text' => 'Put website under maintenance',
            'href' => 'developer/maintenance'
        ),
    );
    $btn_backend = bs_button_dropdown(bs_glyph('lock', 'Back-end Module'), $items, $btn_props, true);
    ?>
    <div class="well" style="max-width: 400px; margin: 0 auto">
        <div class="form-group">
            <?php echo $btn_frontend; ?>
        </div>
        <div class="form-group">
            <?php echo $btn_backend; ?>
        </div>
    </div>
</div>