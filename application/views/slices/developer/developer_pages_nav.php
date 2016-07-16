<?php echo heading(bs_glyph('list-alt', 'Page List', true), 3); ?>
<hr />
<?php
$list_items = array(
    array(
        'title' => 'Landing page',
        'uri' => 'developer/pages/landing',
    ),
    array(
        'title' => 'About page',
        'uri' => 'developer/pages/about',
    ),
    array(
        'title' => 'Programs page',
        'uri' => 'developer/pages/programs',
    ),
    array(
        'title' => 'FAQs page',
        'uri' => 'developer/pages/faqs',
    ),
    array(
        'title' => 'Event Calendar page',
        'uri' => 'developer/pages/calendar',
    ),
    array(
        'title' => 'Terms of Service page',
        'uri' => 'developer/pages/tos',
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