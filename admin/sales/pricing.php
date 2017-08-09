<?php

	/**
	 * Image Store - Admin Pricing
	 *
	 * @file pricing.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/admin/sales/pricing.php
	 * @since 0.5.0
	 */

	 if ( !current_user_can( 'ims_change_pricing' ) )
		die( );

	?>

	<ul class="ims-tabs add-menu-item-tabs">
	<?php foreach ( $this->tabs as $tabid => $tab )
	  echo '<li class="tabs"><a href="#' . esc_attr( $tabid ) . '">' . esc_html( $tab ) . '</a></li> ';?>
	</ul>

	<?php foreach ( $this->tabs as $tabid => $tabname ) {
	  echo '<div id="' . esc_attr( $tabid ) . '" class="ims-box pricing" >';
	  do_action( "ims_pricing_{$tabid}_tab", $this );
	  echo '</div>';
	}

	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
