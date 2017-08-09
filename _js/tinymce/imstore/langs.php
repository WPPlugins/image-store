<?php 

	/**
	 * Image Store - tinymce translation
	 *
	 * @file langs.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/_js/tinymce/imstore/langs.php
	 * @since 3.0.0
	 */
	 
	global $language;
	$language = ( empty($language) ) ?  'en' : $language;

	// escape text only if it needs translating
	function mce_escape( $text ) {
		global $language;
		if ( 'en' == $language ) return $text;
		else return esc_js( $text );
	}
	
	$strings = 'tinyMCE.addI18n("' . $language . '.imstore",{
	lightbox_label:"' . mce_escape( __('Lightbox', 'image-store') ) . '",
	list_label:"' . mce_escape( __('List', 'image-store') ) . '",
	slideshow_label:"' . mce_escape( __('Slideshow', 'image-store') ) . '",
	gallery_search:"' . mce_escape( __('Gallery search', 'image-store') ) . '",
	gallery_id:"' . mce_escape( __('Gallery id', 'image-store') ) . '",
	show_as:"' . mce_escape( __('Show as', 'image-store') ) . '",
	add_gallery:"' . mce_escape( __('Add Gallery', 'image-store') ) . '",
	box_title:"' . mce_escape( __('Image Store Galleries', 'image-store') ) . '",
	tab_tilte:"' . mce_escape( __('Galleries', 'image-store') ) . '"
	});';
