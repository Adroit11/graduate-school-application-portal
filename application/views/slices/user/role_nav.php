<?php

$nav_items = array(
    array(
        'uri' => 'user/apply',
        'title' => 'Online Application',
    ),
    array(
        'uri' => 'user/account',
        'title' => 'Account Settings',
    ),
    array(
        'uri' => 'logout',
        'title' => 'Log Out',
    ),
);
$inside_contents = bs_nav_anchor($nav_items);
echo bs_nav_dropdown('user', bs_glyph('user', 'User'), $inside_contents);
?>