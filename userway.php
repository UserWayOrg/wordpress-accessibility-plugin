<?php
/*
Plugin Name: Accessibility by UserWay
Plugin URI: https://userway.org
Description: The UserWay Accessibility Widget is a WordPress plugin that helps make your WordPress site more accessible without refactoring your website's existing code and will increase compliance with WCAG 2.1, ATAG 2.0, ADA, & Section 508 requirements.
Version: 2.5.4
Author: UserWay.org
Author URI: https://userway.org
*/

/*
    Copyright 2020  UserWay  (email: admin@userway.org)
*/

define( 'USW_USERWAY_DIR', plugin_dir_path( __FILE__ ) );
define( 'USW_USERWAY_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, 'usw_userway_activation' );
register_activation_hook(__FILE__, 'userway_plugin_activation_callback');
register_uninstall_hook( __FILE__, 'usw_userway_uninstall' );

require_once( USW_USERWAY_DIR . 'includes/functions.php' );
require_once( USW_USERWAY_DIR . 'includes/notifications.php' );

function usw_userway_activation() {
	initUwTable();
}

function usw_userway_uninstall() {
	removeUwTable();
}

function usw_userway_load() {
	if ( is_admin() ) {
		require_once( USW_USERWAY_DIR . 'includes/admin.php' );
	}
	require_once( USW_USERWAY_DIR . 'includes/controller.php' );
}

usw_userway_load();

function usw_addplugin_footer_notice() {
	global $wpdb;

	$table_exist = isUwTableExist();
	$table_name  = $wpdb->prefix . 'userway';
	$date        = date( "Y-m-d H:i:s" );

	if ( ! $table_exist ) {
		initUwTable();
	}

	$account = getUwAccount();

	if ( ! isset( $account ) ) {
		$account = getRemoteUwAccountId();
		if ( $account ) {
			$wpdb->insert( $table_name, [
				'account_id'   => $account,
				'state'        => true,
				'created_time' => $date,
				'updated_time' => $date,
			] );
		}
	}

	$account = getUwAccount();

	if ( isset( $account['account_id'] ) && mb_strlen( $account['account_id'] ) > 0 && isset( $account['state'] ) && (boolean) $account['state'] === true ) {
		echo "<script>
              (function(e){
                  var el = document.createElement('script');
                  el.setAttribute('data-account', '" . $account['account_id'] . "');
                  el.setAttribute('src', 'https://cdn.qa.userway.dev/widget.js');
                  document.body.appendChild(el);
                })();
              </script>";
	}
}

add_action( 'wp_footer', 'usw_addplugin_footer_notice' );
