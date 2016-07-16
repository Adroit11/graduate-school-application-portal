<?php
echo bs_nav_anchor('about', 'About');
$nav_items = array(
    array(
        'uri' => 'programs/business',
        'title' => 'Business',
    ),
    array(
        'uri' => 'programs/education',
        'title' => 'Education',
    ),
    array(
        'uri' => 'programs/engineering_it',
        'title' => 'Engineering & IT',
    ),
    array(
        'uri' => 'programs/nursing',
        'title' => 'Nursing',
    ),
);
$inside_contents = bs_nav_anchor($nav_items);
echo bs_nav_dropdown('programs', 'Programs', $inside_contents);
echo bs_nav_anchor('faqs', 'FAQs');
$nav_items = array(
    array(
        'uri' => 'calendar',
        'title' => 'Event Calendar'
    ),
    array(
        'uri' => 'tos',
        'title' => 'Terms of Service'
    ),
);

$inside_contents = bs_nav_anchor($nav_items);
echo bs_nav_dropdown('other', 'Other', $inside_contents);
?>