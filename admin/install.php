<?php

/**
 * Image store - Install / Reset
 *
 * @file install.php
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2016
 * @filesource  wp-content/plugins/image-store/admin/install.php
 * @since 0.5.0
 */

// Stop direct access of the file
if ( !defined('ABSPATH') || !current_user_can( 'activate_plugins' ) )
	die( );

class ImStoreInstaller extends ImStore {

	/**
	 * Constructor
	 *
	 * @return void
	 * @since 0.5.0
	 */
	function __construct( ) {
		$this->ver = get_option('imstore_version');
		$this->userid = get_current_user_id( );
	}

	/**
	 * Default behavior
	 *
	 * @return void
	 * @since 3.0.3
	 */
	function init( ){

	 	if ( empty( $this->ver ) )
			$this->imstore_default_options( );

		if ( $this->version > $this->ver || empty( $this->ver ))
			$this->update( );

		if ( !get_option( 'ims_pricelist' ) )
			$this->price_lists( );

		do_action( 'ims_install' );

		//save imstore version
		update_option( 'imstore_version', $this->version );
	}


	/**
	 * Setup the default option array
	 * Create required categores
	 *
	 * @return void
	 * @since 0.5.0
	 */
	function imstore_default_options() {
		global $wpdb;

		$ims_ft_opts['album_per_page'] = false;
		$ims_ft_opts['album_template'] = 'page.php';
		$ims_ft_opts['album_slug'] = 'albums';
		$ims_ft_opts['album_level'] = false;
		$ims_ft_opts['autoStart'] = '';
		$ims_ft_opts['attchlink'] = false;

		$ims_ft_opts['bottommenu'] = false;

		$ims_ft_opts['clocal'] = '1';
		$ims_ft_opts['closeLinkText'] = __( 'Close', 'image-store' );
		$ims_ft_opts['columns'] = '3';
		$ims_ft_opts['currency'] = 'USD';

		$ims_ft_opts['deletefiles'] = '1';
		$ims_ft_opts['decimal'] = true;
		$ims_ft_opts['downloadlinks'] = false;

		$ims_ft_opts['emailreceipt'] = '1';
		$ims_ft_opts['favorites'] = true;
		$ims_ft_opts['loginform'] = true;

		$ims_ft_opts['galleriespath'] = '/_imsgalleries';
		$ims_ft_opts['gallery_template'] = false;
		$ims_ft_opts['gallery_slug'] = 'galleries';
		$ims_ft_opts['galleryexpire'] = '60';
		$ims_ft_opts['gateway_method'] = 'post';
		$ims_ft_opts['googleid'] = '';

		$ims_ft_opts['image_slug'] = 'ims-image';
		$ims_ft_opts['imgs_per_page'] = false;
		$ims_ft_opts['imgsortdirect'] = 'ASC';
		$ims_ft_opts['imgsortorder'] = 'menu_order';
		$ims_ft_opts['imswidget'] = false;

		$ims_ft_opts['maxPagesToShow'] = 5;
		$ims_ft_opts['mediarss'] = '1';

		$ims_ft_opts['nextLinkText'] = __('Next', 'image-store');
		$ims_ft_opts['nextPageLinkText'] = __('Next &rsaquo;', 'image-store');
		$ims_ft_opts['notifyemail'] = get_option('admin_email');
		$ims_ft_opts['notifymssg'] = sprintf(__("A new order was place at you image store at %s \n\nOrder number: %%order_number%% \nTo view the order details please login to your site at: %s \n\n%%instructions%%", 'image-store'), get_option('blogname'), wp_login_url());
		$ims_ft_opts['notifysubj'] = __('New purchase notification', 'image-store');
		$ims_ft_opts['numThumbs'] = 8;

		$ims_ft_opts['paypalname'] = false;
		$ims_ft_opts['pauseLinkTex'] = __('Pause', 'image-store');
		$ims_ft_opts['paymentname'] = 'Pay by check';
		$ims_ft_opts['playLinkText'] = __('Play', 'image-store');
		$ims_ft_opts['prevLinkText'] = __('Previous', 'image-store');
		$ims_ft_opts['prevPageLinkText'] = __('&lsaquo; Prev', 'image-store');

		$ims_ft_opts['pagseguroemail'] = false;
		$ims_ft_opts['pagsegurotoken'] = false;
		$ims_ft_opts['pagsegurotesturl'] = false;
		$ims_ft_opts['photos'] = true;

		$ims_ft_opts['receiptname'] =  'Image Store';
		$ims_ft_opts['receiptemail'] =  'imstore@' . $_SERVER['HTTP_HOST'];

		$ims_ft_opts['shipping'] = true;
		$ims_ft_opts['shippingmessage'] = '';
		$ims_ft_opts['slideshow'] = true;
		$ims_ft_opts['store'] = '1';
		$ims_ft_opts['sameasbilling'] = '1';
		$ims_ft_opts['securegalleries'] = '1';
		$ims_ft_opts['slideshowSpeed'] = 3200;
		$ims_ft_opts['stylesheet'] = '1';
		$ims_ft_opts['symbol'] = '$';
		$ims_ft_opts['swfupload'] = '1';

		$ims_ft_opts['taxamount'] = false;
		$ims_ft_opts['taxcountry'] = false;
		$ims_ft_opts['tag_per_page'] = false;
		$ims_ft_opts['tag_slug'] = 'ims-tags';
		$ims_ft_opts['tag_template'] = false;
		$ims_ft_opts['taxtype'] = 'percent';
		$ims_ft_opts['titleascaption'] = '1';
		$ims_ft_opts['termsconds'] = '';
		$ims_ft_opts['transitionTime'] = 1000;
		$ims_ft_opts['thankyoureceipt'] = sprintf(__("<h2>Thank You, %%customer_first%% %%customer_last%%</h2>\n Save the information bellow for your records. \n\nTotal payment: %%total%%\nTransaction number: %%order_number%%\n\nIf you have any question about your order please contact us at: %s", 'image-store'), get_option('admin_email'));

		$ims_ft_opts['voting_like'] = true;

		$ims_ft_opts['watermark'] = '0';
		$ims_ft_opts['watermark_color'] = 'ffffff';
		$ims_ft_opts['watermark_size'] = '12';
		$ims_ft_opts['watermark_text'] = get_option('blogname');
		$ims_ft_opts['watermark_trans'] = '90';
		$ims_ft_opts['widgettools'] = false;
		$ims_ft_opts['wplightbox'] = false;
		$ims_ft_opts['watermarktile'] = false;

		$ims_ft_opts['wepayaccesstoken'] = false;
		$ims_ft_opts['wepayclientid'] = false;
		$ims_ft_opts['wepayaccountid'] = false;
		$ims_ft_opts['wepayclientsecret'] = false;

		//dont change array order
		$ims_ft_opts['tags'] = array(
			__('/%total%/', 'image-store'),
			__('/%status%/', 'image-store'),
			__('/%gallery%/', 'image-store'),
			__('/%shipping%/', 'image-store'),
			__('/%order_number%/', 'image-store'),
			__('/%customer_last%/', 'image-store'),
			__('/%customer_first%/', 'image-store'),
			__('/%customer_email%/', 'image-store'),
			__('/%instructions%/', 'image-store'),
			__('/%items_count%/', 'image-store'),
			__('/%gallery_id%/', 'image-store'),
		);

		$ims_ft_opts['required_ims_zip'] = 1;
		$ims_ft_opts['required_user_email'] = 1;
		$ims_ft_opts['required_first_name'] = 1;
		$ims_ft_opts['required_ims_address'] = 1;

		$ims_ft_opts['required_ims_city'] = false;
		$ims_ft_opts['required_ims_state'] = false;
		$ims_ft_opts['required_last_name'] = false;
		$ims_ft_opts['required_ims_phone'] = false;

		$ims_ft_opts['checkoutfields'] = array(
			'ims_city' => __('City', 'image-store'),
			'ims_state' => __('State', 'image-store'),
			'user_email' => __('Email', 'image-store'),
			'ims_phone' => __('Phone', 'image-store'),
			'ims_address' => __('Address', 'image-store'),
			'ims_zip' => __('Zip Code', 'image-store'),
			'last_name' => __('Last Name', 'image-store'),
			'first_name' => __('First Name', 'image-store'),
		);

		//gateway array
		$ims_ft_opts['gateway'] = array(
			'paypalsand' => 1,
			'paypalprod' => false,
			'googlesand' => false,
			'googleprod' => false,
			'googleprod' => false,
			'enotification' => false,
			'wepaystage'=> false,
			'wepayprod' => false,
			'pagseguroprod'=> false,
			'pagsegurosand' => false,
			'sagepaydev' => false,
			'sagepay' => false,
			'custom' => false,
		);

		//add custom gateway tags
		$old = get_option($this->optionkey);
		if (isset($old['carttags']))
			$ims_ft_opts['carttags'] = $old['carttags'];

		//default image sizes
		$ims_dis_img['mini'] = array('name' => 'mini', 'w' => 70, 'h' => 60, 'q' => 95, 'crop' => 1);
		$ims_dis_img['preview'] = array('name' => 'preview', 'w' => 380, 'h' => 380, 'q' => 80, 'crop' => 0);

		//allow plugins to modify default options
		$ims_ft_opts = apply_filters('ims_default_opts', $ims_ft_opts);

		update_option('mini_crop', 1);
		update_option('mini_size_w', 70);
		update_option('mini_size_h', 60);

		update_option('preview_crop', 0);
		update_option('preview_size_w', 380);
		update_option('preview_size_h', 380);
		update_option('preview_size_q', 80);

		//display galleries on front-end
		update_option('ims_searchable', true);

		//save all options
		update_option('ims_dis_images', $ims_dis_img);

		//multisite support
		if (is_multisite( ) && $this->sync == true)
			update_site_option($this->optionkey, $ims_ft_opts);
		else
			update_option($this->optionkey, $ims_ft_opts);

		//allow plugins to stop table optimazation
		if ( $optimize = apply_filters( 'ims_optimize', true, 'install' ) )
			$wpdb->query("OPTIMIZE TABLE $wpdb->options, $wpdb->postmeta, $wpdb->posts, $wpdb->users, $wpdb->usermeta");
	}

