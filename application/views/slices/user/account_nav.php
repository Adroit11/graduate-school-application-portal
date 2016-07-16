<?php echo heading(bs_glyph('cog', 'Settings', true), 3); ?>
<hr />
<?php
$list_items = array(
    array(
        'title' => 'General',
        'uri' => 'user/account',
    ),
    array(
        'title' => 'Notifications',
        'uri' => 'user/account/notifications',
    ),
    array(
        'title' => 'Close Account',
        'uri' => 'user/account/close',
    ),
);
echo '<ul class="nav nav-pills nav-stacked nav-requirements">';
foreach ($list_items as $item) {
    $active = $item['uri'] == uri_string();
    echo sprintf('<li%1$s>%2$s</li>', $active ? ' class="active"' : '', anchor($item['uri'], $active ? bs_glyph('chevron-right', $item['title'], true) : $item['title']));
    $active = false;
}
echo '</ul>';
?>