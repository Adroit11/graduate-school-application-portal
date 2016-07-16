<?php

$nav_items = array(
    array(
        'uri' => 'admin',
        'title' => 'Dashboard',
    ),
    array(
        'uri' => 'admin/account',
        'title' => 'Account Settings',
    ),
    array(
        'uri' => 'logout',
        'title' => 'Log Out',
    ),
);
$inside_contents = bs_nav_anchor($nav_items);
echo bs_nav_dropdown('admin', bs_glyph('user', 'Admin'), $inside_contents);
?>