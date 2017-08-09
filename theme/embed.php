<?php


	/**
	 * Image Store - tinymce embed code
	 *
	 * @file embed.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/theme/embed.php
	 * @since 3.5.5
	 */

		header( 'Robots: none' );
		header( 'HTTP/1.1 200 OK' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Cache-control:no-cache,no-store,must-revalidate,max-age=0');

		if ( !current_user_can( 'edit_ims_gallery' ) )
			die( );

		$values = "";
		foreach( array( 'number', 'id', 'layout', 'orderby', 'order', 'caption', 'linkto' ) as $value ){
			if( ! empty( $_REQUEST[$value] ) )
				$values .= " $value=" . esc_attr( $_REQUEST[$value] );
		}

		echo do_shortcode( "[ims-gallery". $values . "]" ) ;
		die();