	/**
	 * Set use permission/roles
	 * and add defult price list, image sizes
	 *
	 * @return void
	 * @since 2.0.0
	 */
	function update( ) {

		global $wpdb;
		wp_cache_flush();

		if ( $this->ver < "2.0.0" ) {
			$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key
			IN( 'ims_downloads', 'ims_download_max', '_ims_image_count', '_ims_customer' )");
			$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_ims_visits' WHERE meta_key = 'ims_visits'");
			$wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_ims_tracking' WHERE meta_key = 'ims_tracking'");
		}

		$ims_ft_opts = get_option( $this->optionkey );

		if ( $this->ver <= "2.0.8") {
			$ims_ft_opts['album_template'] = 'page.php';
			$ims_ft_opts['tags'][] = __('/%instructions%/', 'image-store');
		}

		if ($this->ver <= "3.0.0" || empty($ims_ft_opts['carttags'])) {
			$ims_ft_opts['gateway_method'] = 'post';
			$ims_ft_opts['carttags'] = array(
				__('%image_id%', 'image-store'),
				__('%image_name%', 'image-store'),
				__('%image_value%', 'image-store'),
				__('%image_color%', 'image-store'),
				__('%image_quantity%', 'image-store'),
				__('%image_download%', 'image-store'),
				__('%cart_id%', 'image-store'),
				__('%cart_tax%', 'image-store'),
				__('%cart_total%', 'image-store'),
				__('%cart_status%', 'image-store'),
				__('%cart_shipping%', 'image-store'),
				__('%cart_currency%', 'image-store'),
				__('%cart_subtotal%', 'image-store'),
				__('%cart_discount%', 'image-store'),
				__('%cart_discount_code%', 'image-store'),
				__('%cart_total_items%', 'image-store'),
			);
		}

		if ( $this->ver <= "3.1.0" ){

			$ims_ft_opts['googleid'] =  '';
			$ims_ft_opts['attchlink'] =  false;
			$ims_ft_opts['imswidget'] =  false;
			$ims_ft_opts['wplightbox'] =  false;
			$ims_ft_opts['widgettools'] =  false;
			$ims_ft_opts['gallery_template'] =  false;
			$ims_ft_opts['receiptname'] =  'Image Store';
			$ims_ft_opts['receiptemail'] =  'imstore@' . $_SERVER['HTTP_HOST'];

			if( !is_array( $ims_ft_opts['gateway'] ) ){
				$key = $ims_ft_opts['gateway'];
				$ims_ft_opts['gateway'] = array(
						'paypalsand' => 1,
						'paypalprod' => false,
						'googlesand' => false,
						'googleprod' => false,
						'googleprod' => false,
						'enotification' => false,
						'wepaystage'=> false,
						'wepayprod' => false,
						'pagseguroprod'=> false,
						'pagsegurosand' => false,
						'sagepaydev' => false,
						'sagepay' => false,
						'custom' => false,
				);
				$ims_ft_opts['gateway'][$key] = 1;
			}

			$this->ims_xtra_pricing_opts( );
			update_option( 'ims_site_url', get_option( 'siteurl' ) );
		}

		if ( $this->ver <= "3.1.5" ){
			$ims_ft_opts['tags'] = array(
				__('/%total%/', 'image-store'),
				__('/%status%/', 'image-store'),
				__('/%gallery%/', 'image-store'),
				__('/%shipping%/', 'image-store'),
				__('/%order_number%/', 'image-store'),
				__('/%customer_last%/', 'image-store'),
				__('/%customer_first%/', 'image-store'),
				__('/%customer_email%/', 'image-store'),
				__('/%instructions%/', 'image-store'),
			);
		}

		if ( $this->ver <= "3.2.0" ){

			$ims_ft_opts['tags'][] = __('/%items_count%/', 'image-store');
			$ims_ft_opts['gateway']['wepaystage'] = false;
			$ims_ft_opts['gateway']['wepayprod'] = false;
			$ims_ft_opts['wepayaccesstoken'] = false;
			$ims_ft_opts['wepayclientid'] = false;
			$ims_ft_opts['wepayclientsecret'] = false;

			delete_option( 'ims_gateways' );
		}

		if ( $this->ver <= "3.2.1" ){

			$ims_ft_opts['columns'] = '3';
			$ims_ft_opts['termsconds'] = '';
			$ims_ft_opts['taxcountry'] = false;
			$ims_ft_opts['taxamount'] = false;
			$ims_ft_opts['tag_slug'] = 'ims-tags';
			$ims_ft_opts['album_slug'] = 'albums';
			$ims_ft_opts['gallery_slug'] = 'galleries';
			$ims_ft_opts['image_slug'] = 'ims-image';

			$ims_ft_opts['tag_template'] = false;
			$ims_ft_opts['tag_per_page'] = false;
			$ims_ft_opts['album_per_page'] = false;

			$ims_ft_opts['paypalname'] = false;
			$ims_ft_opts['bottommenu'] = false;
			$ims_ft_opts['required_ims_city'] = false;
			$ims_ft_opts['required_ims_state'] = false;
			$ims_ft_opts['required_last_name'] = false;
			$ims_ft_opts['required_ims_phone'] = false;
		}

		if ( $this->ver <= "3.2.3" ){
			$ims_ft_opts['album_level'] = false;
		}

		if ( $this->ver <= "3.2.7" ){
			$ims_ft_opts['watermarktile'] = false;
		}

		if ( $this->ver <= "3.3.0" ){

			$ims_ft_opts['store'] = empty( $ims_ft_opts['disablestore'] ) ? true : false;
			$ims_ft_opts['photos'] = empty( $ims_ft_opts['hidephotos'] ) ? true : false;
			$ims_ft_opts['favorites'] = empty( $ims_ft_opts['hidefavorites'] ) ? true : false;
			$ims_ft_opts['voting_like'] = empty( $ims_ft_opts['disable_like'] ) ? true : false;
			$ims_ft_opts['slideshow'] = empty( $ims_ft_opts['hideslideshow'] ) ? true : false;
			$ims_ft_opts['decimal'] = empty( $ims_ft_opts['disable_decimal'] ) ? true : false;
			$ims_ft_opts['shipping'] = empty( $ims_ft_opts['disable_shipping'] ) ? true : false;

			$ims_ft_opts['titleascaption'] = true;
			$ims_ft_opts['shippingmessage'] = '';
		}

		if ( $this->ver <= "3.3.2" ){
			$ims_ft_opts['watermark_trans']  = round( abs( ( ( $ims_ft_opts['watermark_trans'] / 127) * 100 ) - 100 ));
			update_option( 'ims_cache_time', time(  ) );
		}

		if ( $this->ver <= "3.3.3" )
			$ims_ft_opts['loginform'] = true;

		if ( $this->ver <= "3.4" ){
			// remove post_expire database column
			$post  = get_posts( array( 'posts_per_page' => 1 ) );
			if( isset( $post[0]->post_expire ) ){
				$galleries = $wpdb->get_results(
					"Select ID, post_expire from $wpdb->posts where post_expire != '0000-00-00 00:00:00' AND post_type IN () LIMIT 1000"
				);
				foreach( $galleries as $gallery )
					update_post_meta( $gallery->ID, '_ims_post_expire',  $gallery->post_expire );
				$wpdb->query( "ALTER TABLE $wpdb->posts DROP post_expire" );
			}

			$ims_ft_opts['wepayaccountid'] = '';
		}

		if ( $this->ver <= "3.4.6" )
			$ims_ft_opts['downloadlinks'] = false;

		if ( $this->ver <= "3.5.1" ){
			if( ! isset( $ims_ft_opts['gateway']['sagepaydev'] ) )
				$ims_ft_opts['gateway']['sagepaydev'] = false;
		}

		if ( version_compare( $this->ver, "3.5.2", '<=' )){
			$ims_ft_opts['tags'][] =	__('/%gallery_id%/', 'image-store');
			update_option( $this->optionkey, $ims_ft_opts );
		}


		//add imstore capabilities
		$ims_caps = array(
			'read_sales' => __('Read sales', 'image-store'),
			'add_galleries' => __('Add galleries', 'image-store'),
			'change_pricing' => __('Change pricing', 'image-store'),
			'change_settings' => __('Change Settings', 'image-store'),
			'manage_galleries' => __('Manage galleries', 'image-store'),
			'manage_customers' => __('Manage Customers', 'image-store'),
			'change_permissions' => __('Change Permissions', 'image-store'),
		); $ims_caps = apply_filters( 'ims_user_caps', $ims_caps);


		//user options
		$ims_user_opts['swfupload'] = '1';
		$ims_user_opts['caplist'] = $ims_caps;
		update_option( 'ims_user_options', $ims_user_opts);

		//assign caps to adminstrator, if not, to the editor
		$role = get_role( 'administrator' );
		$role = ( empty( $role) ) ? get_role('editor') : $role;
		foreach ( $ims_caps as $cap => $capname )
			$role->add_cap( 'ims_' . $cap);

		//add core caps
		$core_caps = array(
			'publish_ims_gallerys',
			'read_ims_gallery',
			'read_private_ims_gallery',
			'edit_ims_gallery',
			'edit_ims_gallerys',
			'edit_others_ims_gallerys',
			'edit_published_ims_gallerys',
			'delete_ims_gallery',
			'delete_ims_gallerys',
			'delete_post_ims_gallery',
			'delete_posts_ims_gallery',
			'delete_private_ims_gallery',
			'delete_others_ims_gallerys',
			'delete_published_ims_gallery',
		); update_option( 'ims_core_caps', $core_caps );

		foreach ( $core_caps as $cap )
			$role->add_cap( $cap );

		//add capabilities to the customer role
		$customer = @get_role( $this->customer_role );

		if ( empty( $customer ) )
			add_role( $this->customer_role, 'Customer', array('read' => 1, 'ims_read_galleries' => 1));

		//save all ims options to be deleted when plugin is unstalled
		update_option( 'ims_options', array( 'ims_front_options', $this->optionkey, 'ims_back_options', 'ims_page_secure', 'ims_searchable',
		'ims_print_finishes','ims_shipping_options', 'ims_pricelist', 'ims_options', 'ims_page_galleries', 'ims_sizes', 'ims_image_key', 'ims_download_sizes',
		'ims_dis_images', 'ims_user_options', 'ims_site_url', 'ims_color_filters', 'ims_page_cart', 'ims_color_options','ims_gateways', 'ims_core_caps'));


		//create secure page
		$page_secure = get_option('ims_page_secure');
		if (empty($page_secure)) {
			$secure_data = array(
				'ID' => false,
				'post_type' => 'page',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'post_author' => $this->userid,
				'post_content' => '[image-store secure=1]',
				'post_title' =>__('Secure Images', 'image-store'),
				'post_excerpt' => ''
			);
			$page_secure = wp_insert_post($secure_data);
			if ($page_secure)
				update_option('ims_page_secure', $page_secure);
		}

		//create cart page
		$page_cart = get_option('ims_page_cart');
		if (empty($page_cart)) {
			$cart_data = array(
				'ID' => false,
				'post_type' => 'page',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'post_author' => $this->userid,
				'post_title' => __('Cart', 'image-store'),
				'post_content' => '[image-store cart=1]',
				'post_excerpt' => ''
			);
			$page_cart = wp_insert_post($cart_data);
			if ($page_cart)
				update_option('ims_page_cart', $page_cart);
		}

		//create galleries page
		$page_gal = get_option('ims_page_galleries');
		if (empty($page_gal)) {
			$gallery_data = array(
				'ID' => false,
				'post_title' => 'Image Store',
				'post_type' => 'page',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'post_author' => $this->userid,
				'post_content' => '[image-store]',
				'post_excerpt' => ''
			);
			$page_gal = wp_insert_post($gallery_data);
			if ($page_gal)
				update_option('ims_page_galleries', $page_gal);
		}
	}

