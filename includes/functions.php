<?php
/*
    Copyright 2021  UserWay  (email: admin@userway.org)
*/

function initUwTable() {
	global $wpdb;
	$table_name      = $wpdb->prefix . 'userway';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "
    CREATE TABLE IF NOT EXISTS `$table_name` (
        `preference_id` INT(10) NOT NULL AUTO_INCREMENT,
        `account_id`    VARCHAR(255) NOT NULL,
        `state`         smallint(5) NOT NULL,
		`created_time`  TIMESTAMP NOT NULL,
        `updated_time`  TIMESTAMP NOT NULL,
        PRIMARY KEY (`preference_id`)
    ) $charset_collate
    ";

	if ( ! function_exists( 'dbDelta' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	}

	dbDelta( $sql );
}

function removeUwTable() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'userway';

	$sql = "DROP TABLE IF EXISTS `$table_name`";

	$wpdb->get_results( $sql );
}
