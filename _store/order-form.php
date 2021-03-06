	<?php

	/**
	 * Image Store - Order Form
	 *
	 * @file order-form.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/_store/order-form.php
	 * @since 0.5.0
	 */

	// Stop direct access of the file
	if ( !defined( 'ABSPATH' ) )
		die( );
		
	$form =
	  '<form id="ims-pricelist" method="post"> ' . apply_filters( 'ims_before_order_form', '', $this ) . '
		  <div class="ims-image-count"><em></em> ' . __( 'Selected', 'image-store' ) . '<span class="touch-close">' . __( 'Close', 'image-store' ) . '</span></div>
		  <div class="ims-add-error">' . __( 'There are no images selected', 'image-store' ) . '</div>
		  <div class="ims-instructions">' . __( 'These preferences will be apply to all selected images', 'image-store' ) . '</div>
		  
		  <div class="ims-prlicelist-wrap">
		  <div class="ims-field">
			  <label for="ims-quantity">' . __( 'Quantity', 'image-store' ) . ' </label>
			  <input name="ims-quantity" type="text" class="inputsm" id="ims-quantity" value="1" />
		  </div><!--.ims-field-->';
	 
		//color options
		if ( !empty( $this->listmeta['colors'] ) ) :
			 
			$form .= '
			<div class="ims-field"> 
			  <label for="imstore-color">' . __( 'Color', 'image-store' ) . ' </label>
			  <select name="imstore-color" id="imstore-color" class="select">';
			  foreach ( $this->listmeta['colors'] as $key => $color )
				$form .= '<option value="' . esc_attr( $key ) . '">' . $color['name'] . '</option>';
			$form .= ' </select></div><!--.ims-field-->';
		  
		endif;
	
	
		//finishes
		if ( !empty( $this->listmeta['finishes'] ) ) :
		
		$form .= '
		<div class="ims-field"> 
		<label for="imstore-finish">' . __( 'Photo Finish', 'image-store' ) . ' </label>
			<select name="imstore-finish" id="imstore-finish" class="select">';
			foreach ( $this->listmeta['finishes'] as $key => $finish ) 
				$form .= '<option value="' . esc_attr( $key ) . '">' . $finish['name'] . '</option>';
		$form .= ' </select></div><!--.ims-field-->';
		
		endif;
	
	
		//sizes
		$form .= '<span class="ims-image-size">' . __( 'Sizes', 'image-store' ) . '</span>';
		$form .='<div class="ims-image-sizes">';
		
		
		if ( $this->sizes ){
			
			foreach ( $this->sizes as $size ){
				$form .= '<label> <input type="checkbox" name="ims-image-size[]" value="';
				
				if ( isset( $size['ID'] ) ){  
					
					$package_sizes = '';
					$form .= esc_attr( $size['name'] ) . '" /> ' . $size['name'] . ': ';
					
					foreach ( (array) get_post_meta( $size['ID'], '_ims_sizes', true ) as $package_size => $count ){
						if ( is_array( $count ) )
							$package_sizes .= $package_size . '<span class="ims-unit">' . $count['unit'] . '</span> <span class="ims-pcount">( ' . $count['count'] . ' )</span>, ';
						else $package_sizes .= $package_size . '<span class="ims-scount"> ( ' . $count . ' )</span>, ';
					};
					
					$form .= rtrim( " &mdash; " . $package_sizes, ', ' ) . " </label>\n";
				
				}else{
				
					$form .= esc_attr( $size['name'] ) . '" /> ' . $size['name'] . " &mdash; " . $this->format_price( $size['price'] ) . " </label>";
				}
			}
		}
		
	$form .= '</div><!--.ims-image-sizes-->';  
	  
	$form .=
		'<div class="ims-field ims-submit">
			  <a href="' . esc_url( apply_filters( 'ims_form_checkout_url', $this->get_permalink( 'shopping-cart' )  ) ) . '" rel="nofollow" class="button checkout-button">' . 
			  esc_html__( 'Checkout', 'image-store' ) . '</a>
			  <input type="submit" name="ims-add-to-cart" value="' . esc_attr__( 'Add to cart', 'image-store' ) . '" class="button primary" />
			  <input type="hidden" name="_wpnonce" value="' . esc_attr( wp_create_nonce( "ims_add_to_cart" ) ) . '" />
			  <input type="hidden" name="ims-to-cart-ids" id="ims-to-cart-ids" />
		 </div>
	  </div><!--.ims_prlicelist-wrap-->';

	
	 $form .= apply_filters( 'ims_after_order_form', '', $this );

	 $form .='</form><!--#ims-pricelis-->';