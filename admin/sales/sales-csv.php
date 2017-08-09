<?php

	/**
	 * Image Store - Sales CSV File
	 *
	 * @file sales-csv.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/admin/sales/sales-csv.php
	 * @since 0.5.0
	 */

	//define constants
	define( 'WP_ADMIN', true );
	define( 'DOING_AJAX', true );

	$_SERVER['PHP_SELF'] = "/wp-admin/sales-csv.php";

	//load wp
	require_once implode( '/', explode( '/', $_SERVER['SCRIPT_FILENAME'], -6 ) ) . '/wp-load.php';

	check_admin_referer( 'ims_manage_sales' );

	//check that a user has the right permisssion
	if( !current_user_can( 'ims_manage_customers' ) )
		die( );

	$enco = get_bloginfo( 'charset' );

	//don't cache file
	header( 'Cache-control:private' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Last-Modified:' . gmdate( 'D,d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-control:no-cache,no-store,must-revalidate,max-age=0' );

	header( 'Content-Description:File Transfer' );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Content-type: application/csv;  charset=' . "$enco; encoding=$enco" );
	header( 'Content-Disposition:attachment; filename=image-store-sales.csv' );

	$query = apply_filters( 'ims_sales_csv_query',
		"SELECT ID, post_title, post_status, post_date, meta_value
		FROM $wpdb->posts p
		JOIN $wpdb->postmeta pm
		ON ( p.ID = pm.post_id )
		WHERE post_type = 'ims_order'
		AND post_status != 'trash'
		AND post_status != 'draft'
		AND meta_key = '_response_data'
		GROUP BY ID
		ORDER BY post_date DESC"
	);

	$results = $wpdb->get_results( $query );

	if( empty( $results ) )
		die( );

	$columns = apply_filters( 'ims_sales_csv_columns', array(
		'txn_id'		=> __( 'Order number', 'image-store'),
		'post_date'		=> __( 'Date', 'image-store'),
		'payment_gross' => __( 'Amount', 'image-store'),
		'tax' 			=> __( 'Tax', 'image-store'),
		'first_name' 	=> __( 'Firstname', 'image-store'),
		'last_name' 	=> __( 'Lastname', 'image-store'),
		'num_cart_items'=> __( 'Images', 'image-store'),
		'payment_status'=> __( 'Payment status', 'image-store'),
		'post_status' 	=> __( 'Order Status', 'image-store'),
		'address_street'=> __( 'Address', 'image-store'),
		'address_city'	=> __( 'City', 'image-store'),
		'address_state'	=> __( 'State', 'image-store'),
		'address_zip'	=> __( 'Zip', 'image-store'),
		'address_country'=> __( 'Country', 'image-store'),
	) );

	$str = '';
	foreach( $columns as $column ) $str .= $column ."\t"; $str .= "\n";
	foreach( $results as $result ){
		$data = unserialize( $result->meta_value );
		foreach( $columns as $key => $column ){
			if( preg_match( "/(post_date|post_status)/i", $key ) ) {
				$str .= isset( $result->$key ) ? str_replace( ',', '', $result->$key ) . "\t" : "\t";
			}else{
				$str .= isset( $data[$key] ) ? str_replace( ',', '', $data[$key] ) . "\t" : "\t";
			}
		}
		$str .= "\n";
	}

	echo  chr( 255 ) . chr( 254 ) . mb_convert_encoding( $str,  'UTF-16LE', $enco ) ;
	die( );
