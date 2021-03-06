<?php

	/**
	 * Image Store - tinymce ajax
	 *
	 * @file tinymce.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/_js/tinymce/tinymce.php
	 * @since 3.0.0
	 */

	 //dont cache file
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Last-Modified:' . gmdate( 'D,d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-control:no-cache,no-store,must-revalidate,max-age=0');

	//define constants
	define( 'WP_ADMIN', true );
	define( 'DOING_AJAX', true );

	$_SERVER['PHP_SELF']  = "/wp-admin/tinymce.php";

	//load wp
	require_once implode( '/', explode( '/', $_SERVER['SCRIPT_FILENAME'], -6 ) ) . '/wp-load.php';

	if ( !current_user_can( "ims_manage_galleries")  )
		die();

	$wpjs_url = site_url( "/wp-includes/js/" );
	$admin_body_class = ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );

	$defaults = array( 'id' => '', 'caption' => false, 'layout' => 'lightbox', 'sort' => 0, 'sortby' => 0, 'number' => 'all', 'linkto' => 'file' );
	extract( wp_parse_args( $_GET, $defaults ) );

	?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <title><?php _e( 'Attach Gallery',  'image-store' )?></title>
        <meta name="robots" content="index,follow,noodp,noydir" />

				<?php
				wp_admin_css( );
				do_action( 'admin_print_styles' );
				?>

		<link rel="stylesheet" href="<?php echo IMSTORE_URL ?>/_css/tinymceform.css?ver=300-1235">
		<script type="text/javascript">
			var imslocal = {
				imsajax 		: '<?php echo IMSTORE_ADMIN_URL . "/ajax.php"?>',
				nonceajax 	: '<?php echo wp_create_nonce( 'ims_ajax' )  ?>'
			}
		</script>
    <script type="text/javascript" src="<?php echo $wpjs_url ?>jquery/jquery.js?ver=300-1235"></script>
    </head>

    <body id="ims-galleries" class="hide no-js <?php echo apply_filters( 'admin_body_class', '' ) . " $admin_body_class"; ?>">
    	<form  method="post" id="image-store-embed">
        <div id="gal-selector">
            <div id="gal-options"><br>
              <div>
                <label><span><?php _e( 'Images', 'image-store'); ?></span>
                <input id="number" type="text" name="number" value="<?php echo esc_attr( $number )?>" />
                <small><?php _e( 'How many images to display.', 'image-store'); ?></small></label>
              </div>

              <div>
                <label><span><?php _e( 'Gallery id', 'image-store'); ?></span>
                <input id="galid" type="text" class="regular-text" name="id" value="<?php echo esc_attr( $id )?>" /></label>
              </div>

              <div>
                <label><span><?php _e( 'Show as', 'image-store'); ?></span></label>
                <label><input type="radio" name="layout" value="lightbox" id="lightbox"
									<?php checked( $layout , 'lightbox' )?>/><?php _e( 'Lightbox', 'image-store'); ?></label>
                <label><input type="radio" name="layout" value="slideshow" id="slideshow"
                  <?php checked( $layout, 'slideshow' )?> /><?php _e( 'Slideshow', 'image-store'); ?></label>
			 					<label><input type="radio" name="layout" value="simple_slideshow" id="slideshow"
                  <?php checked( $layout, 'simple_slideshow' )?> /><?php _e( 'Simple Slideshow', 'image-store'); ?></label>
                <label><input type="radio" name="layout"  value="list" id="list" <?php checked( $layout, 'list' )?> /><?php _e( 'List', 'image-store'); ?></label>
              </div>

              <div>
                <label><span><?php _e( 'Sort by', 'image-store'); ?></span> <select name="orderby" id="orderby">
                  <option value="0"><?php _e( 'Default', 'image-store'); ?></option>
                  <option value="date" <?php selected( $sortby, 'date' )?>><?php _e( 'Date', 'image-store'); ?></option>
                  <option value="title" <?php selected( $sortby, 'title' )?>><?php _e( 'Title', 'image-store'); ?></option>
                  <option value="custom" <?php selected( $sortby, 'custom' )?>><?php _e( 'Custom', 'image-store'); ?></option>
                  <option value="caption" <?php selected( $sortby, 'caption' )?>><?php _e( 'Caption', 'image-store'); ?></option>
                </select></label>

                <label><?php _e( 'Order', 'image-store'); ?> <select name="order" id="order">
                  <option value="0"><?php _e( 'Default', 'image-store'); ?></option>
                  <option value="asc" <?php selected( $sort, 'asc' )?>><?php _e( 'Ascending', 'image-store'); ?></option>
                  <option value="desc" <?php selected( $sort, 'desc' )?>><?php _e( 'Descending', 'image-store'); ?></option>
                </select></label>
              </div>

              <div>
                <label><span><?php _e( 'Link to:', 'image-store'); ?></span></label>
                <label><input type="radio" name="linkto" value="file" id="file" <?php checked( $linkto, 'file' )?> /><?php _e( 'File', 'image-store'); ?></label>
                <label><input type="radio" name="linkto" value="attachment" id="attachment"
									<?php checked( $linkto, 'attachment' )?> /><?php _e( 'Attachment', 'image-store'); ?></label>
              </div>

              <div>
                <label><span>&nbsp;</span><input id="caption" type="checkbox" name="caption" <?php checked( $caption, 'on' )?> />
                <?php _e( 'Show caption', 'image-store'); ?></label>
              </div>
            </div>
            <?php $show_internal = '1' == get_user_setting( 'imsattach', '0' ); ?>
            <p class="howto toggle-arrow <?php if ( $show_internal ) echo 'toggle-arrow-active'; ?>" id="internal-toggle">
            <?php _e( 'or search galleries', 'image-store'); ?></p>
            <div id="search-panel"<?php if ( !$show_internal ) echo ' class="hide"'; ?>>
              <div class="link-search-wrapper">
                <label>
                  <span><?php _e( 'Search' ); ?></span>
                  <input type="text" id="search-field" class="link-search-field regular-text" autocomplete="off" />
                  <img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
                </label>
              </div>
              <div id="search-results" class="query-results">
                <ul></ul>
                <div class="river-waiting">
                  <img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
                </div>
              </div>
            </div>
          </div>

        <div class="submitbox mceActionPanel">
              <div id="ims-link-cancel" style="float:left">
                <input type="button" id="cancel" name="cancel" value="<?php esc_attr_e( 'Cancel', 'image-store')?>" class="button" />
              </div>

              <div id="ims-link-insert" style="float:right">
                <input type="submit" id="insert" name="insert" value="<?php esc_attr_e( 'Insert', 'image-store')?>" class="button-primary" />
              </div>
            </div>
      </form>
			<script type="text/javascript" src="tinymce.js?ver=321-300"></script>
	</body>
</html>
