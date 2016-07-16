<?php

$nav_items = array(
    array(
        'uri' => 'developer',
        'title' => 'Dashboard',
    ),
    array(
        'uri' => 'developer/account',
        'title' => 'Account Settings',
    ),
    array(
        'uri' => 'logout',
        'title' => 'Log Out',
    ),
);
$inside_contents = bs_nav_anchor($nav_items);
echo bs_nav_dropdown('developer', bs_glyph('user', 'Developer'), $inside_contents);