	/**
	 * Setup default print finishes,
	 * color and shipping options
	 *
	 * @return void
	 * @since 3.1.0
	 */
	function ims_xtra_pricing_opts(){
		//finishes
		$sizes = array(
			array('name' => __( 'None', 'image-store'), 'price' => '0', 'type' => 'amount' ),
			array('name' => __( 'Matte', 'image-store'), 'price' => '2.95', 'type' => 'amount' ),
			array('name' => __( 'Glossy', 'image-store'), 'price' => '3.45', 'type' => 'amount' ),
			array('name' => __( 'Semi-gloss', 'image-store'), 'price' => '3', 'type' => 'percent' ),
			array('name' => __( 'Metallic', 'image-store'), 'price' => '2', 'type' => 'percent' ),
			array('name' => __( 'Lustre', 'image-store'), 'price' => '5', 'type' => 'percent' ),
			array('name' => __( 'Texture', 'image-store'), 'price' => '7', 'type' => 'percent' ),
		);
		update_option('ims_print_finishes', $sizes);

		//colors
		$colors = array(
			array( 'price' => '0', 'code' =>false, 'name' => __( 'Color', 'image-store')),
			array( 'price' => '1.00', 'code' =>'bw','name' => __( 'BW', 'image-store')),
			array( 'price' => '1.00', 'code' =>'sp','name' => __('Sepia', 'image-store')),
			array( 'price' => '1.00', 'code' =>'gn','name' => __('Green Tone', 'image-store'))
		);
		update_option('ims_color_options', $colors);

		//filters
		$filters = array(
			'bw' => array( 'code' =>'bw','name' => __('BW', 'image-store'),'grayscale'=>1, 'contrast'=>'', 'brightness'=>'+10','colorize'=>''),
			'sp' => array( 'code' =>'sp','name' => __('Sepia', 'image-store'),'grayscale'=>1,'contrast'=>'', 'brightness'=>'+5','colorize'=>'35,25,10'),
			'gn' => array( 'code' =>'gn','name' => __('Green Tone', 'image-store'),'grayscale'=>'', 'contrast'=>'-5','brightness'=>'','colorize'=>'93,111,38,80')
		);
		update_option('ims_color_filters', $filters);

		//shipping
		$shipping =  array(
			array( 'price' => '3.00', 'name' => __('Local', 'image-store')),
			array( 'price' => '20.00', 'name' => __('International', 'image-store')),
		);
		update_option('ims_shipping_options', $shipping);
	}

