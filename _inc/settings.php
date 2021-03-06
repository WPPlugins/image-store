<?php

/**
 * Image Store - admin settings
 *
 * @file settings.php
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2016
 * @filesource  wp-content/plugins/image-store/_inc/settings.php
 * @since 3.2.1
 */
 
class ImStoreSet extends ImStoreAdmin {
	
	/**
	 * Constructor
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function ImStoreSet( $page, $action ) {
		
		$this->ImStoreAdmin( $page, $action );
		
		//speed up wordpress load
		if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) || SHORTINIT )
			return;
		
		add_filter( 'ims_settings_tabs', array( &$this, 'settings_tabs' ), 2 );
		
		add_action( 'admin_init', array( &$this, 'save_settings' ), 10 );
		add_action( 'ims_settings', array( &$this, 'watermark_location'), 2 );
		add_action( 'ims_setting_fields', array( &$this, 'show_user_caps' ), 11 );
		add_action( 'ims_setting_fields', array( &$this, 'dynamic_settings' ), 12 );
		add_action( 'ims_setting_fields', array( &$this, 'add_gateway_fields' ), 10 );
		
		//script styles
		add_action( 'admin_print_styles', array( &$this, 'load_settings_styles' ), 1 );
	}
	
	/**
	 * Load admin styles
	 *
	 * @return void
	 * @since 3.2.1
	 */
	function load_settings_styles( ) {
		wp_enqueue_style( 'ims-settings', IMSTORE_URL . '/_css/settings.css', false, $this->version, 'all' );
	}
	
	/**
	 * Disable tabs if store is disable
	 *
	 * @return array
	 * @since 3.2.1
	 */
	function settings_tabs( $tabs ){
		
		global $blog_id;			
		if ( is_plugin_active_for_network( IMSTORE_FILE_NAME ) 
			|| ( $this->sync && $blog_id != 1 ) )
			unset( $tabs['reset'] );
		
		if( $this->opts['store'] )
			return $tabs;
		
		foreach(array( 'payment', 'checkout', ) as $name )
			unset( $tabs[$name] );
			
		return $tabs;
	}
	
	/**
	 * Show settings base on option
	 *
	 * @return array()
	 * @since 3.3.0
	 */
	function dynamic_settings( $settings ){
		
		if( empty( $this->opts['emailreceipt']) ){
			unset( $settings['checkout']['receiptname'] );
			unset( $settings['checkout']['receiptemail'] );
			unset( $settings['checkout']['thankyoureceipt'] );
		}
		
		if( $this->opts['watermark'] == 0 && isset( $settings['image']['watermark_']) )
			unset( $settings['image']['watermark_'] );
		
		if( $this->opts['watermark'] == 2 && isset( $settings['image']['watermark_']['opts']) ){
			unset( $settings['image']['watermark_']['opts']['text'] );
			unset( $settings['image']['watermark_']['opts']['color'] );
			unset( $settings['image']['watermark_']['opts']['size'] );
		}
		
		if( $this->opts['watermark'] != 2 && isset( $settings['image']['watermarkurl'] ) )
			unset( $settings['image']['watermarkurl'] );
		
		if( $this->opts['watermark'] == 0 && isset( $settings['image']['watermarktile'] ) )
			unset( $settings['image']['watermarktile'] );
		
		return $settings;	
	}
	
