<?php

	/**
	 * Image Store - Admin Settings Fields
	 *
	 * @file settings-fields.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource wp-content/plugins/image-store/admin/settings/settings-fields.php
	 * @since 3.0.0
	 */
	 
	 if ( ! current_user_can( 'ims_change_settings' ) )
		die( );
	
	//page option
	$pages = ( array ) get_pages( );
	$templates = function_exists( 'get_page_templates' ) ? get_page_templates( ) : false;
	
	//general settings
	$settings['general'] = array( 
		'deletefiles' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Delete image files', 'image-store' ),
			'desc' => __( 'Delete files from server, when deleting a gallery/images', 'image-store' ),
		 ),
		'mediarss' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Media RSS feed', 'image-store' ),
			'desc' => __( 'Add RSS feed the blog header for unsecured galleries. Useful for CoolIris/PicLens', 'image-store' ),
		 ),
		'stylesheet' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Use CSS', 'image-store' ),
			'desc' => __( 'Use the default Image Store look', 'image-store' ),
		 ),
		'imswidget' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Widget', 'image-store' ),
			'desc' => __( 'Enable the use of the Image Store Widget', 'image-store' ),
		 ),
		'widgettools' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Tools Widget', 'image-store' ),
			'desc' => __( 'Disable default store navigation and use a widget instead', 'image-store' ),
		 ),
		'store' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Store features', 'image-store' ),
			'desc' => __( 'Uncheck to use as a gallery manager only, not a store.', 'image-store' ),
		 ),
		'photos' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Show "Photo" link', 'image-store' ),
			'desc' => __( 'Uncheck to hide Photo link from the store navigation.', 'image-store' ),
		 ),
		'slideshow' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Show "Slideshow" link', 'image-store' ),
			'desc' => __( 'Uncheck to hide Slideshow link from the store navigation.', 'image-store' ),
		 ),
		'favorites' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Show "Favorites" link', 'image-store' ),
			'desc' => __( 'Uncheck to hide Favorites link from the store navigation.', 'image-store' ),
		 ),
		'ims_searchable' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Searchable galleries', 'image-store' ),
			'desc' => __( 'Allow galleries to show in search results.', 'image-store' ),
		 ),
		 'voting_like' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Activate voting', 'image-store' ),
			'desc' => __( 'Enable voting/like feature.', 'image-store' ),
		 ),
		 'columns' => array( 
			'type' => 'select',
			'label' => __( 'Columns', 'image-store' ),
			'desc' => __( 'Change the column display.', 'image-store' ),
			'opts' => array( '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', ),
		 ),
	 );
	 
	// Taxonomies
	$settings['taxonomies'] = array(
		'album_level' => array(
			'val' => 1, 
			'type' => 'checkbox',
			'label' => __( 'One level', 'image-store' ),
			'desc' => __( 'Display only one level on albums, do not display child albums.', 'image-store' ),
		 ),
		'album_slug' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Albums url base', 'image-store' ),
			'desc' => __( 'Change the url base for albums.', 'image-store' ),
		 ),
		'album_template' => array( 
			'type' => 'select',
			'label' => __( 'Album Template', 'image-store' ),
			'desc' => __( 'Select the template that should be used to display albums.', 'image-store' ),
			'opts' => array( 
			'0' => __( 'Default template', 'image-store' ),
				'page.php' => __( 'Page template', 'image-store' ),
			 ) + array_flip( ( array ) $templates ),
		 ),
		'album_per_page' => array( 
			'val' => '',
			'type' => 'number',
			'label' => __( 'Galleries per album', 'image-store' ),
			'desc' => __( 'How many galleries to display per page on albums.', 'image-store' ),
		 ),
		 'tag_slug' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Tag url base', 'image-store' ),
			'desc' => __( 'Change the url base for tags.', 'image-store' ),
		 ),
		 'tag_template' => array( 
			'type' => 'select',
			'label' => __( 'Tags Template', 'image-store' ),
			'desc' => __( ' Select the template that should be used to display tags.', 'image-store' ),
			'opts' => array( 
			'0' => __( 'Default template', 'image-store' ),
				'page.php' => __( 'Page template', 'image-store' ),
			 ) + array_flip( ( array ) $templates ),
		 ),
		'tag_per_page' => array( 
			'val' => '',
			'type' => 'number',
			'label' => __( 'Galleries per tag', 'image-store' ),
			'desc' => __( 'How many galleries to display per page on tags.', 'image-store' ),
		 ),
	);
	

	// Gallery
	$settings['gallery'] = array( 
		'galleriespath' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Galleries folder path', 'image-store' ),
			'desc' => __( 'Default folder path for all the galleries images', 'image-store' ),
		 ),
		'securegalleries' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Secure galleries', 'image-store' ),
			'desc' => __( 'Secure all new galleries with a password by default.', 'image-store' ),
		 ),
		'titleascaption' => array(
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Use title as caption', 'image-store' ),
			'desc' => __( 'Use title field as caption instead of caption field.', 'image-store' ),
		),
		'wplightbox' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Ligthbox for WP galleries', 'image-store' ),
			'desc' => __( 'Use lightbox on WordPress Galleries.', 'image-store' ),
		 ),
		'downloadorig' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Download Original', 'image-store' ),
			'desc' => __( 'Allow users to download original image if image size selected is not available.', 'image-store' ),
		 ),
		'attchlink' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Link image to attachment', 'image-store' ),
			'desc' => __( 'Link image to image page instead of image file.', 'image-store' ),
		 ),
		'ims_page_secure' => array( 
			'type' => 'select',
			'label' => __( 'Secure galleries page', 'image-store' ),
			'desc' => __( ' Page used to display the gallery login form', 'image-store' ),
		 ),
		'gallery_template' => array( 
			'type' => 'select',
			'label' => __( 'Gallery template', 'image-store' ),
			'desc' => __( ' Select the template that should be used to display the galleries.', 'image-store' ),
			'opts' => array( '0' => __( 'Default template', 'image-store' ) ) + array_flip( ( array ) $templates ),
		 ),
		'gallery_slug' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Gallery url base', 'image-store' ),
			'desc' => __( 'Change the url base for galleries.', 'image-store' ),
		 ),
		'imgs_per_page' => array( 
			'val' => '',
			'type' => 'number',
			'label' => __( 'Images per page', 'image-store' ),
			'desc' => __( 'How many images to display per page on the front-end.', 'image-store' ),
		 ),
		'galleryexpire' => array( 
			'val' => '',
			'type' => 'number',
			'label' => __( 'Galleries expire after', 'image-store' ),
			'desc' => __( 'In days, set to 0 to remove expiration default.', 'image-store' ),
		 ),
		'imgsortorder' => array( 
			'type' => 'select',
			'label' => __( 'Sort images', 'image-store' ),
			'opts' => array( 
				'menu_order' => __( 'Custom order', 'image-store' ),
				'excerpt' => __( 'Caption', 'image-store' ),
				'title' => __( 'Image title', 'image-store' ),
				'date' => __( 'Image date', 'image-store' ),
			 ),
		 ),
		'imgsortdirect' => array( 
			'type' => 'select',
			'label' => __( 'Sort direction', 'image-store' ),
			'opts' => array( 
				'ASC' => __( 'Ascending', 'image-store' ),
				'DESC' => __( 'Descending', 'image-store' ),
			 ),
		 ),
	 );
	 
	foreach ( $pages as $page )
	$settings['gallery']['ims_page_secure']['opts'][$page->ID] = $page->post_title;
	
	
	// Image
	$settings['image'] = array( 
		'image_slug' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Image url base', 'image-store' ),
			'desc' => __( 'Change the url base for images.', 'image-store' ),
		 ),
		'preview_size_' => array( 
			'multi' => true,
			'label' => __( 'Image preview size( pixels )', 'image-store' ),
			'desc' => __( 'After changing the size, images for old galleries need to be regenerated using scan folder.', 'image-store' ),
			'opts' => array( 
				'w' => array( 
					'val' => '',
					'type' => 'number',
					'label' => __( 'Max Width', 'image-store' ),
				 ),
				'h' => array( 
					'val' => '',
					'type' => 'number',
					'label' => __( 'Max Height', 'image-store' ),
				 ),
				'q' => array( 
					'val' => '',
					'label' => __( 'Quality', 'image-store' ),
					'type' => 'number',
					'desc' => '( 1-100 )',
				 ),
			 ),
		 ),
		'watermark' => array( 
			'type' => 'radio',
			'label' => __( 'Watermark', 'image-store' ),
			'opts' => array( 
				'0' => __( 'No watermark', 'image-store' ),
				'1' => __( 'Use text as watermark', 'image-store' ),
				'2' => __( 'Use image as watermark', 'image-store' ),
			 ),
		 ),
		'watermark_' => array( 
			'multi' => true,
			'type' => 'text',
			'label' => __( 'Watermark options', 'image-store' ),
			'opts' => array( 
				'text' => array( 
					'val' => '',
					'type' => 'text',
					'label' => __( 'Text', 'image-store' ),
				 ),
				'color' => array( 
					'val' => '',
					'type' => 'text',
					'label' => __( 'Color', 'image-store' ),
					'desc' => ' #Hex'
				 ),
				'size' => array( 
					'val' => '',
					'type' => 'number',
					'label' => __( 'Font size', 'image-store' )
				 ),
				'trans' => array( 
					'val' => '',
					'type' => 'text',
					'label' => __( 'Transparency', 'image-store' ),
					'desc' => ' ( 0-100 )'
				 ),
			 ),
		 ),
		'watermarkurl' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Watermark URL', 'image-store' ),
			'desc' => __( 'Path relative to wp-content or full URL to image, PNG with transparency recommended', 'image-store' ),
		 ),
		 'watermarktile' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Tile Watermark', 'image-store' ),
			'desc' => __( 'Tile image or text watermark, it will disable the watermark location option', 'image-store' ),
		 ),
	 );
	
	//slideshow
	$settings['slideshow'] = array( 
		array( 
			'col' => true,
			'opts' => array( 
				'numThumbs' => array( 
					'type' => 'number',
					'label' => __( 'Number of thumbnails to show', 'image-store' ),
				 ),
				'maxPagesToShow' => array( 
					'type' => 'number',
					'label' => __( 'Maximun number of pages', 'image-store' ),
				 )
			 ),
		 ),
		array( 
			'col' => true,
			'opts' => array( 
				'transitionTime' => array( 
					'type' => 'number',
					'label' => __( 'Transition time', 'image-store' ),
					'desc' => __( '1000 = 1 second', 'image-store' ),
				 ),
				'slideshowSpeed' => array( 
					'type' => 'number',
					'label' => __( 'Slideshow speed', 'image-store' ),
					'desc' => __( '1000 = 1 second', 'image-store' ),
				 )
			 ),
		 ),
		array( 
			'col' => true,
			'opts' => array( 
				'playLinkText' => array( 
					'type' => 'text',
					'label' => __( 'Play link text', 'image-store' ),
				 ),
				'pauseLinkTex' => array( 
					'type' => 'text',
					'label' => __( 'Pause link text', 'image-store' ),
				 )
			 ),
		 ),
		array( 
			'col' => true,
			'opts' => array( 
				'nextLinkText' => array( 
					'type' => 'text',
					'label' => __( 'Next link text', 'image-store' ),
				 ),
				'prevLinkText' => array( 
					'type' => 'text',
					'label' => __( 'Previous link text', 'image-store' ),
				 )
			 ),
		 ),
		array( 
			'col' => true,
			'opts' => array( 
				'nextPageLinkText' => array( 
					'type' => 'text',
					'label' => __( 'Next page link text', 'image-store' ),
				 ),
				'prevPageLinkText' => array( 
					'val' => '',
					'type' => 'text',
					'label' => __( 'Previous page link text', 'image-store' ),
				 )
			 ),
		 ),
		array( 
			'col' => true,
			'opts' => array( 
				'closeLinkText' => array( 
					'type' => 'text',
					'label' => __( 'Close link text', 'image-store' ),
				 ),
				'empty' => array( 'label' => '&nbsp;' )
			 ),
		 ),
		array( 
			'col' => true,
			'opts' => array( 
				'bottommenu' => array( 
					'val' => 1,
					'type' => 'checkbox',
					'label' => __( 'Menu at the bottom', 'image-store' ),
				 ),
				'autoStart' => array( 
					'val' => 1,
					'type' => 'checkbox',
					'label' => __( 'Auto start?', 'image-store' ),
				 )
			 ),
		 ),
	 );

	//payment
	$settings['payment'] = array( 
		'symbol' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Currency Symbol', 'image-store' ),
		 ),
		'shipping' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Apply shipping', 'image-store' ),
			'desc' => __( 'Uncheck to disable shopping cart shipping option.', 'image-store' ),
		 ),
		'decimal' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Show decimal point', 'image-store' ),
			'desc' => __( 'Uncheck to disable auto format prices with a decimal points.', 'image-store' ),
		 ),
		'clocal' => array( 
			'type' => 'radio',
			'label' => __( 'Currency Symbol Location', 'image-store' ),
			'opts' => array( 
				'1' => __( '&#036;100', 'image-store' ),
				'2' => __( '&#036; 100', 'image-store' ),
				'3' => __( '100&#036;', 'image-store' ),
				'4' => __( '100 &#036;', 'image-store' ),
			 ),
		 ),
		'currency' => array( 
			'type' => 'select',
			'label' => __( 'Default Currency', 'image-store' ),
			'opts' => array( 
				'0' => __( 'Please Choose Default Currency', 'image-store' ),
				'AUD' => __( 'Australian Dollar', 'image-store' ),
				'BRL' => __( 'Brazilian Real', 'image-store' ),
				'CAD' => __( 'Canadian Dollar', 'image-store' ),
				'CZK' => __( 'Czech Koruna', 'image-store' ),
				'DKK' => __( 'Danish Krone', 'image-store' ),
				'EUR' => __( 'Euro', 'image-store' ),
				'HKD' => __( 'Hong Kong Dollar', 'image-store' ),
				'HUF' => __( 'Hungarian Forint', 'image-store' ),
				'ILS' => __( 'Israeli New Sheqel', 'image-store' ),
				'JPY' => __( 'Japanese Yen', 'image-store' ),
				'MYR' => __( 'Malaysian Ringgit', 'image-store' ),
				'MXN' => __( 'Mexican Peso', 'image-store' ),
				'NOK' => __( 'Norwegian Krone', 'image-store' ),
				'NZD' => __( 'New Zealand Dollar', 'image-store' ),
				'PHP' => __( 'Philippine Peso', 'image-store' ),
				'PLN' => __( 'Polish Zloty', 'image-store' ),
				'GBP' => __( 'Pound Sterling', 'image-store' ),
				'SGD' => __( 'Singapore Dollar', 'image-store' ),
				'ZAR' => __( 'South African Rands', 'image-store' ),
				'SEK' => __( 'Swedish Krona', 'image-store' ),
				'CHF' => __( 'Swiss Franc', 'image-store' ),
				'TWD' => __( 'Taiwan New Dollar', 'image-store' ),
				'THB' => __( 'Thai Baht', 'image-store' ),
				'TRY' => __( 'Turkish Lira', 'image-store' ),
				'USD' => __( 'U.S. Dollar', 'image-store' ),
			 ),
		 ),
		'gateway' => array( 
			'multi' => true,
			'label' => __( 'Payment gateway', 'image-store' ),
			'opts' => array( 
				'enotification' => array( 'val' => 1, 'label' => __( 'Email notification only', 'image-store' ), 'type' => 'checkbox' ),
				'paypalsand' => array( 'val' => 1, 'label' => __( 'Paypal Cart Sanbox (test)', 'image-store' ), 'type' => 'checkbox' ),
				'paypalprod' => array( 'val' => 1, 'label' => __( 'Paypal Cart Production (live)', 'image-store' ), 'type' => 'checkbox' ),
				'googlesand' => array( 'val' => 1, 'label' => __( 'Google Checkout Sandbox (test)', 'image-store' ), 'type' => 'checkbox' ),
				'googleprod' => array( 'val' => 1, 'label' => __( 'Google Checkout Production (live)', 'image-store' ), 'type' => 'checkbox' ),
				'wepaystage' => array( 'val' => 1, 'label' => __( 'WePay Stage (test)', 'image-store' ), 'type' => 'checkbox' ),
				'wepayprod' => array( 'val' => 1, 'label' => __( 'WePay Production (live)', 'image-store' ), 'type' => 'checkbox' ),
				'pagsegurosand' => array( 'val' => 1, 'label' => __( 'Pago Seguro Sandbox (test)', 'image-store' ), 'type' => 'checkbox' ),
				'pagseguroprod' => array( 'val' => 1, 'label' => __( 'Pago Seguro Production (live)', 'image-store' ), 'type' => 'checkbox' ),
				'sagepaydev' => array( 'val' => 1, 'label' => __( 'sagePay Sandbox (test)', 'image-store' ), 'type' => 'checkbox' ),
				'sagepay' => array( 'val' => 1, 'label' => __( 'sagePay Production (live)', 'image-store' ), 'type' => 'checkbox' ),
				'custom' => array( 'val' => 1, 'label' => __( 'Other', 'image-store' ), 'type' => 'checkbox' ),
			 )
		 ),
	 );

	//checkout
	$settings['checkout'] = array( 
		'taxamount' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Tax', 'image-store' ),
			'desc' => __( 'Set tax to zero (0) to remove tax calculation.', 'image-store' ),
		 ),
		 'loginform' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Checkout login form', 'image-store' ),
			'desc' => __( 'Add the login / register form at  the end of the receipt page.', 'image-store' ),
		 ),
		  'downloadlinks' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Email only downloads', 'image-store' ),
			'desc' => __( 'Display download link on email only checkout receipt even if payment has not been verified.', 'image-store' ),
		 ),
		'taxtype' => array( 
			'type' => 'select',
			'label' => __( 'Tax calculation type', 'image-store' ),
			'opts' => array( 
				'percent' => __( 'Percent', 'image-store' ),
				'amount' => __( 'Amount', 'image-store' ),
			 ),
		 ),
		'notifyemail' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Order Notification email(s)', 'image-store' ),
		 ),
		'notifysubj' => array( 
			'val' => '',
			'type' => 'text',
			'label' => __( 'Order Notification subject', 'image-store' ),
		 ),
		'notifymssg' => array( 
			'type' => 'textarea',
			'label' => __( 'Order Notification message', 'image-store' ),
			'desc' => __( 'Tags: ', 'image-store' ) . str_replace( '/', '', implode( ', ', ( array ) $this->opts['tags'] ) ),
		 ),
		'emailreceipt' => array( 
			'val' => 1,
			'type' => 'checkbox',
			'label' => __( 'Email receipt', 'image-store' ),
			'desc' => __( 'Email purchase reciept to customers if they provide an email.', 'image-store' ),
		 ),
		'receiptname' => array( 
			'val' => 'Image Store',
			'type' => 'text',
			'label' => __( 'Receipt From', 'image-store' ),
			'desc' => __( 'Display name where the receipt comes from', 'image-store' ),
		 ),
		'receiptemail' => array( 
			'val' => 'imstore@' . $_SERVER['HTTP_HOST'],
			'type' => 'text',
			'label' => __( 'Receipt From email', 'image-store' ),
			'desc' => __( 'This is the email address that will be display to the user in the "From" field', 'image-store' ),
		 ),
		'thankyoureceipt' => array( 
			'type' => 'textarea',
			'label' => __( 'Purchase Receipt', 'image-store' ),
			'desc' => __( 'Thank you message and receipt information', 'image-store' ),
		 ),
		'termsconds' => array( 
			'type' => 'textarea',
			'label' => __( 'Terms and Conditions', 'image-store' ),
			'desc' => __( 'Shown below the shopping cart', 'image-store' ),
		 ),
	 );
	
	//permissions
	$settings['permissions'] = array( 
		'userid' => array( 
			'type' => 'select',
			'label' => __( 'Select User', 'image-store' ),
			'opts' => $this->get_users( ),
		 ),
	 );

	//reset
	$settings['reset'] = array( 
		
		'empty1' => array( 'type' => 'empty' ),
		
		'resetsettings' => array( 
			'type' => 'submit',
			'label' => __( 'Reset', 'image-store' ),
			'val' => __( 'Reset all settings to defaults', 'image-store' ),
		 ),
		
		'empty2' => array( 'type' => 'empty' ),
		
		'uninstall' => array( 
			'type' => 'uninstall',
			'label' => __( 'Uninstall', 'image-store' ),
			'val' => __( 'Uninstall Image Store', 'image-store' ),
			'desc' => __( '<p><strong>UNINSTALL IMAGE STORE WARNING.</strong></p>
					 <p>Once uninstalled,this cannot be undone.<strong> You should backup your database </strong> 
					 and image files before doing this, Just in case. If you are not sure what are your doing,please don not do anything</p>', 'image-store' ),
		 ),
	
	 );
	
	$settings = apply_filters( 'ims_setting_fields', $settings );
