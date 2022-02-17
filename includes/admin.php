<?php

/*
    Copyright 2021  UserWay  (email: admin@userway.org)
*/

$true_page = 'userway';

require_once( USW_USERWAY_DIR . 'includes/functions.php' );

function usw_userway_settings() {
	add_menu_page( 'UserWay', 'UserWay', 'manage_options', 'userway', 'usw_userway_settings_page', 'dashicons-universal-access-alt' );
}

add_action( 'admin_menu', 'usw_userway_settings' );