	/**
	 * Add watermark location option
	 *
	 * @return void
	 * @since 3.0.3
	 */
	function watermark_location($boxid) {
		if ( $boxid != 'image' )
			return;
		
		if( $this->opts['watermarktile'] || !$this->opts['watermark'] )
			return;
		
		$option = $this->get_option('ims_wlocal');
		$wlocal = empty($option) ? 5 : $option;

		echo '<tr class="row-wlocal" valign="top"><td><label>' . __('Watermark location', 'image-store') . '</label></td><td>';
		echo '<div class="row">
			<label><input name="wlocal" type="radio" value="1" ' . checked(1, $wlocal, false) . ' /></label>
			<label><input name="wlocal" type="radio" value="2" ' . checked(2, $wlocal, false) . '/></label>
			<label><input name="wlocal" type="radio" value="3" ' . checked(3, $wlocal, false) . '/></label>
			</div>';
		echo '<div class="row">
			<label><input name="wlocal" type="radio" value="4" ' . checked(4, $wlocal, false) . '/></label>
			<label><input name="wlocal" type="radio" value="5" ' . checked(5, $wlocal, false) . '/></label>
			<label><input name="wlocal" type="radio" value="6" ' . checked(6, $wlocal, false) . '/></label>
			</div>';
		echo '<div class="row">
			<label><input name="wlocal" type="radio" value="7" ' . checked(7, $wlocal, false) . '/></label>
			<label><input name="wlocal" type="radio" value="8" ' . checked(8, $wlocal, false) . '/></label>
			<label><input name="wlocal" type="radio" value="9" ' . checked(9, $wlocal, false) . '/></label>
			</div>';
		echo '</td></tr>';
	}
	