	/**
	 * Setup the default price and lists
	 * and image sizes
	 *
	 * @return void
	 * @since 2.0.0
	 */
	function price_lists() {

		// default image sizes
		$sizes = array(
			array('name' => '4x6', 'price' => '4.95', 'unit' => 'in', 'type' => 'p'),
			array('name' => '8x10', 'price' => '15.90', 'unit' => 'in', 'type' => 'p'),
			array('name' => '11x14', 'price' => '25.90', 'unit' => 'in', 'type' => 'p'),
			array('name' => '16X20', 'price' => '64.75', 'unit' => 'in', 'type' => 'p'),
			array('name' => '20x24', 'price' => '88.30', 'unit' => 'in', 'type' => 'p'),
			array('name' => '2.5x3.5', 'price' => '1.25', 'unit' => 'in', 'type' => 'p'),
			array('name' => '600x600', 'w' => 600, 'h' => 600, 'price' => '1.25', 'unit' => 'px', 'type' => 'd'),
		);
		update_option('ims_sizes', $sizes);


		// price list
		$price_list = array(
			'ID' => false,
			'post_status' => 'publish',
			'post_type' => 'ims_pricelist',
			'post_title' => __('Default Price List', 'image-store'),
			'sizes' => array(
				array('name' => '4x6', 'price' => '4.95', 'unit' => 'in', 'type' => 'p'),
				array('name' => '8x10', 'price' => '15.90', 'unit' => 'in', 'type' => 'p'),
				array('name' => '11x14', 'price' => '25.90', 'unit' => 'in', 'type' => 'p'),
				array('name' => '16X20', 'price' => '64.75', 'unit' => 'in', 'type' => 'p'),
				array('name' => '20x24', 'price' => '88.30', 'unit' => 'in', 'type' => 'p'),
				array('name' => '600x600', 'w' => 600, 'h' => 600, 'price' => '1.25', 'unit' => 'px', 'type' => 'd'),
			),

			//list options
			'options' => array(
				'finishes' => array( ),
				//colors
				'colors' =>array(
					array( 'price' => '0', 'name' => __( 'Color', 'image-store'),  'code' => false ),
					array( 'price' => '1.00', 'name' => __( 'BW', 'image-store'),  'code' =>'bw' ),
					array( 'price' =>  '1.00', 'name' => __('Sepia', 'image-store'), 'code' =>'sp' ),
				),
			));


		// packages
		$packages = array(
			array('ID' => false, 'post_title' => __('Package 1', 'image-store'), 'post_type' => 'ims_package', 'post_status' => 'publish',
				'_ims_price' => '35.00', '_ims_sizes' => array(
					'8x10' => array('unit' => 'in', 'count' => 1),
					'5x7' => array('unit' => 'in', 'count' => 1),
					'2.5x3.5' => array('unit' => 'in', 'count' => '8'))
			),
			array('ID' => false, 'post_title' => __('Package 2', 'image-store'), 'post_type' => 'ims_package', 'post_status' => 'publish',
				'_ims_price' => '47.10', '_ims_sizes' => array(
					'8x10' => array('unit' => 'in', 'count' => 1),
					'5x7' => array('unit' => 'in', 'count' => 2),
					'2.5x3.5' => array('unit' => 'in', 'count' => 16))
			),
			array('ID' => false, 'post_title' => __('Package 3', 'image-store'), 'post_type' => 'ims_package', 'post_status' => 'publish',
				'_ims_price' => '58.85', '_ims_sizes' => array(
					'8x10' => array('unit' => 'in', 'count' => 2),
					'5x7' => array('unit' => 'in', 'count' => 2),
					'2.5x3.5' => array('unit' => 'in', 'count' => 16))
			),
			array('ID' => false, 'post_title' => __('Wallets', 'image-store'), 'post_type' => 'ims_package', 'post_status' => 'publish',
				'_ims_price' => '15.90', '_ims_sizes' => array('2.5x3.5' => array('unit' => 'in', 'count' => 8)))
		);

		//fix insallation headers already sent wit multisites
		if (is_multisite()) {
			global $wp_post_types;
			$obj = new stdClass();
			$obj->name = false;
			$obj->public = false;
			$obj->_builtin = false;
			$obj->hierarchical = false;
			$wp_post_types['ims_pricelist'] = $obj;
			$wp_post_types['ims_package'] = $obj;
		}

		//update package information
		foreach ($packages as $package) {
			$package_id = wp_insert_post($package);
			if (!$package_id)
				continue;
			$price_list['sizes'][] = array('ID' => $package_id, 'name' => $package['post_title']);
			update_post_meta($package_id, '_ims_price', $package['_ims_price']);
			update_post_meta($package_id, '_ims_sizes', $package['_ims_sizes']);
		}

		$price_list = apply_filters('ims_default_pricelists', $price_list);

		$list_id = wp_insert_post($price_list);
		update_option('ims_pricelist', $list_id);

		if (empty($list_id))
			return;

		update_post_meta($list_id, '_ims_list_opts', $price_list['options']);
		update_post_meta($list_id, '_ims_sizes', $price_list['sizes']);

		if (is_multisite()) {
			unset($wp_post_types['ims_pricelist']);
			unset($wp_post_types['ims_package']);
		}
	}

