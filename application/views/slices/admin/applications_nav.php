<?php echo heading(bs_glyph('align-right', 'Task Navigation', true), 3); ?>
<hr />
<?php
$list_items = array(
    array(
        'title' => 'Dashboard',
        'uri' => 'user/apply',
    ),
    array(
        'title' => 'Basic Information',
        'uri' => 'user/apply/basic',
    ),
    array(
        'title' => 'Educational Information',
        'uri' => 'user/apply/education',
    ),
    array(
        'title' => 'Electronic Documents',
        'uri' => 'user/apply/documents',
    ),
    array(
        'title' => 'Applicant Essay',
        'uri' => 'user/apply/essay',
    ),
    array(
        'title' => 'Professional Recommendations',
        'uri' => 'user/apply/recommendations'
    )
);
echo '<ul class="nav nav-pills nav-stacked nav-requirements">';
foreach ($list_items as $item) {
    $active = $item['uri'] == uri_string();
    echo sprintf('<li%1$s>%2$s</li>', $active ? ' class="active"' : '', anchor($item['uri'], $active ? bs_glyph('chevron-right', $item['title'], true) : $item['title']));
    $active = false;
}
echo '</ul>';
?>