<?php

	/**
	 * Image Store - Images Info Metabox
	 *
	 * @file images.php
	 * @package Image Store
	 * @author Hafid Trujillo
	 * @copyright 2010-2016
	 * @filesource  wp-content/plugins/image-store/admin/galleries/images.php
	 * @since 3.2.1
	 */
		
	if ( !current_user_can( 'ims_manage_galleries') )
		die( );

	$css 			= ' alternate';
	$page 		= empty( $_GET['p'] ) ? 1 : (int) $_GET['p'];
	$status 	= isset( $_GET['status'] ) ? $_GET['status'] : 'publish';
	$order 		= empty( $this->meta['_ims_order'][0] ) ? $this->opts['imgsortdirect'] : $this->meta['_ims_order'][0];
	$orderby 	= empty( $this->meta['_ims_sortby'][0] ) ? $this->opts['imgsortorder'] : $this->meta['_ims_sortby'][0];
	
	$errors[1] = __( 'Upload failed.', 'image-store' );
	$errors[2] = __( 'Not a valid URL path', 'image-store' );
	$errors[3] = __( 'This is not a zip file.', 'image-store' );
	$errors[4] = __( 'Please enter a folder path.', 'image-store' );
	$errors[5] = __( 'There was an error extracting the images.', 'image-store' );
	$errors[6] = __( 'The folder doesn&#8217;t exist,please check your folder path.', 'image-store' );
	
	$this->is_trash = ( isset( $_GET['status'] ) ) && ( $_GET['status'] == 'trash' );
	
	$status_labels = array(
		'trash' => __( 'Trash', 'image-store' ),
		'publish' => __( 'Published', 'image-store' ),
	);
	
	$args = apply_filters('ims_pre_get_images', array(
		'order' => $order,
		'paged' => $page,
		'post_status' => $status,
		'post_type' => 'ims_image',
		'post_parent' => $this->galid,
		'orderby' => $this->sort[$orderby],
		'posts_per_page' => $this->per_page,
	),  $this );
	
	$images = new WP_Query( $args );
	
	$start = ( ( $page - 1 ) * $this->per_page );
	$current_count = ( ( ( $this->per_page * $page) - $this->per_page) + 1);
	
	$page_links = paginate_links( array(
		'current' => $page,
		'prev_text' => __('&laquo;', 'image-store'),
		'next_text' => __('&raquo;', 'image-store'),
		'total' => $images->max_num_pages,
		'format' => "&status=$status&p=%#%",
		'base' => $this->pageurl . '%_%#ims_images_box',
	) );
	
	?>

    <div class="tablenav">
    
        <ul class="subsubsub">
    	<?php $this->count_links( $status_labels, 
			array( 'type' => 'image', 'default_status' => 'publish', 'active' => $status, 'postid' => $this->galid ) 
		) ?>
        </ul><!--.subsubsub-->
        
        <div class="alignright actions">
            <select name="actions">
                <option value="0" selected="selected"><?php _e('Actions', 'image-store') ?></option>
   					 <?php if ( $this->is_trash ) { ?>
                    <option value="publish"><?php _e('Restore', 'image-store') ?></option> 
                    <option value="delete"><?php _e('Delete Permanently', 'image-store') ?></option>
   					 <?php } else { ?>
                    <option value="trash"><?php _e('Move to Trash', 'image-store') ?></option>
                	<?php } ?>
            </select>
            <input type="submit" id="gallery-image-action" value="<?php _e('Apply', 'image-store') ?>" name="doactions" class="button action" />
        </div><!--.actions-->
    </div><!--.tablenav-->
	
    
    <?php
	if ( isset( $_GET['error'] ) ) echo '<div class="error"><p><strong>' . $errors[ $_GET['error'] ] . '</strong></p></div>';
	?>
	
    <table class="hide-if-no-js widefat post fixed ims-table sort-images">
        <thead>
            <tr><?php print_column_headers( 'ims_gallery' ) ?></tr>
        </thead>
        <tbody id="media-items" class="hide-if-no-js" >
        <?php
        foreach ( $images->posts as $image ) {
            $css = ( $css == ' alternate') ? '' : ' alternate';
            $meta = (array) get_post_meta( $image->ID, '_wp_attachment_metadata', true );
			
            echo '<tr id="media-item-' . $image->ID . '" class="media-item iedit' . $css . '">';
            $this->display_image_columns( $image->ID, $meta, (array) $image );
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
    
    <input type="hidden" name="sort_count" value="<?php echo esc_attr( $current_count ) ?>" class="sort_count" />
	
    <div class="tablenav">
    	<div class="tablenav-pages">
        <?php if ( $page_links )  echo sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s', 
			number_format_i18n( $start + 1 ), number_format_i18n( min( $page * $this->per_page, $images->found_posts ) ), 
			'<span class="total-type-count">' . number_format_i18n( $images->found_posts ) . '</span>', $page_links
		);?>
        </div>
    </div><!--.tablenav-->