	/**
	 * Uninstall all settings
	 *
	 * @return void
	 * @since 0.5.0
	 */
	function imstore_uninstall() {
		global $wpdb, $wp_rewrite;

		if (!current_user_can('edit_plugins') || !current_user_can('ims_change_settings'))
			return;

		do_action('ims_before_uninstall');

		//remove scheduled_hook for expire galleries
		wp_clear_scheduled_hook('ims_expire');

		//delete manager pages
		wp_delete_post(get_option('ims_page_cart'), true);
		wp_delete_post(get_option('ims_page_secure'), true);
		wp_delete_post(get_option('ims_page_galleries'), true);

		//delete database version
		delete_option('imstore_version');

		//delete image sizes
		delete_option('mini_crop');
		delete_option('mini_size_w');
		delete_option('mini_size_h');
		delete_option('mini_size_q');

		delete_option('preview_crop');
		delete_option('preview_size_w');
		delete_option('preview_size_h');
		delete_option('preview_size_q');

		//Remove capabilities from user roles
		$role = get_role('administrator');
		$userops = get_option('ims_user_options');
		$role = ( empty($role) ) ? get_role('editor') : $role;
		foreach ( $userops['caplist'] as $cap => $capname )
			$role->remove_cap( 'ims_' . $cap );

		//remove core capabilities
		$core_caps = ( array) get_option( 'ims_core_caps', true );
		foreach ( $core_caps as $cap ) $role->remove_cap( $cap );

		//remove all options
		$ims_ops = get_option('ims_options');
		foreach ((array) $ims_ops as $ims_op)
			delete_option($ims_op);

		//deactivate plugin
		if( get_site_option( 'ims_sync_settings' ) ) {
			update_site_option( 'ims_sync_settings', false );
			deactivate_sitewide_plugin( IMSTORE_FILE_NAME );
		}

		$networkadmin = function_exists( 'is_network_admin' ) ? is_network_admin( ) : false;
		deactivate_plugins( IMSTORE_FILE_NAME, false, $networkadmin );

		//delete posts/galleries/pricelist/reports
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_type IN( 'ims_package', 'ims_pricelist', 'ims_gallery', 'ims_order', 'ims_promo', 'ims_image' )");

		//hand over the images to wp media gallery
		//$wpdb->query("UPDATE $wpdb->posts SET post_type = 'attachment', post_parent = 0, post_status = 'inherit' WHERE post_type IN( 'ims_image' )");

		//delete post metadata
		$wpdb->query("
			DELETE FROM $wpdb->postmeta WHERE meta_key
			IN( '_ims_list_opts', '_ims_sizes', '_ims_price', '_ims_folder_path', '_ims_price_list', '_ims_gallery_id', '_ims_sortby',
				 '_ims_order', '_ims_customer', '_ims_image_count', 'ims_download_max', '_ims_tracking', '_ims_visits', '_ims_promo_count',
				 'ims_downloads', '_ims_favorites', '_ims_order_data', '_ims_promo_data', '_ims_promo_code', '_response_data',
				 'ims_visits', 'ims_tracking', '_ims_downloads', '_dis_store', '_to_attach', '_user_download', '_ims_email_sent', '_ims_full_gallery'
			 ) "
		);

		//delete user metadata
		$wpdb->query(
			"DELETE FROM $wpdb->usermeta WHERE meta_key
			 IN( 'ims_user_caps', 'ims_customers_per_page', 'ims_galleries_per_page', 'ims_address', 'ims_sales_per_page',
				 'ims_city', 'ims_phone', 'ims_state', 'ims_zip', '_ims_favorites', 'ims_status', 'ims_info', 'ims_company', '_ims_image_like'
			 )"
		);

		//optomize wp tables
		if ( $optimize = apply_filters( 'ims_optimize', true, 'uninstall' ) )
			$wpdb->query("OPTIMIZE TABLE $wpdb->options, $wpdb->postmeta, $wpdb->posts, $wpdb->users, $wpdb->usermeta");

		//destroy active cookies
		setcookie('ims_orderid_' . COOKIEHASH, ' ', ( time( ) - 31536000), COOKIEPATH, COOKIE_DOMAIN);
		setcookie('imstore_galleryid' . COOKIEHASH, ' ', ( time( ) - 31536000), COOKIEPATH, COOKIE_DOMAIN);

		//clean rewrite rules
		$wp_rewrite->flush_rules( );

		do_action('ims_after_uninstall');

		//redirect user
		if ( is_multisite( ) && current_user_can( 'manage_network' )
		&& is_plugin_active_for_network( IMSTORE_FILE_NAME ) )
			wp_redirect( network_admin_url( 'plugins.php?deactivate=true' ) );
		else wp_redirect( admin_url( 'plugins.php?deactivate=true' ) );
		die( );
	}
}
