<?php

	/**
	 * Image Store - Checkout Form Page
	 *
	 * @file cart.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/_store/checkout.php
	 * @since 1.0.2
	 */

	// Stop direct access of the file
	if ( !defined( 'ABSPATH' ) )
		die( );
	
	$userdata = wp_get_current_user( ); 
	$fields = apply_filters( 'ims_user_checkout_fields', array(
		'last_name', 'first_name', 'user_email', 'ims_address', 'ims_city', 'ims_state', 'ims_zip', 'ims_phone' 
	));
	
	foreach( $fields as $field ){
		if( isset( $_POST[ $field ] ) )
			$userdata->$field = $_POST[ $field ];
		else if( !isset( $userdata->$field  ) ){
			if( !is_object( $userdata ) )
				$userdata = new stdClass();
			$userdata->$field = false;
		}
	}
	
	$output .= '<form method="post" action="#" class="shipping-info">';
	
	$output .= apply_filters( 'ims_before_checkout_order', '', $this->cart, $this->opts );
		
	$output .= '<fieldset class="ims-shipping">';
	$output .= '<legend>' . __( "Shipping Information", 'image-store' ) . '</legend>';
	
	$output .= '<div class="ims-p user-info">';
	$output .= '<label for="first_name">' . __( 'First Name', 'image-store' ) . ( $this->opts['required_first_name'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="first_name" id="first_name" value="' . esc_attr( $userdata->first_name ) . '" class="ims-input" />';
	$output .= '<span class="ims-break"></span>';
	$output .= '<label for="last_name">' . __( 'Last Name', 'image-store' ) . ( $this->opts['required_last_name'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="last_name" id="last_name" value="' . esc_attr( $userdata->last_name ) . '" class="ims-input"/>';
	$output .= '</div><!--.user-info-->';
	
	$output .= '<div class="ims-p email-info">';
	$output .= '<label for="user_email">' . __( 'Email', 'image-store' ) . ( $this->opts['required_user_email'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="user_email" id="user_email" value="' . esc_attr( $userdata->user_email ) . '" class="ims-input" />';
	$output .= '</div><!--.email-info-->';
	
	$output .= '<div class="ims-p address-info">';
	$output .= '<label for="ims_address">' . __( 'Address', 'image-store' ) . ( $this->opts['required_ims_address'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="ims_address" id="ims_address" value="' . esc_attr( $userdata->ims_address ) . '" class="ims-input" />';
	$output .= '<span class="ims-break"></span>';
	
	$output .= '<label for="ims_city">' . __( 'City', 'image-store' ) . ( $this->opts['required_ims_city'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="ims_city" id="ims_city" value="' . esc_attr( $userdata->ims_city ) . '" class="ims-input" />';
	$output .= '<span class="ims-break"></span>';
	
	$output .= '<label for="ims_state">' . __( 'State', 'image-store' ) . ( $this->opts['required_ims_state'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="ims_state" id="ims_state" value="' . esc_attr( $userdata->ims_state ) . '" class="ims-input" />';
	$output .= '<span class="ims-break"></span>';
	
	$output .= '<label for="ims_zip">' . __( 'Zip', 'image-store' ) . ( $this->opts['required_ims_zip'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="ims_zip" id="ims_zip" value="' . esc_attr( $userdata->ims_zip ) . '" class="ims-input" />';
	$output .= '<span class="ims-break"></span>';
		
	$output .= '<label for="ims_phone">' . __( 'Phone', 'image-store' ) . ( $this->opts['required_ims_phone'] ? ' <span class="req">*</span>' : '' ) . ' </label>';
	$output .= '<input type="text" name="ims_phone" id="ims_phone" value="' . esc_attr( $userdata->ims_phone ) . '" class="ims-input" />';
	$output .= '</div>';

	$output .= apply_filters( 'ims_after_checkout_order', '', $this->cart, $this->opts );

	$output .= '<div class="ims-p">';
	$output .= '<label for="ims_instructions">' . __( 'Additional Instructions', 'image-store' ) . ' </label>';
	$output .= '<textarea name="instructions" id="ims_instructions" class="ims-instructions">' . esc_html( $this->cart['instructions'] ) . '</textarea>';
	$output .= '</div>';
	
	$output .= '<div class="ims-p"><small><span class="req">*</span>' . __( "Required fields", 'image-store' ) . '</small></div>';
	$output .= '</fieldset><!--.shipping-info-->';
	
	$output .= apply_filters( 'ims_checkout_order_fieldset', '', $this->cart, $this->opts );
	
	$output .= '<fieldset class="order-info">';
	$output .= '<legend>' . __( "Order Information", 'image-store' ) . '</legend>';
	$output .= '<div class="ims-p order-info">';
	$output .= '<span class="ims-items"><strong>' . __( "Total items:", 'image-store' ) . '</strong> ' . $this->cart['items'] . '</span>';
	$output .= '<span class="ims-total"><strong>' . __( "Order Total:", 'ims ') . '</strong> ' . $this->format_price( $this->cart['total'] ) . '</span>';
	$output .= '</div>';

	$output .= apply_filters( 'ims_checkout_order_fields', '', $this->cart, $this->opts );
	
	if ( $this->opts['shippingmessage'] )
	$output .='<div class="shipping-message">' . make_clickable(wpautop(stripslashes($this->opts['shippingmessage']))) . '</div>';
	
	$output .= '<div class="ims-p submit-buttons">';
	$output .= '<input name="ims-cancel-checkout" type="submit" value="' . esc_attr__( 'Cancel', 'image-store' ) . '" class="secundary secondary" /> ';
	$output .= '<input name="ims-enotice-checkout" type="submit" value="' . esc_attr__(' Submit Order', 'image-store' ) . '" class="primary" />';
	
	$output .= apply_filters( 'ims_checkout_actions', '', $this->cart, $this->opts );
	
	$output .= '</div><!--.submit-buttons-->';
	
	$output .= '<input type="hidden" name="_wpnonce" id="_wpnonce" value="' . wp_create_nonce( "ims_submit_order" ) . '" />';
	
	$output .= apply_filters( 'ims_checkout_hidden_fields', '', $this->cart, $this->opts );
	
	$output .= '</form>';