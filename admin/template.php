<?php

	/**
	 * Image Store - Admin Template
	 *
	 * @file template.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/admin/template.php
	 * @since 2.0.0
	 */

	 // Stop direct access of the file
	 if ( !defined( 'ABSPATH' ) )
		die( );

	$count = ( isset( $_GET['c'] ) ) ? $_GET['c'] : false;
	$msnum = ( !empty( $_GET['ms'] ) ) ? $_GET['ms'] : false;

	//Settings
	$message[1] = __( "Cache cleared.", 'image-store' );
	$message[2] = __( 'The user was updated.', 'image-store' );
	$message[3] = __( 'All settings were reseted.', 'image-store' );
	$message[4] = __( 'The settings were updated.', 'image-store' );

	//customers
	$message[10] = __( 'A new customer was added successfully.', 'image-store' );
	$message[11] = __( 'Customer updated.', 'image-store' );
	$message[12] = __( 'Status successfully updated.', 'image-store' );
	$message[13] = __( 'Customer deleted.', 'image-store' );
	$message[14] = sprintf( __( '%d customers updated.', 'image-store' ), $count);
	$message[15] = sprintf( __( '%d customers deleted.', 'image-store' ), $count);

	//Sales
	$message[20] = __( 'Trash emptied', 'image-store' );
	$message[21] = __( 'Order deleted.', 'image-store' );
	$message[22] = __( 'Order status updated.', 'image-store' );
	$message[23] = __( 'Order moved to trash.', 'image-store' );
	$message[24] = sprintf( __( '%d orders deleted.', 'image-store' ), $count);
	$message[25] = sprintf( __( 'Status updated on %d orders.', 'image-store' ), $count);
	$message[26] = sprintf( __( '%d orders moved to trash.', 'image-store' ), $count);

	//pricing
	$message[30] = __( 'Promotion updated.', 'image-store' );
	$message[31] = __( 'Promotion deleted.', 'image-store' );
	$message[32] = __( 'New promotion added.', 'image-store' );
	$message[33] = __( 'The package was updated.', 'image-store' );
	$message[34] = __( 'The price list was updated.', 'image-store' );
	$message[35] = __( 'The new package was created.', 'image-store' );
	$message[36] = __( 'A new image size was created.', 'image-store' );
	$message[37] = __( 'Image size list was updated.', 'image-store' );
	$message[38] = __( 'The new list was created successfully.', 'image-store' );
	$message[39] = sprintf( __( '%d promotions deleted.', 'image-store' ), $count);

	$message[40] = __( 'Options saved.', 'image-store' );
	$message[41] = __( "You do not have sufficient permissions to access this page.", 'image-store' );

	$message[42] = __( 'Color option list was updated.', 'image-store' );
	$message[43] = __( 'Shipping option list was updated.', 'image-store' );
	$message[44] = __( 'Finish list was updated.', 'image-store' );
	$message[45] = __( 'Filter list was updated.', 'image-store' );

	$screens = array(
		"ims-sales" => array( "sales", __( 'Sales', 'image-store' ) ),
		"ims-customers" => array( "users", __( 'Users', 'image-store' ) ),
		"user-galleries" => array( "galleries", __( 'My Galleries', 'image-store' ) ),
		"user-images" => array( "images", __( 'My Images', 'image-store' ) ),
		"ims-pricing" => array( "pricing", __( 'Pricing', 'image-store' ) ),
		"ims-settings" => array( "options-general", __( 'Settings', 'image-store' ) )
	);

	?>

    <div class="wrap imstore">
	    <?php screen_icon( $screens[$this->page][0] ) ?>
			<h2><?php echo $screens[$this->page][1] ?></h2>

      <?php if ( $this->page == 'ims-settings' ): //display ads ?>

      <div class="ims-social-box">
          <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=YM9GXCFBND89E" class="ims-donate" title="Like the plugin? Please Donate"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="donate" height="20"></a>
      </div><!--.ims-social-box-->

			<div class="adunitbox postbox">
				<div id="adunit"></div><!--.adunit-->
				<script type="text/javascript">(function() {  var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];  s.type = 'text/javascript';  s.async = true; s.src = '//s.xparkmedia.com/_rsc/ads.js?mode=auto'; t.parentNode.insertBefore(s, t); })();</script>
			</div>

      <?php endif; //end ads  ?>

    	<div id="poststuff" class="metabox-holder">
      <?php if ($msnum) echo '<div class="updated fade" id="message"><p>', $message[$msnum], '</p></div>' ?>

     	<?php switch ( $this->page ) {
				case "ims-settings":
					include( apply_filters( 'ims_settings_path', IMSTORE_ABSPATH . '/admin/settings/settings.php' ) );
					break;
				case "ims-customers":
					include( apply_filters( 'ims_customers_path', IMSTORE_ABSPATH . '/admin/customers/customers.php' ) );
					break;
				case "ims-pricing":
					include( apply_filters( 'ims_pricing_path', IMSTORE_ABSPATH . '/admin/sales/pricing.php' ) );
					break;
				case "ims-sales":
					if ( isset( $_GET['details'] ) )
						include( apply_filters( 'ims_sales_details_path', IMSTORE_ABSPATH . '/admin/sales/sales-details.php' ) );
					else include( apply_filters( 'ims_sales_path', IMSTORE_ABSPATH . '/admin/sales/sales.php' ) );
					break;
				case "user-galleries":
					include( apply_filters( 'ims_user_galleries_path', IMSTORE_ABSPATH . '/admin/customers/customer-galleries.php' ) );
					break;
				case "user-images":
					include( apply_filters( 'ims_user_images_path', IMSTORE_ABSPATH . '/admin/customers/customer-images.php' ) );
					break;
				default:
			} ?>
        </div>
    </div><!--.wrap .imstore-->