	/**
	 * Add fields base on 
	 * wagateway selected
	 *
	 * @param array $settings
	 * @return array
	 * @since 3.2.1
	 */
	function add_gateway_fields( $settings ){
		
		//enotification
		if ( $this->opts['gateway']['enotification'] ) {
			$settings['payment']['shippingmessage'] = array(
				'val' => '',
				'type' => 'textarea',
				'label' => __('Shipping Message', 'image-store'),
			);
			$settings['payment']['required_'] = array(
				'multi' => true,
				'label' => __('Required Fields', 'image-store'),
			);
			
			foreach ((array) $this->opts['checkoutfields'] as $key => $label)
				$settings['payment']['required_']['opts'][$key] = array( 'val' => 1, 'label' => $label, 'type' => 'checkbox' );
			
			$settings['payment']['currency']['opts'][] = __('---- eNotification only ----', 'image-store');
			$settings['payment']['currency']['opts']['ARS'] = __('Argentina Peso', 'image-store');
			$settings['payment']['currency']['opts']['CLP'] = __('Chile Peso', 'image-store');
			$settings['payment']['currency']['opts']['INR'] = __('Indian Rupee', 'image-store');
			$settings['payment']['currency']['opts']['VND'] = __('Vietnam Dong', 'image-store');
		}
		
		//wepay
		if ($this->opts['gateway']['wepaystage']
		|| $this->opts['gateway']['wepayprod']) {
			$settings['payment']['wepayclientid'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Client ID', 'image-store'),
			);
			$settings['payment']['wepayclientsecret'] = array(
				'val' => '',
				'type' => 'password',
				'label' => __('Client Secret', 'image-store'),
			);
			$settings['payment']['wepayaccesstoken'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Access Token', 'image-store'),
			);
			$settings['payment']['wepayaccountid'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Account ID', 'image-store'),
			);
		}

		//paypal
		if ( $this->opts['gateway']['paypalsand']
		|| $this->opts['gateway']['paypalprod'] ) {
			$settings['payment']['paypalname'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('PayPal Account E-mail', 'image-store'),
			);
		}
		
		//google	
		if( $this->opts['gateway']['googlesand'] 
		|| $this->opts['gateway']['googleprod'] ){
			$settings['payment']['googleid'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Google merchant ID', 'image-store'),
			);
			$settings['payment']['googlekey'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Google Merchant key', 'image-store'),
			);
		}
		
		if( $this->opts['gateway']['googlesand'] 
		|| $this->opts['gateway']['googleprod']
		|| $this->opts['gateway']['sagepay'] 
		|| $this->opts['gateway']['sagepaydev'] ){
		
		$settings['payment']['taxcountry'] = array(
			'val' => '',
			'type' => 'select',
			'label' => __( 'Country', 'image-store' ),
			'opts' => array( 
				"GB" => "United Kingdom",
    			"AF" => "Afghanistan",
    			"AX" => "Aland Islands",
    			"AL" => "Albania",
    			"DZ" => "Algeria",
    			"AS" => "American Samoa",
    			"AD" => "Andorra",
    			"AO" => "Angola",
    			"AI" => "Anguilla",
    			"AQ" => "Antarctica",
    			"AG" => "Antigua and Barbuda",
    			"AR" => "Argentina",
    			"AM" => "Armenia",
    			"AW" => "Aruba",
    			"AU" => "Australia",
    			"AT" => "Austria",
    			"AZ" => "Azerbaijan",
    			"BS" => "Bahamas",
    			"BH" => "Bahrain",
    			"BD" => "Bangladesh",
    			"BB" => "Barbados",
    			"BY" => "Belarus",
    			"BE" => "Belgium",
    			"BZ" => "Belize",
    			"BJ" => "Benin",
    			"BM" => "Bermuda",
    			"BT" => "Bhutan",
    			"BO" => "Bolivia",
    			"BA" => "Bosnia and Herzegovina",
    			"BW" => "Botswana",
    			"BV" => "Bouvet Island",
    			"BR" => "Brazil",
    			"IO" => "British Indian Ocean Territory",
    			"BN" => "Brunei Darussalam",
    			"BG" => "Bulgaria",
    			"BF" => "Burkina Faso",
    			"BI" => "Burundi",
    			"KH" => "Cambodia",
    			"CM" => "Cameroon",
    			"CA" => "Canada",
    			"CV" => "Cape Verde",
    			"KY" => "Cayman Islands",
    			"CF" => "Central African Republic",
    			"TD" => "Chad",
    			"CL" => "Chile",
    			"CN" => "China",
    			"CX" => "Christmas Island",
    			"CC" => "Cocos (Keeling) Islands",
    			"CO" => "Colombia",
    			"KM" => "Comoros",
    			"CG" => "Congo",
    			"CD" => "Congo, The Democratic Republic of the",
    			"CK" => "Cook Islands",
    			"CR" => "Costa Rica",
    			"CI" => "Côte d'Ivoire",
    			"HR" => "Croatia",
    			"CU" => "Cuba",
    			"CY" => "Cyprus",
    			"CZ" => "Czech Republic",
    			"DK" => "Denmark",
    			"DJ" => "Djibouti",
    			"DM" => "Dominica",
    			"DO" => "Dominican Republic",
    			"EC" => "Ecuador",
    			"EG" => "Egypt",
    			"SV" => "El Salvador",
    			"GQ" => "Equatorial Guinea",
    			"ER" => "Eritrea",
    			"EE" => "Estonia",
    			"ET" => "Ethiopia",
    			"FK" => "Falkland Islands (Malvinas)",
    			"FO" => "Faroe Islands",
    			"FJ" => "Fiji",
    			"FI" => "Finland",
    			"FR" => "France",
    			"GF" => "French Guiana",
    			"PF" => "French Polynesia",
    			"TF" => "French Southern Territories",
    			"GA" => "Gabon",
    			"GM" => "Gambia",
    			"GE" => "Georgia",
    			"DE" => "Germany",
    			"GH" => "Ghana",
    			"GI" => "Gibraltar",
    			"GR" => "Greece",
    			"GL" => "Greenland",
    			"GD" => "Grenada",
    			"GP" => "Guadeloupe",
    			"GU" => "Guam",
    			"GT" => "Guatemala",
    			"GG" => "Guernsey",
    			"GN" => "Guinea",
    			"GW" => "Guinea-Bissau",
    			"GY" => "Guyana",
    			"HT" => "Haiti",
    			"HM" => "Heard Island and McDonald Islands",
    			"VA" => "Holy See (Vatican City State)",
    			"HN" => "Honduras",
    			"HK" => "Hong Kong",
    			"HU" => "Hungary",
    			"IS" => "Iceland",
    			"IN" => "India",
    			"ID" => "Indonesia",
    			"IR" => "Iran, Islamic Republic of",
    			"IQ" => "Iraq",
    			"IE" => "Ireland",
    			"IM" => "Isle of Man",
    			"IL" => "Israel",
    			"IT" => "Italy",
    			"JM" => "Jamaica",
    			"JP" => "Japan",
    			"JE" => "Jersey",
    			"JO" => "Jordan",
    			"KZ" => "Kazakhstan",
    			"KE" => "Kenya",
    			"KI" => "Kiribati",
    			"KP" => "Korea, Democratic People's Republic of",
    			"KR" => "Korea, Republic of",
    			"KW" => "Kuwait",
    			"KG" => "Kyrgyzstan",
    			"LA" => "Lao People's Democratic Republic",
    			"LV" => "Latvia",
    			"LB" => "Lebanon",
    			"LS" => "Lesotho",
    			"LR" => "Liberia",
    			"LY" => "Libyan Arab Jamahiriya",
    			"LI" => "Liechtenstein",
    			"LT" => "Lithuania",
    			"LU" => "Luxembourg",
    			"MO" => "Macao",
    			"MK" => "Macedonia, The Former Yugoslav Republic of",
    			"MG" => "Madagascar",
    			"MW" => "Malawi",
    			"MY" => "Malaysia",
    			"MV" => "Maldives",
    			"ML" => "Mali",
    			"MT" => "Malta",
    			"MH" => "Marshall Islands",
    			"MQ" => "Martinique",
    			"MR" => "Mauritania",
    			"MU" => "Mauritius",
    			"YT" => "Mayotte",
    			"MX" => "Mexico",
    			"FM" => "Micronesia, Federated States of",
    			"MD" => "Moldova",
    			"MC" => "Monaco",
    			"MN" => "Mongolia",
    			"ME" => "Montenegro",
    			"MS" => "Montserrat",
    			"MA" => "Morocco",
    			"MZ" => "Mozambique",
    			"MM" => "Myanmar",
    			"NA" => "Namibia",
    			"NR" => "Nauru",
    			"NP" => "Nepal",
    			"NL" => "Netherlands",
    			"AN" => "Netherlands Antilles",
    			"NC" => "New Caledonia",
    			"NZ" => "New Zealand",
    			"NI" => "Nicaragua",
    			"NE" => "Niger",
    			"NG" => "Nigeria",
    			"NU" => "Niue",
    			"NF" => "Norfolk Island",
    			"MP" => "Northern Mariana Islands",
    			"NO" => "Norway",
    			"OM" => "Oman",
    			"PK" => "Pakistan",
    			"PW" => "Palau",
    			"PS" => "Palestinian Territory, Occupied",
    			"PA" => "Panama",
    			"PG" => "Papua New Guinea",
    			"PY" => "Paraguay",
    			"PE" => "Peru",
    			"PH" => "Philippines",
    			"PN" => "Pitcairn",
    			"PL" => "Poland",
    			"PT" => "Portugal",
    			"PR" => "Puerto Rico",
    			"QA" => "Qatar",
    			"RE" => "Réunion",
    			"RO" => "Romania",
    			"RU" => "Russian Federation",
    			"RW" => "Rwanda",
    			"BL" => "Saint Barthélemy",
    			"SH" => "Saint Helena",
    			"KN" => "Saint Kitts and Nevis",
    			"LC" => "Saint Lucia",
    			"MF" => "Saint Martin",
    			"PM" => "Saint Pierre and Miquelon",
    			"VC" => "Saint Vincent and the Grenadines",
    			"WS" => "Samoa",
    			"SM" => "San Marino",
    			"ST" => "Sao Tome and Principe",
    			"SA" => "Saudi Arabia",
    			"SN" => "Senegal",
    			"RS" => "Serbia",
    			"SC" => "Seychelles",
    			"SL" => "Sierra Leone",
    			"SG" => "Singapore",
    			"SK" => "Slovakia",
    			"SI" => "Slovenia",
    			"SB" => "Solomon Islands",
    			"SO" => "Somalia",
    			"ZA" => "South Africa",
    			"GS" => "South Georgia and the South Sandwich Islands",
    			"ES" => "Spain",
    			"LK" => "Sri Lanka",
    			"SD" => "Sudan",
    			"SR" => "Suriname",
    			"SJ" => "Svalbard and Jan Mayen",
    			"SZ" => "Swaziland",
    			"SE" => "Sweden",
    			"CH" => "Switzerland",
    			"SY" => "Syrian Arab Republic",
    			"TW" => "Taiwan, Province of China",
    			"TJ" => "Tajikistan",
    			"TZ" => "Tanzania, United Republic of",
    			"TH" => "Thailand",
    			"TL" => "Timor-Leste",
    			"TG" => "Togo",
    			"TK" => "Tokelau",
    			"TO" => "Tonga",
    			"TT" => "Trinidad and Tobago",
    			"TN" => "Tunisia",
    			"TR" => "Turkey",
    			"TM" => "Turkmenistan",
    			"TC" => "Turks and Caicos Islands",
    			"TV" => "Tuvalu",
    			"UG" => "Uganda",
    			"UA" => "Ukraine",
    			"AE" => "United Arab Emirates",
    			"GB" => "United Kingdom",
    			"US" => "United States",
    			"UM" => "United States Minor Outlying Islands",
    			"UY" => "Uruguay",
    			"UZ" => "Uzbekistan",
    			"VU" => "Vanuatu",
    			"VE" => "Venezuela",
    			"VN" => "Viet Nam",
    			"VG" => "Virgin Islands, British",
    			"VI" => "Virgin Islands, U.S.",
    			"WF" => "Wallis and Futuna",
    			"EH" => "Western Sahara",
    			"YE" => "Yemen",
    			"ZM" => "Zambia",
    			"ZW" => "Zimbabwe",
			)
		);
		}
		
		//pagseguro	
		if( $this->opts['gateway']['pagsegurosand'] 
		|| $this->opts['gateway']['pagseguroprod'] ){
			
			$settings['payment']['pagseguroemail'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('PagSeguro Seller email', 'image-store'),
			);
			$settings['payment']['pagsegurotoken'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('PagSeguro token', 'image-store'),
			);
			$settings['payment']['pagsegurotesturl'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('PagSeguro test url', 'image-store'),
			);
		}
		
		// sagepay
		if( $this->opts['gateway']['sagepay'] 
		|| $this->opts['gateway']['sagepaydev']){
			
			$settings['payment']['vpsprotocol'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('SagePay VPS Protocol', 'image-store'),
				'desc' => __('Tested with version 2.23.'),
			);
			$settings['payment']['spvendor'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('SagePay Vendor Name', 'image-store'),
			);
			$settings['payment']['sppassword'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('SagePay Password', 'image-store'),
			);
			$settings['payment']['country'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('SagePay Password', 'image-store'),
			);
			$settings['payment']['spdescription'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('SagePay Description', 'image-store'),
			);
			$settings['payment']['required_'] = array(
				'multi' => true,
				'label' => __('Required Fields', 'image-store'),
			);
			foreach ((array) $this->opts['checkoutfields'] as $key => $label)
				$settings['payment']['required_']['opts'][$key] = array( 'val' => 1, 'label' => $label, 'type' => 'checkbox' );
		}
		
		//custom
		if ($this->opts['gateway']['custom']) {
			$settings['payment']['gateway_name'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Custom Service Name', 'image-store'),
			);
			$settings['payment']['gateway_method'] = array(
				'type' => 'radio',
				'label' => __('Custom Method', 'image-store'),
				'opts' => array(
					'get' => __('Get', 'image-store'),
					'post' => __('Post', 'image-store'),
				),
			);
			$settings['payment']['gateway_url'] = array(
				'val' => '',
				'type' => 'text',
				'label' => __('Custom URL', 'image-store'),
			);
			$settings['payment']['data_pair'] = array(
				'val' => '',
				'type' => 'textarea',
				'label' => __('Custom Data Pair', 'image-store'),
				'desc' => __('Enter key|value should be separated by a pipe, and each data pair by a comma. 
				 ex: key|value,Key|value. <br />
				<strong>Note:</strong> you will have to setup your own notification script ro record sales.<br />
				<strong>Tags:</strong> ', 'image-store') . str_replace( '/', '', implode( ', ', $this->opts['carttags'] ) ),
			);
		}		
		return $settings;
	}
	
	/**
	 * Show user capabilities
	 * to provide permission to image store
	 *
	 * @param array $settings
	 * @return array
	 * @since 3.2.1
	 */
	function show_user_caps( $settings ){
		if ( empty( $_GET['userid'] ) )
			return $settings;
		
		$userid = ( int ) $_GET['userid'];
		$settings['permissions']['ims_'] = array(
			'multi' => true,
			'type' => 'checkbox',
			'label' => __('Permissions', 'image-store'),
		);
		foreach ( $this->uopts['caplist'] as $cap => $capname )
			$settings['permissions']['ims_']['opts'][$cap] = array( 'val' => 1, 'label' => $capname, 'type' => 'checkbox', 'user' => $userid );
		$this->opts['userid'] = $userid;
		return $settings;
	}
	
	/**
	 * Get all user except customers
	 * and administrators
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function get_users( ) {
		
		$users = wp_cache_get( 'ims_users', 'image-store' );

		if ( false == $users ) {
			global $wpdb;
			$users = $wpdb->get_results( 
				"SELECT ID, user_login name FROM $wpdb->users u 
				JOIN $wpdb->usermeta um ON ( u.ID = um.user_id ) 
				WHERE meta_key = '{$wpdb->prefix}capabilities'
				AND meta_value NOT LIKE '%\"administrator\"%'
				AND meta_value NOT LIKE '%\"". esc_sql( $this->customer_role ) ."\"%'
				GROUP BY u.ID "
			);
			wp_cache_set( 'ims_users', $users, 'image-store' );
		}

		if ( empty( $users ) )
			return array( '0' => __('No users to manage', 'image-store'));

		$list = array();
		$list[0] = __('Select user', 'image-store');

		foreach ($users as $user)
			$list[$user->ID] = $user->name;

		return $list;
	}
	
	/**
	 * Return Image Store options
	 *
	 * @parm string $option
	 * @parm unit $userid 
	 * @return string/int
	 * @since 3.0.0
	 */
	function vr( $option, $key = false, $userid = 0 ) {
		if ( $userid ) {
			$usermeta = get_user_meta( $userid, 'ims_user_caps', true );
			if ( isset( $usermeta["{$option}{$key}"] ) ) return true;
			return false;
		}
		if ( isset( $this->opts[$option][$key] ) && is_array( $this->opts[$option] ) )
			return stripslashes( $this->opts[$option][$key] );
		elseif ( isset( $this->opts[$option . $key] ) )
			return stripslashes( $this->opts[$option . $key] );
		elseif ( isset( $this->opts[$option] ) && is_string( $this->opts[$option] ) )
			return stripslashes( $this->opts[$option] );
		elseif ( $o = $this->get_option( $option ) )
			return stripslashes( $o );
		elseif ( $ok = $this->get_option( $option . $key ) )
			return stripslashes( $ok );
		return false;
	}
	
	/**
	 * Check if it's a checkbox
	 * or radio box
	 *
	 * @parm string $elem
	 * @return bool
	 * @since 3.0.0
	 */
	function is_checkbox($type) {
		if ( $this->in_array( $type, array( 'checkbox', 'radio' ) ) )
			return true;
		return false;
	}
	
	/**
	 * Display unit sizes
	 *
	 * @return void
	 * @since 1.1.0
	 */
	function dropdown_units($name, $selected) {
		$output = '<select name="' . $name . '" class="unit">';
		foreach ( $this->units as $unit => $label ) {
			$select = ( $selected == $unit ) ? ' selected="selected"' : '';
			$output .= '<option value="' . esc_attr($unit) . '" ' . $select . '>' . $label . '</option>';
		}
		echo $output .= '</select>';
	}
	
	/**
	 * Save settings
	 *
	 * @return void
	 * @since 3.0.0
	 */
	function save_settings( ) {
		
		if( isset( $_REQUEST['flush'] ) )
			flush_rewrite_rules( ); 

		if ( empty( $_POST ) || $this->page != 'ims-settings' )
			return;
		
		check_admin_referer( 'ims_settings' );

		//reset settings
		if ( isset( $_POST['resetsettings'] ) || isset( $_POST['uninstall'] ) ) {

			include_once( IMSTORE_ABSPATH . '/admin/install.php' );
			$ImStoreInstaller = new ImStoreInstaller();
			
			if ( isset( $_POST['uninstall'] ) )
				$ImStoreInstaller->imstore_uninstall( );
			
			$ImStoreInstaller->imstore_default_options( );
			wp_redirect( $this->pageurl . '&flush=1&ms=3' );
			die( );

		//save options
		} elseif ( isset( $_POST['ims-action'] ) ) {
			
			$action = $_POST['ims-action'];
			include( IMSTORE_ABSPATH . "/admin/settings/settings-fields.php" );

			if ( empty( $action ) || empty( $settings[$action]) ) {
				
				wp_redirect( $this->pageurl );
				die( );
			}

			//clear image cache data
			if( isset( $_POST['watermark'] ) )
				update_option( 'ims_cache_time', current_time( 'timestamp' ) );

			if ( 'permissions' == $action ) {
				if ( ! is_numeric( $_POST['userid'] ) ) {
					
					wp_redirect( $this->pageurl );
					die( );
				}

				$newcaps = array( );
				$userid = (int) $_POST['userid'];
				
				foreach ( $this->uopts['caplist'] as $cap => $label )
					if ( !empty($_POST['ims_'][$cap] ) )
						$newcaps['ims_' . $cap] = 1;
				
				update_user_meta( $userid, 'ims_user_caps', $newcaps);
				do_action( 'ims_user_permissions', $action, $userid, $this->uopts );
				
				wp_redirect( $this->pageurl . "&userid=" . $userid );
				die( );
			}

			foreach ( $settings[$action] as $key => $val ) {
				if ( isset( $val['col'] ) ) {
					foreach ( $val['opts'] as $k2 => $v2) {
						if ( empty($_POST[$k2] ) )
							$this->opts[$k2] = false;
						else $this->opts[$k2] = $_POST[$k2];
					}
				}elseif ( isset( $val['multi'] ) ) {
					foreach ( $val['opts'] as $k2 => $v2 ) {
						if ( $this->get_option( $key . $k2 ) ){
							update_option($key . $k2, $_POST[$key][$k2]);
							if( $this->sync == true ) 
								update_blog_option( 1, $key . $k2, $_POST[$key][$k2] );
						}elseif ( isset( $this->opts[$key] ) && is_array( $this->opts[$key] ) )
							$this->opts[$key][$k2] = isset($_POST[$key][$k2]) ? $_POST[$key][$k2] : false;
						elseif ( !empty( $_POST[$key][$k2] ) )
							$this->opts[$key . $k2] = $_POST[$key][$k2];
						else $this->opts[$key . $k2] = false;
					}
				}elseif( $key == 'galleriespath' && !preg_match('/^\//',$_POST['galleriespath'] ) ){
					$this->opts[$key] = "/" . trim( $_POST['galleriespath'] );
				}elseif ( isset($_POST[$key] ) )
					$this->opts[$key] = $_POST[$key];
				else $this->opts[$key] = false;
			}
					
			//multisite support
			if ( is_multisite( ) && $this->sync == true )
				switch_to_blog( 1 );
				
			update_option( $this->optionkey, $this->opts );
			
			if ( isset( $_POST['wlocal'] ) )
				update_option( 'ims_wlocal', $_POST['wlocal'] );

			if ( isset( $_POST['columns'] ) ) 
				update_option( 'ims_searchable', ( isset($_POST['ims_searchable'] ) ? $_POST['ims_searchable'] : false ) );
			
			if ( $this->in_array( $action, array( 'taxonomies', 'image', 'gallery', 'general' ) )  )
				$this->pageurl .= "&flush=1";
			
			do_action( 'ims_save_settings', $action, $settings );

			wp_redirect( $this->pageurl . '&ms=4' );
			die( );
		}
	}
}