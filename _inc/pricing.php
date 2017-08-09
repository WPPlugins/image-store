<?php

/**
 * Image Store - Pricing
 *
 * @file pricing.php
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2016
 * @filesource  wp-content/plugins/image-store/_inc/pricing.php
 * @since 3.2.1
 */

class ImStorePricing extends ImStoreAdmin {

	public $tabs = array( );

	/**
	 * Constructor
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function ImStorePricing( $page, $action ) {

		$this->ImStoreAdmin( $page, $action );

    $this->screen     = "ims_gallery_page_{$page}";
    $this->metaboxes  = array('ims_pricelists', 'ims_packages');

    add_action( 'wp_admin', array( &$this, 'register_screen_columns' ), 15 );
    add_action( 'hidden_meta_boxes', array( &$this, 'hidden_meta_boxes' ), 15, 2 );

		//speed up wordpress load
		if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) || SHORTINIT )
			return;

		add_action( 'init', array( &$this, 'pricing_init' ), 15 );
		add_filter( 'auth_redirect', array( &$this, 'pricing_actions' ), 5 );
		add_action( 'admin_print_styles', array( &$this, 'register_metaboxes' ), 10 );
	}

  /**
   * Persisten metabox options
   *
   * @return void
   * @since 3.5.7
   */
  function hidden_meta_boxes( $hidden, $screen ){
    if( ! $this->in_array( $screen->id,  $this->metaboxes ) )
      return $hidden;
    return  (array) get_user_option( "closedpostboxes_{$this->screen}" );
  }

	/**
	 * Add hook after wp starts
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function pricing_init( ){

		add_action( 'ims_pricing_pricelists_tab', array( &$this, 'ims_pricelist_tab' ), 1, 2 );
		add_action( 'ims_pricing_packages_tab', array( &$this, 'ims_packages_tab' ), 1, 2 );
		add_action( 'ims_pricing_promotions_tab', array( &$this, 'ims_promotions_tab' ), 1, 2 );

		$this->tabs = apply_filters( 'ims_pricing_tabs', array(
			'pricelists' => __('Price lists', 'image-store'),
			'packages' => __('Packages', 'image-store'),
			'promotions' => __('Promotions', 'image-store'),
		) );
	}

	/**
	 *  Register metabox hooks
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function register_metaboxes( ){

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_enqueue_script( 'jquery-ui-datepicker', IMSTORE_URL . '/_js/jquery-ui-datepicker.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( 'ims-pricing', IMSTORE_URL . '/_js/pricing.js', array( 'jquery',  'jquery-ui-draggable' ), $this->version, true );

		wp_enqueue_style( 'ims-pricing', IMSTORE_URL . '/_css/pricing.css', false, $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui-datepicker', IMSTORE_URL . '/_css/jquery-datepicker.css', false, $this->version, 'all' );

		add_meta_box( 'image_sizes-price', __( 'Image sizes', 'image-store' ), array( &$this, 'image_sizes' ), 'ims_pricelists', 'side', 'default', array( 'tabid' => 'pricelists' ) );
		add_meta_box( 'image_sizes-package', __( 'Image sizes', 'image-store' ), array( &$this, 'image_sizes' ), 'ims_packages', 'side', 'default', array( 'tabid' => 'packages' ) );

		add_meta_box( 'color_options', __( 'Color options', 'image-store' ), array( &$this, 'color_options' ), 'ims_pricelists', 'side' );
		add_meta_box( 'shipping_options', __( 'Shipping options', 'image-store' ), array( &$this, 'shipping_options' ), 'ims_pricelists', 'side');

		add_meta_box( 'price-list-new', __( 'New pricelist', 'image-store' ), array( &$this, 'new_pricelist' ), 'ims_pricelists', 'normal' );
		add_meta_box( 'price-list-box', __( 'Price lists', 'image-store'), array( &$this, 'price_lists' ), 'ims_pricelists', 'normal');
		add_meta_box( 'print-finishes-box', __( 'Print finishes', 'image-store' ), array( &$this, 'print_finishes' ), 'ims_pricelists', 'normal' );
		add_meta_box( 'price-list-package', __( 'Packages', 'image-store' ), array( &$this, 'lists_packages' ), 'ims_pricelists', 'normal' );
		add_meta_box( 'color_filters', __( 'Color filters', 'image-store' ), array( &$this, 'color_filters' ), 'ims_pricelists', 'normal' );

		add_meta_box( 'new_package', __( 'New Package', 'image-store' ), array( &$this,'new_package' ), 'ims_packages', 'normal' );
		add_meta_box( 'packages-list', __( 'Packages', 'image-store' ),  array( &$this,'package_list' ), 'ims_packages', 'normal' );

		if( isset( $_REQUEST['iaction'] ) ) {
			if( $_REQUEST['iaction'] == 'new' )
				add_meta_box( 'new_promo', __( 'New Promotion', 'image-store' ),  array( &$this,'new_promotion' ), 'ims_promotions', 'normal' );
			else add_meta_box( 'new_promo', __( 'Edit Promotion', 'image-store' ),  array( &$this,'new_promotion' ), 'ims_promotions', 'normal' );
		}
		add_meta_box( 'promotions_table', __( 'Promotion list', 'image-store' ),  array( &$this,'promotions_table' ), 'ims_promotions', 'normal' );
	}

	/* Register screen columns
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function register_screen_columns( ) {

		$this->columns = array(
			'cb' => '<input type="checkbox">', 'name' => __('Name', 'image-store'),
			'code' => __('Code', 'image-store'), 'starts' => __('Starts', 'image-store'),
			'expires' => __('Expires', 'image-store'), 'type' => __('Type', 'image-store'),
			'discount' => __('Discount', 'image-store'), 'limit' => __('Limit', 'image-store'),
			'redeemed' => __('Redeemed', 'image-store'),
		);

		add_filter( 'screen_settings', array( &$this, 'screen_settings' ) );
		register_column_headers( $this->screen, $this->columns );

		$this->hidden = ( array ) get_hidden_columns( $this->screen );
	}

	/**
	 * Get all packages
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function get_packages( ) {
		$packages = wp_cache_get( 'ims_packages' );
		if ( false == $packages ) {
			global $wpdb;
			$packages = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'ims_package'" );
			wp_cache_set( 'ims_packages', $packages );
		}
		return $packages;
	}

	/**
	 * Display pricelist metabox content
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function ims_pricelist_tab( ) {
		echo '<div class="inside-col2">';
		  do_meta_boxes( 'ims_pricelists', 'normal', $this );
		echo '</div><div class="inside-col1">';
		  do_meta_boxes( 'ims_pricelists', 'side', $this );
		echo '</div><div class="clear"></div>';
	}

	/**
	 * Display packages metabox content
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function ims_packages_tab( ) {
		echo '<div class="inside-col2">';

		do_meta_boxes('ims_packages', 'normal', $this);
		echo '</div><div class="inside-col1">';

		do_meta_boxes('ims_packages', 'side', $this);
		echo '</div><div class="clear"></div>';
	}

	/**
	 * Display unit sizes dropdown menu
	 *
	 * @return void
	 * @since 1.1.0
	 */
	function dropdown_units( $name, $selected ) {
		$output = '<select name="' . esc_attr($name) . '" class="unit">';
		foreach ( $this->units as $unit => $label ) {
			$select = ( $selected == $unit ) ? ' selected="selected"' : '';
			$output .= '<option value="' . esc_attr( $unit ) . '" ' . $select . '>' . esc_html($label) . '</option>';
		}
		echo $output .= '</select>';
	}

