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

function isUwTableExist() {
	global $wpdb;
	$table_name  = $wpdb->prefix . 'userway';
	$table_exist = false;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
		$table_exist = true;
	}

	return $table_exist;
}

function getRemoteUwAccountId() {
	$account_id = null;
	$apiUrl     = 'https://api.userway.org/api/v1/users/account-by-site';

	$args = array(
		'body' => array(
			'site' => $_SERVER['HTTP_HOST'],
		)
	);

	$response      = wp_remote_post( $apiUrl, $args );
	$response_code = wp_remote_retrieve_response_message( $response );

	if ( $response_code === 'OK' ) {
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $response_body['account'] ) ) {
			$account_id = $response_body['account'];
		}
	}

	return $account_id;
}

function getUwAccount() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'userway';
	$account    = null;
	$dbData     = $wpdb->get_results( "SELECT * FROM $table_name LIMIT 0, 1" );

	if ( isset( $dbData[0] ) ) {
		$account = [
			'account_id' => $dbData[0]->account_id,
			'state'      => $dbData[0]->state,
		];
	}

	return $account;
}