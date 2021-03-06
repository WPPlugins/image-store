<?php

	/**
	 * Image Store -  Single Gallery Shortcode
	 *
	 * @file shortcode.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/_store/shortcode.php
	 * @since 0.5.3
	 */

	// Stop direct access of the file
	if ( !defined( 'ABSPATH' ) )
		die( );

	class ImStoreShortCode {

		/**
		 * Constructor
		 *
		 * @return void
		 * @since 0.5.3
		 */
		function ImStoreShortCode( ) {

			global $ImStore;
			$this->opts = $ImStore->opts;
			add_shortcode( 'ims-gallery', array( &$this, 'ims_gallery_shortcode' ), 50 );
		}

		/**
		 * Core function display gallery
		 *
		 * @param array $atts
		 * @return string
		 * @since 0.5.3
		 */
		function ims_gallery_shortcode( $atts ) {

			extract( $atts = shortcode_atts( array(
				'id' => '',
				'filmstrip' => 1,
				'linkto' => 'file',
				'caption' => false,
				'number' => false,
				'slideshow' => false,
				'size' => 'thumbnail',
				'layout' => 'lightbox',
				'simple_slideshow' => false,
				'sort' => $this->opts['imgsortdirect'],
				'sortby' => $this->opts['imgsortorder'],
			), $atts,  'ims_gallery' ) );

			if ( empty( $id ) )
				return;

			global $wpdb, $ImStore;

			$this->galid = $wpdb->get_var( $wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta
				 WHERE meta_key = '_ims_gallery_id'
				 AND meta_value = '%s'"
			, $id ) );

			if ( empty( $this->galid ) )
				return;

			$this->order 	= $sort;
			$this->sortby = $ImStore->sort[$sortby];
			$this->limit 	= ( !$number || strtolower($number) == 'all' ) ? false : $number;
			$slideshow 		= ( isset( $layout ) && preg_match( '/slideshow/i', $layout ) ) ? true : false;

			$this->get_gallery( );
			
			if ( $slideshow )
				return $this->display_slideshow( '', $layout, $caption );
			return $this->display_galleries( $atts );
		}

		/**
		 * Get gallery images
		 *
		 * @return void
		 * @since 2.0.0
		 */
		function get_gallery( ) {

			global $wpdb;

			$limit = ( empty( $this->limit ) ) ? '' : " LIMIT $this->limit ";
			$this->attachments = wp_cache_get( 'ims_shortcode_' . $this->galid . $this->limit  , 'image-store' );

			if ( false == $this->attachments ) {
				$this->attachments = $wpdb->get_results( $wpdb->prepare(
					"SELECT *, meta_value meta
					FROM $wpdb->posts AS p
					LEFT JOIN $wpdb->postmeta AS pm
					ON p.ID = pm.post_id
					WHERE post_type = 'ims_image'
					AND meta_key = '_wp_attachment_metadata'
					AND post_status = 'publish' AND post_parent = %d
					ORDER BY $this->sortby $this->order $limit"
				, $this->galid ) );
			}

			if ( empty( $this->attachments ) )
				return;

			foreach ( $this->attachments as $key => $post )
				$this->attachments[$key]->meta = maybe_unserialize( $post->meta );

			wp_cache_set( 'ims_shortcode_' . $this->galid . $this->limit , $this->attachments, 'image-store' );
			return;
		}

		/**
		 * Display slideshow navigation
		 *
		 * @return string
		 * @since 3.2.1
		 */
		function slide_show_nav( ){
			global $ImStore;
			$ImStore->attachments = $this->attachments;
			return $ImStore->slide_show_nav( );
		}

		/**
		 * Display galleries
		 *
		 * @param array $atts
		 * @return string
		 * @since 0.5.3
		 */
		function display_galleries( $atts ) {
			global $ImStore;

			extract( $atts );

			$ImStore->galid = $this->galid;
			$ImStore->active_store = false;
			$ImStore->opts['favorites'] = false;

			if ( $linkto == 'attachment' )
				$ImStore->opts['attchlink'] = true;

			$ImStore->attachments = $this->attachments;

			$css = ( $layout == 'lightbox' && $linkto != 'attachment'  ) ? 'ims-colorbox' : 'ims-' . $layout;
			$images =  '<div class="' . $css . '">' . $ImStore->display_galleries( ) . '</div>';

			if ( $caption ) return $images;
			else return preg_replace('#<figcaption(.*?)>(.*?)</figcaption>#is', '', $images);
		}

		/**
		 * Display slideshow
		 *
		 * @param string $output
		 * @param string|bool $layout
		 * @return string
		 * @since 0.5.3
		 */
		function display_slideshow( $output = '', $layout = false, $caption = false ) {

			$this->active_store = false;
			$this->opts['favorites'] = false;

			$include_file = apply_filters( 'ims_slideshow_path', IMSTORE_ABSPATH . '/_store/' .
				 ( $layout == 'simple_slideshow' ? 'simple_slideshow.php'  : 'slideshow.php' )
			);

			if( file_exists( $include_file ) )
				include( $include_file );

			return $output;
		}

		/**
		 * Depricated  use get_gallery
		 */
		function get_galleries(){
			_deprecated_function( 'get_galleries', '4.4.3', 'get_gallery');
			$this->get_gallery( );
		}

	}