	/**
	 * Display promotions metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function ims_promotions_tab( ) {
		do_meta_boxes( 'ims_promotions', 'normal', $this );
	}

	/**
	 * Display new pricelist metabox content
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function new_pricelist( ) {
		echo '<form method="post" action="#pricelists" >
			<p><label>' . __( 'Name', 'image-store' ) . ' <input type="text" name="pricelist_name" class="regular-text" /></label>
			<input type="submit" name="newpricelist" value="' . esc_attr__('Add List', 'image-store') . '" class="button-primary" /></p>';
		wp_nonce_field( 'ims_new_pricelist' );
		echo '</form>';
	}

	/**
	 * Display new package metabox content
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function new_package( ) {
		echo '<form method="post" action="#packages" >
			<p><label>' . __( 'Name', 'image-store' ) . ' <input type="text" name="package_name" class="regular-text" /></label>
			<input type="submit" name="newpackage" value="' . esc_attr__( 'Add Package', 'image-store' ) . '" class="button-primary" /></p>';
		wp_nonce_field( 'ims_new_packages' );
		echo '</form>';
	}

	/**
	 * Handle actions from pricing page
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function pricing_actions( ){

		if( empty( $_POST ) && empty( $_GET['delete'] ) )
			return;

		//clear cancel post data
		if ( isset( $_POST['cancel'] ) )
			wp_redirect( $this->pageurl );

		//new /update promotion
		if ( isset( $_POST['promotion'] ) ) {
			check_admin_referer( 'ims_promotion' );
			$errors = $this->add_promotion( );
		}

		///delete bulk promotion
		if ( isset( $_POST['doaction'] ) ) {
			check_admin_referer( 'ims_promotions' );
			if( isset( $_POST['action'] ) && $_POST['action'] == 'delete' )
				$errors = $this->delete_promotions( );
		}

		//delete promotion
		if ( isset( $_GET['delete'] ) && is_numeric( $_GET['delete'] ) ) {
			check_admin_referer( 'ims_link_promo' );
			$errors = $this->delete_promotions( );
		}

		//create new package
		if ( isset( $_POST['newpackage'] ) ) {
			check_admin_referer('ims_new_packages');
			$errors = $this->create_package( );
		}

		//update packages
		if ( isset( $_POST['updatepackage'] ) ) {
			check_admin_referer( 'ims_update_packages' );
			$errors = $this->update_package( );
		}

		//create new pricelist
		if ( isset( $_POST['newpricelist'] ) ) {
			check_admin_referer('ims_new_pricelist');
			$errors = $this->create_pricelist( );
		}

		//update list
		if ( isset( $_POST['updatelist'] ) ) {
			check_admin_referer( 'ims_pricelist' );
			$errors = $this->update_pricelist( );
		}

		//update shippping options
		if ( isset( $_POST['updateshipping'] ) ) {

			check_admin_referer( 'ims_shipping' );
			$shipping = isset( $_POST['shipping'] ) ? array_values( $_POST['shipping'] ) : array();

			update_option( 'ims_shipping_options', $shipping );
			wp_redirect( $this->pageurl . "&ms=43" );
			die( );
		}

		//update color options
		if ( isset( $_POST['updatecolors'] ) ) {

			check_admin_referer( 'ims_colors' );
			$colors = isset( $_POST['colors'] ) ? array_values( $_POST['colors'] ) : array();

			update_option( 'ims_color_options', $colors );
			wp_redirect( $this->pageurl . "&ms=42" );
			die( );
		}

		//update images sizes
		if ( isset( $_POST['updateimglist'] ) ) {

			check_admin_referer( 'ims_imagesizes' );
			$sizes = isset( $_POST['sizes'] ) ? array_values( $_POST['sizes'] ): array() ;

			update_option( 'ims_sizes', $sizes );
			wp_redirect( $this->pageurl . "&ms=37" );
			die( );
		}

		//update finishes
		if ( isset( $_POST['updatefinishes'] ) ) {

			check_admin_referer('ims_finishes');
			$finishes = isset( $_POST['finishes'] ) ? array_values( $_POST['finishes'] ) : array();

			update_option( 'ims_print_finishes', $finishes );
			wp_redirect( $this->pageurl . "&ms=44" );
			die( );
		}

		//update color filters
		if ( isset( $_POST['updatefilters'] ) ) {

			check_admin_referer( 'ims_filters' );
			$filters = isset( $_POST['filters'] ) ? array_values( $_POST['filters'] ) : array( ) ;

			$processed = array( );
			foreach( $filters as $filter ){
				if( empty( $filter['grayscale'] ) )
					$filter['grayscale'] = false;
				$processed[ $filter['code'] ] = $filter;
			}

			update_option( 'ims_color_filters', $processed );

			wp_redirect( $this->pageurl . "&ms=45" );
			die( );
		}

		//display error message
		if ( isset( $errors) && is_wp_error( $errors ) )
			$this->error_message( $errors );

	}

	/**
	 * Add/update promotions
	 *
	 * @return void | WP_error object
	 * @since 3.0.0
	 */
	function add_promotion( ) {

		$error = new WP_Error( );

		if ( empty( $_POST['promo_name'] ) )
			$error->add( 'empty_name', __( 'A promotion name is required.', 'image-store' ) );

		if ( empty( $_POST['discount'] ) && $_POST['promo_type'] != 3 )
			$error->add( 'discount', __( 'A discount is required', 'image-store' ) );

		if ( empty( $_POST['promo_code'] ) )
			$error->add( 'promo_code', __( 'A promotion code is required', 'image-store' ) );

		global $wpdb;
		$promo_id =  intval( $_POST['promotion_id'] );

		if ( $promo_id != $wpdb->get_var( $wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_value = %s AND meta_key = '_ims_promo_code'"
		, $_POST['promo_code'] ) ) )
			$error->add( 'discount', __( 'Promotion code is already in use', 'image-store' ) );

		if ( ! empty( $error->errors ) )
			return $error;

		$promotion = array(
			'ID' => $promo_id,
			'post_status' => 'publish',
			'post_type' => 'ims_promo',
			'post_date' => $_POST['start_date'],
			'post_date_gmt' => $_POST['start_date'],
			'post_title' => $_POST['promo_name'],
		);

		$promo_id = ( $promotion['ID'] ) ? wp_update_post( $promotion ) : wp_insert_post( $promotion );

		if ( empty( $promo_id ) ) {
			$error->add( 'promo_error', __(' There was a problem creating the promotion.', 'image-store' ) );
			return $error;
		}

		$defaults = array(
			'free-type' => false, 'discount' => false,
			'promo_code' => false, 'promo_type' => false,
			'items' => false, 'rules' => array( ), 'promo_limit' => false,
		);

		$data = wp_parse_args( $_POST, $defaults );
		$data = array_intersect_key( $data, $defaults );

		update_post_meta( $promo_id, '_ims_promo_data', $data );
		update_post_meta( $promo_id, '_ims_promo_code', $_POST['promo_code'] );
		update_post_meta( $promo_id, '_ims_post_expire', $_POST['expiration_date'] );

		do_action( 'ims_add_promotions', $promo_id );

		$a = ( $promotion['ID'] ) ? 30 : 32;
		wp_redirect( $this->pageurl . "&ms=$a#promotions" );

		die( );
	}

	/**
	 * delete promotions
	 *
	 * @return void | WP_error object
	 * @since 3.0.0
	 */
	function delete_promotions( ) {

		$errors = new WP_Error( );

		if ( empty( $_GET['delete'] ) && empty( $_POST['promo'] ) ) {
			$errors->add( 'nothing_checked', __( 'Please select a promo to be deleted.', 'image-store' ) );
			return $errors;
		}

		if( isset( $_GET['delete'] ) )
			$ids = ( array ) $_GET['delete'];
		else if ( isset( $_POST['promo'] ) )
			$ids = ( array ) $_POST['promo'];
		else return;

		global $wpdb;

		$ids = esc_sql( implode( ', ', $ids ) );

		if( $count = $wpdb->query( "DELETE FROM $wpdb->posts WHERE ID IN ($ids) " ) )
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id IN ($ids) " );

		do_action( 'ims_delete_promotions', $ids );

		$a = ( $count < 2 ) ? 31 : 39;
		wp_redirect( $this->pageurl . "&ms=$a&c=$count#promotions" );

		die( );
	}

	/**
	 * Create package
	 *
	 * @return void | WP_error object
	 * @since 3.0.0
	 */
	function create_package( ) {

		$errors = new WP_Error( );

		if ( empty( $_POST['package_name'] ) ) {
			$errors->add( 'empty_name', __('A name is required.', 'image-store' ) );
			return $errors;
		}

		$price_list = array(
			'post_status' => 'publish',
			'post_type' => 'ims_package',
			'post_title' => $_POST['package_name']
		);

		$list_id = wp_insert_post( $price_list );

		if ( empty( $list_id ) ) {
			$errors->add( 'list_error', __('There was a problem creating the package.', 'image-store') );
			return $errors;
		}

		do_action( 'ims_new_package', $list_id );

		wp_redirect( $this->pageurl . "&ms=35#packages" );
		die( );
	}

	/**
	 * Update package
	 *
	 * @return void | WP_error object
	 * @since 3.0.0
	 */
	function update_package( ) {

		if ( empty( $_POST['packageid'] ) )
			return;

		$errors = new WP_Error( );
		if ( empty( $_POST['packagename'] ) ) {
			$errors->add( 'empty_name', __('A name is required.', 'image-store' ) );
			return $errors;
		}

		$sizes = array( );
		if( isset( $_POST['packages'] ) ) {
			foreach ( ( array ) $_POST['packages'] as $size ) {
				$sizes[$size['name']]['unit'] = $size['unit'];
				$sizes[$size['name']]['count'] = $size['count'];
			}
		}

		$id = intval( $_POST['packageid'] );

		update_post_meta( $id, '_ims_sizes', $sizes );
		update_post_meta( $id, '_ims_price', $_POST['packageprice'] );
		$updated = wp_update_post( array( 'ID' => $id, 'post_title' => $_POST['packagename'] ) );

		do_action( 'ims_update_package', $updated );

		wp_redirect( $this->pageurl . "&ms=33#packages" );

		die( );
	}

	/**
	 * Create new price list
	 *
	 * @return void | WP_error object
	 * @since 3.0.0
	 */
	function create_pricelist( ) {
		$errors = new WP_Error( );

		if ( empty( $_POST['pricelist_name'] ) ) {
			$errors->add('empty_name', __('A name is required.', 'image-store'));
			return $errors;
		}

		$price_list = array(
			'post_status' => 'publish',
			'post_type' => 'ims_pricelist',
			'post_title' => $_POST['pricelist_name'],
		);

		$list_id = wp_insert_post( $price_list );

		if ( empty( $list_id ) ) {
			$errors->add( 'list_error', __( 'There was a problem creating the list.', 'image-store' ) );
			return $errors;
		}

		add_post_meta( $list_id, '_ims_list_opts', array( 'colors' => array(), 'finishes' => array() ) );

		do_action( 'ims_new_pricelist', $list_id );

		wp_redirect($this->pageurl . "&ms=38");
		die( );
	}

	/**
	 * Update list
	 *
	 * @return void | WP_error object
	 * @since 3.0.0
	 */
	function update_pricelist( ) {

		if ( empty( $_POST['listid'] ) )
			return;

		$errors = new WP_Error( );
		if ( empty( $_POST['list_name'] ) ) {
			$errors->add('empty_name', __('A name is required.', 'image-store'));
			return $errors;
		}

		$lisid = intval( $_POST['listid'] );
		$options = array( 'colors' => array( ), 'finishes' => array( ) );

		if( isset(  $_POST['colors'] ) )
			$options['colors'] = array_values( $_POST['colors'] );

		if( isset(  $_POST['finishes'] ) )
			$options['finishes'] = array_values( $_POST['finishes'] );

		$sizes = array( );
		if( isset( $_POST['sizes'] ) )
			$sizes = (array) $_POST['sizes'];

		update_post_meta( $lisid, '_ims_sizes', $sizes );
		update_post_meta( $lisid, '_ims_list_opts', $options );

		$data = array( 'ID' => $lisid, 'post_title' => false, 'post_excerpt' => false );

		if( isset(  $_POST['list_name'] ) )
			$data['post_title'] = $_POST['list_name'];

		if( isset(  $_POST['post_excerpt'] ) )
			$data['post_excerpt'] = $_POST['post_excerpt'];

		$updated = wp_update_post( $data );
		do_action( 'ims_update_pricelist', $updated );

		wp_redirect( $this->pageurl . "&ms=34" );
		die( );
	}

	/**
	 * Display image sizes metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function image_sizes( $ims, $args ) {
		$tabid = $args['args']['tabid'];
		?>
        <form method="post" action="<?php echo $this->pageurl."#{$tabid}"?>" >
            <table class="ims-table sizes-list">

                <thead>
                    <tr class="alternate">
                        <td>&nbsp;</td>
                        <td colspan="3" class="name"><?php _e( 'Name', 'image-store' )?></td>
                        <td class="price"><?php _e( 'Price', 'image-store' )?></td>
                        <td><?php _e( 'Width', 'image-store' )?></td>
                        <td><?php _e( 'Height', 'image-store' )?></td>
                        <td><?php _e( 'Unit', 'image-store' )?></td>
                        <td class="download">&nbsp;</td>
                      	<td class="x"><?php _e( 'Delete', 'image-store' )?></td>
                    </tr>
                </thead>

                <tbody>
                    <?php

					if( !$sizes = $this->get_option( 'ims_sizes') )
						$sizes = array( );

                    foreach( (array) $sizes as $key => $size ):
                        $key = str_replace( '|', '', $key );
                        $price = isset( $size['price'] ) ? $size['price'] : false;
                        $sizedata = isset( $size['w'] ) ? array( $size['w'], $size['h'] ) : explode( "x",strtolower( $size['name'] ) );
                    ?>
                    <tr class="size row alternate">
                        <td class="move" title="<?php _e( 'Move to list', 'image-store' )?>">&nbsp;</td>
                        <td colspan="3" class="name"><span class="hidden"><?php echo $size['name']?></span>
                        <span class="hidden"><?php echo esc_html( $this->units[ $size['unit'] ] )?></span>
                        <input type="text" name="sizes[<?php echo $key ?>][name]" class="name" value="<?php echo esc_attr( $size['name'] )?>" />
                        </td>

                        <td class="price">
                            <span class="hidden price"><?php echo $this->format_price( $price ) ?></span>
                            <input type="text" name="sizes[<?php echo $key ?>][count]" value="" class="count" />
                            <input type="text" name="sizes[<?php echo $key ?>][price]" value="<?php echo esc_attr( $price )?>" class="price" />
                        </td>

                        <td class="d"><input type="text" name="sizes[<?php echo $key ?>][w]" value="<?php echo esc_attr( $sizedata[0] )?>" /></td>
                        <td class="d"><input type="text" name="sizes[<?php echo $key ?>][h]" value="<?php echo esc_attr( $sizedata[1] )?>" /></td>

                        <td><?php $this->dropdown_units( "sizes[$key][unit]", $size['unit'] )?></td>

                        <td title="<?php _e( 'Check to make size downloadable', 'image-store' ) ?>" class="download">
                            <input type="checkbox" name="sizes[<?php echo $key ?>][download]" class="downloadable" value="1"  />
                        </td>

                        <td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
                    </tr><!--.row-->
                    <?php endforeach?>
                </tbody>

                <tfoot>
                    <tr class="copyrow" title="sizes">
                        <td>&nbsp;</td>
                        <td colspan="3"><input type="text" class="name"/></td>
                        <td><input type="text" class="price" /></td>
                        <td><input type="text" class="width" /></td>
                        <td><input type="text" class="height" /></td>
                        <td><?php $this->dropdown_units( '', '')?></td>
                        <td class="download"></td>
                        <td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
                    </tr>
                    <tr>
                        <td colspan="9"><small><?php _e( 'in:inches &bull; cm:centimeters &bull; px:pixels', 'image-store' )?></small></td>
                    </tr>
                    <tr class="addrow">
                        <td colspan="5" align="left">+ <a href="#" class="addimagesize"><?php _e( 'Add image size', 'image-store' ) ?></a></td>
                        <td colspan="4" align="right">
                            <input type="submit" name="updateimglist" value="<?php esc_attr_e( 'Update', 'image-store' )?>" class="button-primary" />
                        </td>
                    </tr>
                </tfoot>
            </table>

            <?php wp_nonce_field( 'ims_imagesizes' )?>
        </form>
        <?php
	}

	/**
	 * Display color options metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function color_options( ){
  ?>
  <form method="post" action="<?php echo esc_url( $this->pageurl . "#pricelists" ) ?>" >
    <table class="ims-table color-options">
      <thead>
        <tr class="alternate">
          <td>&nbsp;</td>
          <td colspan="3" class="name"><?php _e( 'Name', 'image-store' )?></td>
          <td colspan="2" class="price"><?php _e( 'Price', 'image-store' )?></td>
          <td><?php _e( 'Code', 'image-store' )?></td>
          <td class="x"><?php _e( 'Delete', 'image-store' )?></td>
        </tr>
      </thead>
      <tbody>
        <?php $colors = $this->get_option( 'ims_color_options' ) ?>
        <?php foreach ( (array) $colors as $key => $color) : ?>
          <tr class="color row alternate">
            <td class="move" title="<?php _e( 'Move to list', 'image-store' ) ?>">&nbsp;</td>
            <td colspan="3" class="name">
              <span class="hidden"><?php echo $color['name'] ?></span>
              <input type="text" name="colors[<?php echo esc_attr($key) ?>][name]" value="<?php echo esc_attr( $color['name'] ) ?>"  class="name" />
            </td>
            <td colspan="2" class="price">
              <span class="hidden"><?php echo $this->format_price( $color['price'] )?></span>
              <input type="text" name="colors[<?php echo esc_attr($key) ?>][price]" value="<?php echo esc_attr( $color['price'] ) ?>" class="price" />
            </td>
            <td class="code">
              <span class="hidden"><?php echo $color['code'] ?></span>
              <input type="text" name="colors[<?php echo esc_attr($key) ?>][code]" value="<?php echo esc_attr( $color['code'] ) ?>" class="code" />
            </td>
            <td class="x" title="<?php _e( 'Delete', 'image-store' ) ?>">x</td>
          </tr><!--.row-->
        <?php endforeach; ?>
      </tbody>

      <tfoot>
        <tr class="copyrow" title="colors">
          <td>&nbsp;</td>
          <td colspan="3" class="name"><input type="text" class="name"/></td>
          <td colspan="2" class="price"><input type="text" class="price" /></td>
          <td class="code"><input type="text" class="code" /></td>
          <td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
        </tr><!--.copyrow-->

        <tr class="addrow">
          <td colspan="4" align="left">+ <a href="#" class="addcoloropt"><?php _e( 'Add color option', 'image-store' )?></a></td>
          <td colspan="4" align="right">
              <input type="submit" name="updatecolors" value="<?php esc_attr_e( 'Update', 'image-store' )?>" class="button-primary" />
          </td>
        </tr>
      </tfoot>
    </table>
    <?php wp_nonce_field( 'ims_colors' ) ?>
  </form>
  <?php
	}

	/**
	 * Display shipping options metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function shipping_options( ){

	?>
	<form method="post" action="<?php echo $this->pageurl."#pricelists"?>" >
		<table class="ims-table shipping-options">
			<thead>
				<tr class="alternate">
				  <td colspan="3" class="name"><?php _e( 'Name', 'image-store' )?></td>
				  <td colspan="3" class="price"><?php _e( 'Price', 'image-store' )?></td>
				 	<td class="x"><?php _e( 'Delete', 'image-store' )?></td>
				</tr>
			</thead>

      <tbody>
      <?php if( !$options = $this->get_option( 'ims_shipping_options' ) )
				$options = array( );

				foreach ( (	array) $options as $key => $option ) : ?>
				<tr class="shipping row alternate">
					<td colspan="3" class="name">
					  <span class="hidden"><?php echo $option['name'] ?></span>
					  <input type="text" name="shipping[<?php echo esc_attr($key) ?>][name]" value="<?php echo esc_attr( $option['name'] ) ?>"  class="name" />
					</td>
					<td colspan="3" class="price">
					  <span class="hidden"><?php echo $this->format_price( $option['price'] ) ?></span>
					  <input type="text" name="shipping[<?php echo esc_attr($key) ?>][price]" value="<?php echo esc_attr( $option['price'] ) ?>" class="price" />
					</td>
					<td class="x" title="<?php _e( 'Delete', 'image-store' ) ?>">x</td>
				</tr><!--.row-->
      <?php endforeach; ?>
      </tbody>

			<tfoot>
				<tr class="copyrow" title="shipping">
					<td colspan="3" class="name"><input type="text" class="name"/></td>
					<td colspan="3" class="price"><input type="text" class="price" /></td>
					<td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
				</tr><!--.copyrow-->

				<tr class="addrow">
					<td colspan="3" align="left">+ <a href="#" class="addshipping"><?php _e( 'Add shipping option', 'image-store' )?></a></td>
					<td colspan="4" align="right">
						<input type="submit" name="updateshipping" value="<?php esc_attr_e( 'Update', 'image-store' )?>" class="button-primary" />
					</td>
				</tr>
			</tfoot>

		</table>
	<?php wp_nonce_field( 'ims_shipping' ) ?>
	</form>
	<?php
	}

	/**
	 * Display price lists metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function price_lists( ) {
    $default_list_id = $this->get_option( 'ims_pricelist' );
    ?>

    <p>
      <small>
      <?php _e( 'Add options by dragging image sizes or packages into the desired list.', 'image-store' ) ?>
      <?php _e( 'Check the box next to the price to make size downloadable, or image will have to be shipped.', 'image-store' ) ?>
      </small>
    </p>

    <?php foreach ( $this->get_pricelists( ) as $key => $list ) : ?>
      <?php  $meta = get_post_meta( $list->ID, '_ims_list_opts', true ); ?>
      <form method="post" id="ims-list-<?php echo $list->ID ?>" action="<?php echo esc_attr( $this->pageurl . "#pricelists" ) ?>" >
        <table class="ims-table pricelists">
          <thead>
            <tr class="bar">
              <?php if ( $list->ID == $default_list_id ): ?>
                <th class="default">
                  <input name="listid" type="hidden" class="listid" value="<?php echo esc_attr( $list->ID ) ?> " />
                </th>
              <?php else: ?>
                <th class="trash">
                  <a href="#">x</a>
                  <input type="hidden" name="listid" class="listid" value="<?php echo esc_attr( $list->ID ) ?>" />
                </th>
              <?php endif ?>
              <th colspan="3" class="itemtop inactive name">
                <label>
                  <span class="list-name"><?php echo esc_html($list->post_title) ?></span>
                  <input type="text" name="list_name" value="<?php echo esc_attr( $list->post_title ) ?>" class="regular-text" />
                </label>
              </th>
              <th colspan="3" class="itemtop plid"><?php echo 'ID: ' . $list->ID ?></th>
              <th class="itemtop toggle"><a href="#">[+]</a></th>
            </tr>
          </thead>

          <thead class="header content">
            <tr class="size row alternate">
              <td>&nbsp;</td>
              <td colspan="3" class="name" ><?php _e( 'Name', 'image-store' ) ?></td>
              <td class="price"><?php _e( 'Price', 'image-store' )?></td>
              <td></td>
              <td class="download"><?php _e( 'Download', 'image-store' )?></td>
              <td class="x"><?php _e( 'Delete', 'image-store' )?></td>
            </tr>
          </thead>

          <tbody class="sizes content">
            <?php if ( $sizes = get_post_meta( $list->ID, '_ims_sizes', true ) ) : ?>
              <?php unset( $sizes['random'] ); ?>
              <?php foreach ( $sizes as $key => $size ): ?>
                <?php if ( empty( $size['name'] ) ) continue; ?>
                <tr class="size row alternate">
                  <td class="move" title="<?php _e( 'Move', 'image-store' ) ?>">&nbsp;</td>
                  <td colspan="3" class="name" ><?php
                    if ( isset( $size['ID'] ) ) {

                      $package_sizes = '';
                      echo $size['name'] . ': ';

                      foreach ( ( array) get_post_meta( $size['ID'], '_ims_sizes', true ) as $package_size => $count ) {
                          if ( is_array( $count ) )
                              $package_sizes .= $package_size . ' ' . $count['unit'] . ' ( ' . $count['count'] . ' ), ';
                          else $package_sizes .= $package_size . ' ( ' . $count . ' ), ';
                      }
                      echo rtrim( $package_sizes, ', ' );

                    } else {

                      echo $size['name'];
                      if ( isset( $size['unit'] ) && isset( $this->units[$size['unit']] ) )
                          echo ' ' . $this->units[$size['unit']];
                      if ( isset( $size['download'] ) )
                          echo " <em>" . __( 'Downloadable.', 'image-store' ) . "</em>";
                    }
                  ?></td>
                  <td class="price">
                    <?php
                    if ( isset( $size['ID'] ) ) {
                      echo $this->format_price( get_post_meta( $size['ID'], '_ims_price', true ) );
                      ?>
                      <input type="hidden" name="sizes[<?php echo esc_attr($key) ?>][ID]" class="id" value="<?php echo esc_attr( $size['ID'] ) ?>"/>
                      <input type="hidden" name="sizes[<?php echo esc_attr($key) ?>][name]" class="name" value="<?php echo esc_attr( $size['name'] ) ?>"/><?php
                    } else {
                      echo $this->format_price($size['price']);
                      ?>
                      <input type="hidden" name="sizes[<?php echo esc_attr($key) ?>][name]" class="name"value="<?php echo esc_attr( $size['name'] ) ?>"/>
                      <input type="hidden" name="sizes[<?php echo esc_attr($key) ?>][price]" class="price" value="<?php echo esc_attr( $size['price'] ) ?>"/><?php
                    }
                    ?>
                  </td>
                  <td >
                  <?php if( isset( $size['unit'] ) ) : ?>
                      <input type="hidden" class="unit" name="sizes[<?php echo esc_attr($key) ?>][unit]" value="<?php echo esc_attr($size['unit']) ?>" />
                  <?php endif ?>
                  </td>
                  <td title="<?php _e('Check to make size downloadable', 'image-store') ?>" class="download">
                      <input type="checkbox" name="sizes[<?php echo $key ?>][download]" class="downloadable" value="1" <?php checked( true, isset( $size['download'] ) ) ?> />
                  </td>
                  <td class="x" title="<?php esc_attr_e( 'Delete', 'image-store' ) ?>">x</td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            <tr class="filler alternate"><td colspan="8"><?php _e( 'Add options by dragging image sizes here', 'image-store' ) ?></td></tr>
          </tbody>

          <tbody class="colors content">
            <tr class="header"> <th colspan="8"><?php _e('Colors', 'image-store') ?></td> </tr>
            <?php foreach ( (array) $meta['colors'] as $key => $color ) : ?>
              <tr class="color row alternate">
                <td class="move" title="<?php _e( 'Move', 'image-store' ) ?>">&nbsp;</td>
                <td colspan="3">
                  <?php echo $color['name'] ?>
                  <input type="text" name="colors[<?php echo esc_attr($key) ?>][name]" value="<?php echo esc_attr($color['name']) ?>" class="name" />
                </td>
                <td>
                  <?php echo $this->format_price( $color['price'] ) ?>
                  <input type="text" name="colors[<?php echo esc_attr($key) ?>][price]" value="<?php echo esc_attr($color['price'])?>" class="price" />
                </td>
                <td colspan="2">
                  <?php if( isset( $color['code'] ) ) : echo $color['code'] ?>
                  <input type="text" name="colors[<?php echo esc_attr($key) ?>][code]" value="<?php echo esc_attr($color['code']) ?>" class="code" />
                  <?php endif;?>
                </td>
                <td class="x" title="<?php esc_attr_e( 'Delete', 'image-store' ) ?>">x</td>
              </tr>
            <?php endforeach; ?>
            <tr class="filler alternate"><td colspan="8"><?php _e( 'Add options by dragging colors here', 'image-store' ) ?></td></tr>
          </tbody><!--.colors-->

          <tbody class="finishes content">
            <tr class="header"> <th colspan="8"><?php _e( 'Finishes', 'image-store' ) ?></td> </tr>
            <?php foreach ( (array) $meta['finishes'] as $key => $finish ): ?>
              <tr class="finish row alternate">
                <td class="move" title="<?php _e('Move', 'image-store') ?>">&nbsp;</td>
                <td colspan="3" class="name">
                  <span class="hidden"><?php echo $finish['name'] ?></span>
                  <input type="text" name="finishes[<?php echo esc_attr($key) ?>][name]" value="<?php echo esc_attr( $finish['name'] ) ?>" class="name" />
                </td>
                <td colspan="2" class="cost">
                  <span class="hidden"><?php echo ( $finish['type'] == 'percent' ) ? $finish['price']  : $this->format_price($finish['price']) ?></span>
                  <input type="text" name="finishes[<?php echo esc_attr($key) ?>][price]" value="<?php echo esc_attr($finish['price'] )?>" class="price">
                </td>
                <td class="type">
                  <span class="hidden"><?php echo $finish['type'] ?></span>
                  <select name="finishes[<?php echo $key ?>][type]" class="type">
                    <option value="amount" <?php selected( 'amount', $finish['type'] ) ?>><?php _e( 'Amount', 'image-store' ) ?></option>
                    <option value="percent" <?php selected( 'percent', $finish['type'] ) ?>><?php _e( 'Percent', 'image-store' ) ?></option>
                  </select>
                </td>
                <td class="x" title="<?php _e( 'Delete', 'image-store' ) ?>">x</td>
              </tr>
            <?php endforeach; ?>
            <tr class="filler alternate"><td colspan="8"><?php _e( 'Add options by dragging finishes here', 'image-store' ) ?></td></tr>
          </tbody><!--finishes-->

          <tfoot class="content">
            <tr><td colspan="8" align="right">
              <input type="hidden" name="size[random]" value="<?php echo rand( 0, 3000 )?>"/>
              <input type="submit" name="updatelist" value="<?php esc_attr_e( 'Update', 'image-store' )?>" class="button-primary" />
            </td></tr>
          </tfoot>
        </table>
        <?php wp_nonce_field( 'ims_pricelist' ) ?>
      </form>
    <?php endforeach; ?>
	<?php }


	/**
	 * Display finishes metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function print_finishes( ) {
		?>
	  <form method="post" action="<?php echo esc_url( $this->pageurl . "#pricelists" ) ?>" >
      <table class="ims-table print-finishes">

        <thead>
          <tr class="alternate">
            <td>&nbsp;</td>
            <td colspan="3" class="name"><?php _e( 'Name', 'image-store' )?></td>
            <td colspan="2" class="price"><?php _e( 'Price', 'image-store' )?></td>
            <td class="type"><?php _e( 'Type', 'image-store' )?></td>
            <td class="x"><?php _e( 'Delete', 'image-store' )?></td>
          </tr>
        </thead>

        <tbody>
        <?php foreach ( (array) $this->get_option( 'ims_print_finishes' ) as $key => $finish ) : ?>
          <tr class="finish row alternate">
            <td class="move" title="<?php _e( 'Move to list', 'image-store' ) ?>">&nbsp;</td>
            <td colspan="3" class="name">
              <span class="hidden"><?php echo $finish['name'] ?></span>
              <input type="text" name="finishes[<?php echo esc_attr($key) ?>][name]" value="<?php echo esc_attr( $finish['name'] ) ?>" class="name" />
            </td>
            <td colspan="2" class="price">
              <span class="hidden"><?php
               echo ( $finish['type'] == 'amount' ) ? $this->format_price($finish['price']) : $finish['price'] . "%";
               ?></span>
              <input type="text" name="finishes[<?php echo esc_attr($key) ?>][price]" value="<?php echo esc_attr( $finish['price'] ) ?>" class="price" />
            </td>
            <td class="type">
              <span class="hidden"><?php echo $finish['type'] ?></span>
              <select name="finishes[<?php echo $key ?>][type]" class="type">
                <option value="amount" <?php selected( 'amount', $finish['type'] ) ?>><?php _e( 'Amount', 'image-store' ) ?></option>
                <option value="percent" <?php selected( 'percent', $finish['type'])  ?>><?php _e( 'Percent', 'image-store' ) ?></option>
              </select>
            </td>
            <td class="x" title="<?php _e( 'Delete', 'image-store' ) ?>">x</td>
          </tr><!--.row-->
        <?php endforeach; ?>
        </tbody><!--.finish-->
        <tfoot>
            <tr class="copyrow" title="finishes">
              <td>&nbsp;</td>
              <td colspan="3"><input type="text" class="name" /></td>
              <td colspan="2"><input type="text" class="price" /></td>
              <td><select  class="type">
                  <option value="amount" <?php selected( 'amount', $finish['type'] ) ?>><?php _e( 'Amount', 'image-store' ) ?></option>
                  <option value="percent" <?php selected( 'percent', $finish['type'] ) ?>><?php _e( 'Percent', 'image-store' ) ?></option>
                </select></td>
              <td class="x" title="<?php _e('Delete', 'image-store') ?>">x</td>
            </tr><!--.copyrow-->
            <tr class="inforow"><td colspan="5"></td>&nbsp;</tr>
            <tr class="addrow">
              <td>&nbsp;</td>
              <td>+ <a href="#" class="addfinish"><?php _e( 'Add finish', 'image-store' ) ?></a></td>
              <td colspan="6" align="right">
                <input type="submit" name="updatefinishes" value="<?php esc_attr_e( 'Update', 'image-store' ) ?>" class="button-primary" />
              </td>
            </tr>
        </tfoot>
      </table>
	  <?php wp_nonce_field( 'ims_finishes' ) ?>
	  </form>
  <?php
	}

	/**
	 * Display pricelist pagackes metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function lists_packages( ) {
		?>
	<form method="post" action="<?php echo esc_url(  $this->pageurl . "#packages" ) ?>" >
		<table class="ims-table package-list">

        	<thead>
              <tr class="alternate">
                  <td>&nbsp;</td>
                  <td colspan="3" class="name"><?php _e( 'Name', 'image-store' )?></td>
                  <td class="price"><?php _e( 'Price', 'image-store' )?></td>
                  <td class="hidden">&nbsp;</td>
                  <td class="downloadable"><?php _e( 'Download', 'image-store' )?></td>
                  <td class="x"><?php _e( 'Delete', 'image-store' )?></td>
              </tr>
          </thead>

			<tbody>
			<?php foreach( $this->get_packages( ) as $key => $package ):?>
				<tr class="packages row alternate">
					<td class="move" title="<?php _e( 'Move to list', 'image-store' )?>">&nbsp;</td>
					<td colspan="3" class="name"><?php echo esc_html( $package->post_title )?>:
					<?php $sizes = '';
						foreach( (array) get_post_meta( $package->ID, '_ims_sizes', true ) as $size => $count){
							if( is_array( $count ) ) $sizes .= $size.' '.$count['unit'].' ( '.$count['count'].' ), ';
							else $sizes .= $size.' ( '.$count.' ), ';
						} echo rtrim($sizes, ', ' );
					?>
					</td>
					<td class="price">
						<?php echo $this->format_price( get_post_meta( $package->ID, '_ims_price', true ) )?>
						<input type="hidden" name="packages[][ID]" class="id" value="<?php echo esc_attr( $package->ID )?>"/>
						<input type="hidden" name="packages[][name]" class="name" value="<?php echo esc_attr( $package->post_title )?>"/>
					</td>
					<td class="hidden">&nbsp;</td>
					<td title="<?php _e( 'Check to make size downloadable', 'image-store' ) ?>" class="downloadable">
						<input type="checkbox" name="packages[<?php echo esc_attr($key)  ?>][download]" class="downloadable" value="1"  />
					</td>
					<td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
				</tr>
			<?php endforeach?>
			</tbody>
		</table>
	</form>
	<?php
	}

	/**
	 * Display color filters metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function color_filters( ){
	?>
		<form method="post" action="<?php echo esc_url( $this->pageurl."#pricelists") ?>" >
			<table class="ims-table color-filters">

				<thead>
					<tr class="alternate">
						<td class="name"><?php _e( 'Name', 'image-store' )?></td>
						<td class="code"><?php _e( 'Code', 'image-store' )?></td>
						<td class="contrast"><?php _e( 'Contrast', 'image-store' )?></td>
						<td class="brightness"><?php _e( 'Brightness', 'image-store' )?></td>
						<td class="colorize"><?php _e( 'Colorize(r,g,b,a)', 'image-store' )?></td>
						<td class="grayscale"><?php _e( 'Grayscale', 'image-store' )?></td>
						<td class="x"><?php _e( 'Delete', 'image-store' )?></td>
					</tr>
				</thead>

				<tbody>
				<?php foreach ( (array) $this->get_option('ims_color_filters') as $code => $filter ) : ?>
				<tr class="filters row alternate">
					<td class="name">
						<input type="text" name="filters[<?php echo esc_attr($code) ?>][name]" value="<?php echo esc_attr( $filter['name'] ) ?>" class="name" />
					</td>
					<td class="code">
						<input type="text" name="filters[<?php echo esc_attr($code) ?>][code]" value="<?php echo esc_attr( $filter['code'] ) ?>" class="code" />
					</td>
					<td class="contrast">
						<input type="text" name="filters[<?php echo esc_attr($code) ?>][contrast]" value="<?php echo esc_attr( $filter['contrast'] ) ?>" class="contrast" />
					</td>
					<td class="brightness">
						<input type="text" name="filters[<?php echo esc_attr($code) ?>][brightness]" value="<?php echo esc_attr( $filter['brightness'] ) ?>" class="brightness" />
					</td>
					<td class="colorize">
						<input type="text" name="filters[<?php echo esc_attr($code) ?>][colorize]" value="<?php echo esc_attr( $filter['colorize'] ) ?>" class="colorize" />
					</td>
					<td class="grayscale">
						<input type="checkbox" name="filters[<?php echo esc_attr($code) ?>][grayscale]" <?php checked( $filter['grayscale'], true ) ?>value="1" class="grayscale" />
					</td>
					<td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
				</tr><!--.row-->
				<?php endforeach; ?>
				</tbody>

				<tfoot>
					<tr class="copyrow" title="filters">
						<td class="name"><input type="text" class="name"/></td>
						<td class="code"><input type="text" class="code" /></td>
						<td class="contrast"><input type="text" class="contrast" /></td>
						<td class="brightness"><input type="text" class="brightness" /></td>
						<td class="colorize"><input type="text" class="colorize" /></td>
						<td class="grayscale"><input type="checkbox" value="1" class="grayscale" /></td>
						<td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
					</tr><!--.copyrow-->

					<tr class="addrow">
						<td colspan="4" align="left">+ <a href="#" class="addcolorfilter"><?php _e( 'Add a filter', 'image-store' )?></a></td>
						<td colspan="4" align="right">
							<input type="submit" name="updatefilters" value="<?php esc_attr_e( 'Update', 'image-store' )?>" class="button-primary" />
						</td>
					</tr>
				</tfoot>

			</table>
			<?php wp_nonce_field('ims_filters') ?>
		</form>
	<?php
	}

	/**
	 * Function  to display
	 * new price list metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function package_list( $ims, $args ) {
		$tabid = $args['args']['tabid'];
		?>
		<p><small><?php _e( 'Add options by dragging image sizes into the desired package.', 'image-store' )?></small></p>

		<?php foreach( $this->get_packages( ) as $key => $package ): ?>
			<?php $price = get_post_meta( $package->ID, '_ims_price', true ) ?>

            <form method="post" id="package-list-<?php echo $package->ID?>" action="<?php echo esc_attr($this->pageurl."#{$tabid}") ?>" >
                <table class="ims-table package-list">
                    <thead>
                        <tr class="bar">
                            <th class="trash">
                                <a href="#">x</a>
                                <input type="hidden" name="packageid" class="packageid" value="<?php echo esc_attr( $package->ID )?>" />
                            </th>
                            <th colspan="3" class="itemtop inactive">
                                <input type="text" name="packagename" value="<?php echo esc_attr( $package->post_title )?>" class="regular-text" />
                            </th>
                            <th><label><?php _e( 'Price', 'image-store' )?>
                                <input type="text" name="packageprice" value="<?php echo esc_attr( $price )?>" class="inputsm" /></label></th>
                            <th>&nbsp;</th>
                            <th class="itemtop toggle"><a href="#">[+]</a></th>
                        </tr>
                    </thead>

                    <tbody class="packages content">
                    <?php if( $sizes = get_post_meta( $package->ID, '_ims_sizes', true ) ) : ?>
											<?php foreach( $sizes as $size => $count ) : ?>
                            <?php if( is_numeric( $size ) ) continue;  ?>

                            <tr class="package row alternate">
                                <td class="move">&nbsp;</td>
                                <td colspan="3" class="pck-name"><?php echo "$size ".$count['unit']?></td>
                                <td class="price">
                                <?php $count_val = ( is_array( $count) ) ?  $count['count'] : $count ?>
                                <input type="hidden" name="packages[<?php echo esc_attr( $size ) ?>][name]" class="name" value="<?php echo esc_attr( $size )?>" />
                                <input type="text" name="packages[<?php echo esc_attr( $size ) ?>][count]" value="<?php
                                 echo esc_attr( $count_val ) ?>" class="count" title="<?php _e( 'Quantity', 'image-store' )?>" />
                                </td>
                                <td><input type="hidden" name="packages[<?php echo esc_attr( $size ) ?>][unit]" class="unit" value="<?php echo esc_attr( $count['unit'] )?>" /></td>
                                <td class="x" title="<?php _e( 'Delete', 'image-store' )?>">x</td>
                            </tr><!--.row-->

                        <?php endforeach ?>
                    <?php endif ?>

                        <tr class="filler"><td colspan="7"><?php _e( 'Add options by dragging image sizes here', 'image-store' )?></td></tr>
                    </tbody>
                    <tfoot class="content">
                        <tr>
                            <td colspan="7" align="right">
                                <input type="hidden" vname="packages[random]" alue="<?php echo rand(0,3000)?>"/>
                                <input type="submit" name="updatepackage" value="<?php esc_attr_e( 'Update', 'image-store' )?>" class="button-primary" />
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php wp_nonce_field( 'ims_update_packages' )?>
            </form>
            <?php
        endforeach;
	}

	/**
	 * Display new promotion metabox content
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function new_promotion( ) {

		if( empty( $_GET['iaction'] ) )
			return;

		$action = __( 'Add', 'image-store' );
		$promo_id =  ( int ) $_GET['iaction'];

		$defaults =  array(
			'promo_name' => false, 'promo_code' => false, 'starts' => false, 'startdate' =>false,
			'expires' => false, 'expiration_date' => false, 'promo_type' => 1, 'discount' =>false,
			'rules' => array( 'logic' => false, 'property' => false, 'value' => false), 'promo_limit' => false,
		);

		if( $promo_id ){
			$action = __( 'Update', 'image-store' );

			$promo 	= get_post( $promo_id );
			$data 	= get_post_meta( $promo_id, '_ims_promo_data', true );
			$date 	= strtotime( $promo->post_date );
			$expire	= strtotime( get_post_meta( $promo_id, '_ims_post_expire', true ) );

			$data['promo_name'] = $promo->post_title;
			$data['startdate'] = date_i18n( 'Y-m-d', $date );
			$data['starts'] = date_i18n( $this->dformat, $date );
			$data['expires'] = date_i18n( $this->dformat, $expire );
			$data['expiration_date'] = date_i18n( 'Y-m-d', $expire );

			$data = wp_parse_args( $_POST,  $data );
		} else $data = wp_parse_args( $_POST, $defaults );

		extract( $data );
		$disabled = ( $promo_type == 3 ) ? ' disabled="disabled"' : '';

		?>
		<form method="post" class="new-promo" action="#promotions" >
			<table class="ims-table">
				<tbody>

					<tr>
						<td colspan="6" align="right">&nbsp;</td>
					</tr>

					<tr class="selector">
						<td>
							<label><?php _e( 'Type', 'image-store' )?>
								<select name="promo_type" id="promo_type">
									<?php foreach( $this->promo_types as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ) ?>"<?php selected( $promo_type, $key )?>><?php echo esc_html( $label )?></option>
									<?php endforeach ?>
								</select>
							</label>
						</td>
						<td colspan="5">
							<?php _e( 'Conditions', 'image-store' )?>
							<select name="rules[property]">
								<?php foreach( $this->rules_property as $val => $label )
									echo '<option value="' . esc_attr( $val ) . '"' . selected( $rules['property'], $val, false ) . '>' . esc_html( $label ) . '</option>';
								?>
							</select>
							<select name="rules[logic]">
									<?php foreach( $this->rules_logic as $val => $label )
										echo '<option value="' . esc_attr( $val ) . '"' . selected( $rules['logic'], $val, false ) . '>' . esc_html( $label ). '</option>';
									?>
							</select>
							<input name="rules[value]" type="text" class="inpsm" value="<?php echo esc_attr( $rules['value'] ) ?>"/>
						</td>
					</tr>

					<tr>
						<td><label for="promo_name"><?php _e( 'Name','image-store' )?></label></td>
						<td><label for="promo_code"> <?php _e( 'Code','image-store' )?></label></td>
						<td><label for="starts"><?php _e( 'Starts','image-store' )?></label></td>
						<td><label for="expires"><?php _e( 'Expire','image-store' )?></label></td>
						<td><label class="hide-free"> <?php _e( 'Discount', 'image-store' )?></label></td>
						<td><label for="promo_limit"> <?php _e( 'Limit', 'image-store' )?></label></td>
					</tr>

					<tr>
						<td><input name="promo_name" type="text" id="promo_name" class="regular-text" value="<?php echo esc_attr( $promo_name ) ?>"/></td>
						<td><input name="promo_code" type="text" id="promo_code" class="regular-text" value="<?php echo esc_attr( $promo_code ) ?>" /></td>
						<td>
							<input name="starts" type="text" id="starts" class="regular-text" value="<?php echo esc_attr( $starts )?>" />
							<input name="start_date" type="hidden" id="start_date" value="<?php echo esc_attr($startdate)?>" />
						</td>
						<td>
							<input name="expires" type="text" id="expires" class="regular-text" value="<?php echo esc_attr( $expires )?>" />
							<input name="expiration_date" type="hidden" id="expiration_date" value="<?php echo esc_attr( $expiration_date ) ?>" />
						</td>
						<td><input name="discount" type="text" id="discount" class="regular-text hide-free" value="<?php echo esc_attr( $discount ) ?>"<?php echo $disabled ?>/> </td>
						<td><input name="promo_limit" type="text" id="promo_limit" class="regular-text" value="<?php echo esc_attr( $promo_limit ) ?>" /></td>
					</tr>

					<tr>
						<td colspan="6" align="right">
							<input type="hidden" name="promotion_id" value="<?php echo esc_attr( $promo_id ) ?>"/>
							<input type="submit" name="cancel" value="<?php esc_attr_e( 'Cancel', 'image-store' )?>" class="button-secondary" />
							<input type="submit" name="promotion" value="<?php echo esc_attr( $action )?>" class="button-primary" />
						</td>
					</tr>

				</tbody>
			</table>
			<?php wp_nonce_field( 'ims_promotion' )?>
		</form>
		<?php
	}

	/**
	 * Display promotions table metabox content
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function promotions_table( ) {

		$css			= ' alternate';
		$page		= ( isset( $_GET['p'] ) ) ? ( int ) $_GET['p'] : 1;
		$nonce 		= '_wpnonce='.wp_create_nonce( 'ims_link_promo' );
		$hidden 	= ( array ) get_hidden_columns( $this->screen );
		$columns	= ( array ) get_column_headers( $this->screen );
		$promos 	= new WP_Query( array( 'post_type' => 'ims_promo', 'paged' => $page, 'posts_per_page' => $this->per_page ) );

		$start = ( $page - 1 ) * $this->per_page;

		$page_links = paginate_links( array(
			'base' => $this->pageurl . '%_%#promotions',
			'format' => '&p=%#%',
			'prev_text' => __( '&laquo;' ),
			'next_text' => __( '&raquo;' ),
			'total' => $promos->max_num_pages,
			'current' => $page,
		) );

		?>
		<form method="post" action="#promotions" >

			<div class="tablenav">
				<div class="alignleft actions">
				<select name="action">
					<option value="" selected="selected"><?php _e( 'Bulk Actions', 'image-store' )?></option>
					<option value="delete"><?php _e( 'Delete', 'image-store' )?></option>
				</select>
				<input type="submit" value="<?php esc_attr_e( 'Apply', 'image-store' );?>" name="doaction" class="button" />
			</div><!--.actions-->
			<a href="<?php echo $this->pageurl ."&amp;iaction=new#promotions"?>" class="button-primary alignright"><?php _e( 'New Promotion', 'image-store' )?></a>
		</div><!--.tablenav-->

		<table class="widefat post fixed ims-table">
			<thead><tr><?php print_column_headers( $this->screen )?></tr></thead>
			<tbody>
				<tbody>
				<?php
				 foreach( $promos->posts as $promo ) :

					$css = ( $css == ' alternate' ) ? '' : ' alternate';
					$meta = get_post_meta( $promo->ID , '_ims_promo_data', true );

					$r = '<tr id="item-' . $promo->ID . '" class="iedit' . $css . '">';
					foreach( $columns as $column_id => $column_name ) :

						$hide = ( $this->in_array( $column_id, $hidden ) ) ? ' hidden' : '';
						switch( $column_id ){
							case 'cb':
								$r .= '<th class="column-' . $column_id . ' check-column">';
								$r .= '<input type="checkbox" name="promo[]" value="' . esc_attr( $promo->ID ) . '" /> </th>';
								break;
							case 'name':
								$r .= '<td class="column-' . $column_id . '" > ' . esc_html( $promo->post_title ) . '<div class="row-actions">' ;
								$r .= '<span><a href="' . esc_attr( $this->pageurl ) . "&amp;iaction={$promo->ID}#promotions" . '">' . __( "Edit", 'image-store' ) . '</a></span> |';
								$r .= '<span class="delete"><a href="' . esc_attr( $this->pageurl ) . "&amp;$nonce&amp;delete={$promo->ID}#promotions" . '"> ' . __( "Delete", 'image-store' ) . '</a></span>';
								$r .= '</div></td>';
								break;
							case 'code':
								$r .= '<td class="column-' . $column_id . $hide . '" > ' ;
								if( isset( $meta['promo_code'] ) ) $r .=  esc_html( $meta['promo_code'] );
								$r .= '</td>' ;
								break;
							case 'starts':
								$r .= '<td class="column-' . $column_id . $hide .'" > ' . date_i18n( $this->dformat, strtotime( $promo->post_date ) ) . '</td>' ;
								break;
							case 'expires':
								$r .= '<td class="column-' . $column_id . $hide . '" > ';
								if( $expires = get_post_meta( $promo->ID, '_ims_post_expire', true ) )
									$r .=  date_i18n( $this->dformat, strtotime( $expires ) );
								$r .= '</td>' ;
								break;
							case 'type':
								$r .= '<td class="column-' . $column_id . $hide . '" > ' ;
								if( isset( $meta['promo_type'] ) ) $r .= $this->promo_types[$meta['promo_type'] ] ;
								$r .= '</td>' ;
								break;
							case 'discount':
								$r .= '<td class="column-' . $column_id . $hide . '" > ' ;
								if( isset( $meta['discount'] ) ) $r .= esc_html( $meta['discount']);
								if( isset( $meta['items'] ) ) $r .= esc_html($meta['items']);
								$r .= '</td>' ;
								break;
							case 'limit':
								$r .= '<td class="column-' . $column_id . $hide . '" > ' ;
								if( isset( $meta['promo_limit'] ) ) $r .= esc_html($meta['promo_limit']);
								$r .= '</td>' ;
								break;
							case 'redeemed':
								$r .= '<td class="column-' . $column_id . $hide . '" > ' ;
								$r .= ( int ) get_post_meta( $promo->ID, '_ims_promo_count', true );
								$r .= '</td>' ;
								break;
						}

					endforeach;
					echo $r .= '</tr>';

				endforeach;
				?>
			</tbody>
		</table><!-- ims-table-->

      <div class="tablenav">
        <div class="tablenav-pages">
        <?php if( $page_links ) echo sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
            number_format_i18n( $start + 1 ),
            number_format_i18n( min( $page * $this->per_page, $promos->found_posts ) ),
            '<span class="total-type-count">' . number_format_i18n( $promos->found_posts ) . '</span>',
            $page_links
        ) ?>
        </div><!--.tablenav-pages-->
      </div><!--.tablenav-->
  		<?php wp_nonce_field( 'ims_promotions' )?>
		</form><!--#promotions-->
	<?php
	}
}
