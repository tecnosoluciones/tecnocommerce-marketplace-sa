<?php

// Disable direct load
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

final class Wicked_Folders_Ajax {

	private static $instance;

	private function __construct() {

		add_action( 'wp_ajax_wicked_folders_save_state', 			array( $this, 'ajax_save_state' ) );
		add_action( 'wp_ajax_wicked_folders_move_object', 			array( $this, 'ajax_move_object' ) );
		add_action( 'wp_ajax_wicked_folders_add_folder', 			array( $this, 'ajax_add_folder' ) );
		add_action( 'wp_ajax_wicked_folders_clone_folder', 			array( $this, 'ajax_clone_folder' ) );
		add_action( 'wp_ajax_wicked_folders_edit_folder', 			array( $this, 'ajax_edit_folder' ) );
		add_action( 'wp_ajax_wicked_folders_delete_folder', 		array( $this, 'ajax_delete_folder' ) );
		add_action( 'wp_ajax_wicked_folders_save_folder', 			array( $this, 'ajax_save_folder' ) );
		add_action( 'wp_ajax_wicked_folders_save_sort_order', 		array( $this, 'ajax_save_sort_order' ) );
		add_action( 'wp_ajax_wicked_folders_dismiss_message', 		array( $this, 'ajax_dismiss_message' ) );
		add_action( 'wp_ajax_wicked_folders_get_child_folders', 	array( $this, 'ajax_get_child_folders' ) );
		add_action( 'wp_ajax_wicked_folders_unassign_folders', 		array( $this, 'ajax_unassign_folders' ) );
		add_action( 'wp_ajax_wicked_folders_save_folder_order', 	array( $this, 'ajax_save_folder_order' ) );
		add_action( 'wp_ajax_wicked_folders_fetch_folders', 		array( $this, 'ajax_fetch_folders' ) );

	}

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new Wicked_Folders_Ajax();
		}
		return self::$instance;
	}

	/**
	 * Admin AJAX callback for moving an item to a new folder.
	 *
	 * @uses Wicked_Folders::move_object
	 * @see Wicked_Folders::move_object
	 */
	public function ajax_move_object() {

		$result 				= array( 'error' => false, 'items' => array(), 'folders' => array() );
		$nonce 					= isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : false;
		$object_type 			= isset( $_REQUEST['object_type'] ) ? sanitize_text_field( $_REQUEST['object_type'] ) : false;
		$object_id 				= isset( $_REQUEST['object_id'] ) ? array_map( 'absint', $_REQUEST['object_id'] ) : false;
		$destination_object_id 	= isset( $_REQUEST['destination_object_id'] ) ? (int) $_REQUEST['destination_object_id'] : false;
		$source_folder_id 		= isset( $_REQUEST['source_folder_id'] ) ? (int) $_REQUEST['source_folder_id'] : false;
		$post_type 				= isset( $_REQUEST['post_type'] ) ? sanitize_text_field( $_REQUEST['post_type'] ) : false;

		/*
		if ( ! wp_verify_nonce( $nonce, 'wicked_folders_move_object' ) ) {
			$result['error'] = true;
		}
		*/

		if ( ! $object_type || ! false === $object_id || ! false === $destination_object_id ) {
			$result['error'] = true;
		}

		if ( ! $result['error'] ) {
			foreach ( $object_id as $id ) {
				Wicked_Folders::move_object( $object_type, ( int ) $id, $destination_object_id, $source_folder_id );
			}

			// Folders are used in response to update item counts
			$result['folders'] = Wicked_Folders::get_folders( $post_type );
		}

		echo json_encode( $result );

		wp_die();

	}

	/**
	 * Admin AJAX callback that unassigns folders from an item.
	 *
	 */
	public function ajax_unassign_folders() {

		$result 	= array( 'error' => false, 'items' => array(), 'folders' => array() );
		$nonce 		= isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : false;
		$taxonomy 	= isset( $_REQUEST['taxonomy'] ) ? sanitize_key( $_REQUEST['taxonomy'] ) : false;
		$object_id 	= isset( $_REQUEST['object_id'] ) ? array_map( 'absint', $_REQUEST['object_id'] ) : false;
		$post_type 	= Wicked_Folders::get_post_name_from_tax_name( $taxonomy );
		$policy 	= false;
		$user_id 	= get_current_user_id();

		if ( class_exists( 'Wicked_Folders_Folder_Collection_Policy' ) ) {
			$policy = Wicked_Folders_Folder_Collection_Policy::get_taxonomy_policy( $taxonomy );
		}

		/*
		if ( ! wp_verify_nonce( $nonce, 'wicked_folders_move_object' ) ) {
			$result['error'] = true;
		}
		*/

		if ( ! $taxonomy ) {
			$result['error'] = true;
		}

		if ( ! $result['error'] ) {
			foreach ( $object_id as $id ) {
				$folder_ids = array();

				// If a policy exists for the taxonomy, only unassign folders
				// from the object that the user has assign permission for
				if ( $policy ) {
					$folder_ids = wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'ids' ) );

					for ( $i = count( $folder_ids ) - 1; $i > -1; $i-- ) {
						if ( $policy->can_assign( $folder_ids[ $i ], $user_id ) ) {
							unset( $folder_ids[ $i ] );
						}
					}
				}

				$update_terms_result = wp_set_object_terms( ( int ) $id, $folder_ids, $taxonomy );

				$result['items'][] = array(
					'objectId' 	=> $id,
					'taxonomy' 	=> $taxonomy,
					'result' 	=> $update_terms_result,
				);
			}

			// Folders are used in response to update item counts
			$result['folders'] = Wicked_Folders::get_folders( $post_type );
		}

		echo json_encode( $result );

		wp_die();
	}

	public function ajax_save_state() {

		$result = array( 'error' => false );
		$data 	= json_decode( file_get_contents( 'php://input' ) );
		//$nonce 	= $data->nonce;
		$screen = $data->screen;
		$state 	= new Wicked_Folders_Screen_State( $screen, get_current_user_id(), $data->lang );

		$state->folder 					= isset( $data->folder->id ) ? $data->folder->id : '0';
		$state->folder_type 			= isset( $data->folder->type ) ? $data->folder->type : 'Wicked_Folders_Folder';
		$state->expanded_folders 		= $data->expanded;
		$state->tree_pane_width 		= $data->treePaneWidth;
		$state->orderby 				= $data->orderby;
		$state->order 					= $data->order;
		$state->is_folder_pane_visible 	= $data->isFolderPaneVisible;
		$state->sort_mode 				= $data->sortMode;

		if ( isset( $data->hideAssignedItems ) ) {
			$state->hide_assigned_items = $data->hideAssignedItems;
		}

		$state->save();

		echo json_encode( $result );

		wp_die();

	}

	public function ajax_add_folder() {

		$this->ajax_edit_folder();

	}

	public function ajax_edit_folder() {

		$result 	= array( 'error' => false, 'message' => __( 'An error occurred. Please try again.', 'wicked-folders' ) );
		$nonce  	= isset( $_REQUEST['nounce'] ) ? sanitize_text_field( $_REQUEST['nounce'] ) : false;
		$id 		= isset( $_REQUEST['id'] ) ? ( int ) $_REQUEST['id'] : false;
		$name 		= isset( $_REQUEST['name'] ) ? sanitize_text_field( $_REQUEST['name'] ) : false;
		$parent 	= isset( $_REQUEST['parent'] ) ? ( int ) $_REQUEST['parent'] : false;
		$post_type 	= isset( $_REQUEST['post_type'] ) ? sanitize_key( $_REQUEST['post_type'] ) : false;
		$tax_name 	= Wicked_Folders::get_tax_name( $post_type );
		$url 		= admin_url( 'edit.php?post_type=' . $post_type . '&page=' . $tax_name );

		//if ( ! wp_verify_nonce( $nonce, 'wicked_folders_add_folder' ) ) {
		//	$result['error'] = true;
		//}

		if ( ! $name || ! $post_type ) {
			$result['message'] = __( 'Invalid name or post type.', 'wicked-folders' );
			$result['error'] = true;
		}

		if ( -1 == $parent  || false === $parent ) {
			$parent = 0;
		}

		if ( ! $result['error'] ) {
			if ( $id ) {
				$existing_term = get_term_by( 'name', $name, $tax_name );
				// Don't allow terms with the same name at the same level
				if ( $existing_term && $existing_term->parent == $parent ) {
					$term = new WP_Error( 'term_exists' );
				} else {
					$term = wp_update_term( $id, $tax_name, array(
						'name' 		=> $name,
						'parent' 	=> $parent,
					) );
				}
			} else {
				$term = wp_insert_term( $name, $tax_name, array(
					'parent' => $parent,
				) );
			}
			if ( is_wp_error( $term ) ) {
				if ( isset( $term->errors['term_exists'] ) ) {
					$result['message'] = __( 'A folder with that name already exists in the selected parent folder. Please enter a different name or select a different parent folder.', 'wicked-folders' );
				} else {
					$result['message'] = $term->get_error_message();
				}
				$result['error'] = true;
			} else {
				$select = wp_dropdown_categories( array(
					'orderby'           => 'name',
					'order'             => 'ASC',
					'show_option_none'  => '&mdash; ' . __( 'Parent Folder', 'wicked-folders' ) . ' &mdash;',
					'taxonomy'          => $tax_name,
					'depth'             => 0,
					'hierarchical'      => true,
					'hide_empty'        => false,
					'selected'          => $parent,
					'echo' 				=> false,
					'option_none_value' => 0,
				) );
				$result = array(
					'error' 	=> false,
					'folderId' 	=> $term['term_id'],
					'folderUrl' => add_query_arg( 'folder', $term['term_id'], $url ),
					'select' 	=> $select,
				);
			}
		}

		echo json_encode( $result );

		wp_die();

	}

	public function ajax_delete_folder() {

		// TODO: check nonce
		$result 	= array( 'error' => false );
		$nonce  	= isset( $_REQUEST['nounce'] ) ? sanitize_text_field( $_REQUEST['nounce'] ) : false;
		$id 		= isset( $_REQUEST['id'] ) ? ( int ) $_REQUEST['id'] : false;
		$post_type 	= isset( $_REQUEST['post_type'] ) ? sanitize_key( $_REQUEST['post_type'] ) : false;
		$taxonomy 	= isset( $_REQUEST['taxonomy'] ) ? sanitize_key( $_REQUEST['taxonomy'] ) : Wicked_Folders::get_tax_name( $post_type );

		$delete_result = wp_delete_term( $id, $taxonomy );

		if ( is_wp_error( $delete_result ) ) {
			$result['error'] 	= true;
			$result['message'] 	= $delete_result->get_error_message();
		}

		echo json_encode( $result );

		wp_die();

	}

	public function ajax_save_folder() {

		$response 	= array( 'error' => false );
		//$method 	= $_SERVER['REQUEST_METHOD'];
		$method 	= isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : 'POST';
		$method 	= isset( $_REQUEST['_method_override'] ) ? sanitize_text_field( $_REQUEST['_method_override'] ) : $method;
		$folder		= json_decode( file_get_contents( 'php://input' ) );
		$policy 	= false;
		$user_id 	= get_current_user_id();

		if ( 'DELETE' == $method ) {
			$folder_id 	= ( int ) $_REQUEST['id'];
			$taxonomy 	= sanitize_key( $_REQUEST['taxonomy'] );
		} else {
			$folder_id 	= $folder->id;
			$taxonomy 	= $folder->taxonomy;
		}

		// The Polylang plugin uses the jQuery ajaxPrefilter function to alter
		// AJAX requests which breaks the request (see polylang/js/media.js).
		// The following code checks to see if the Polylang plugin is active and,
		// if so, removes the string added by the Polylang plugin so the request
		// can be processed properly.
		if ( function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) ) {
			$data 	= file_get_contents( 'php://input' );
			$data 	= preg_replace( '/^pll_post_id=([0-9|undefined]*)?&/', '', $data );
			$data 	= preg_replace( '/&pll_ajax_backend=1/', '', $data );
			$folder = json_decode( $data );
		}

		// Similar issue with Anything Order by Terms plugin; adds a screen_id
		// parameter (see anything-order-by-terms/modules/base/script.js) which
		// breaks the request
		if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'anything-order-by-terms/anything-order.php' ) ) {
			$data 	= file_get_contents( 'php://input' );
			$data 	= preg_replace( '/&screen_id=([A-Z\-\_0-9]*)/i', '', $data );
			$folder = json_decode( $data );
		}

		if ( class_exists( 'Wicked_Folders_Folder_Collection_Policy' ) ) {
			$policy = Wicked_Folders_Folder_Collection_Policy::get_taxonomy_policy( $taxonomy );

			// If there's a security policy, enforce it
			if ( $policy ) {
				if (
					( 'POST' == $method && false == $policy->can_create( $user_id ) ) ||
					( 'PUT' == $method && false == $policy->can_edit( $folder_id, $user_id ) ) ||
					( 'DELETE' == $method && false == $policy->can_delete( $folder_id, $user_id ) )
				) {
					$response['message'] 	= __( 'Permission denied.', 'wicked-folders' );
					$response['error'] 		= true;

					status_header( 400 );

					echo json_encode( $response );

					die();
				}
			}
		}

		// Insert folder
		if ( 'POST' == $method ) {
			// TODO: Refactor. We should be working with a proper folder object
			// that is initalized from the JSON in the request and then
			// serialized as JSON
			$term = wp_insert_term( $folder->name, $folder->taxonomy, array(
				'parent' 	=> $folder->parent,
				'slug' 		=> Wicked_Folders_Term_Folder::generate_unique_slug( $folder->name, $folder->taxonomy ),
			) );

			if ( ! is_wp_error( $term ) ) {
				$owner_data 		= get_userdata( $user_id );
				$folder->id 		= ( string ) $term['term_id'];
				$folder->ownerId 	= $user_id;
				$folder->ownerName 	= isset( $owner_data->data->display_name ) ? $owner_data->data->display_name : '';

				add_term_meta( $term['term_id'], 'wf_owner_id', $user_id );
			}
		}

		// Update folder
		if ( 'PUT' == $method ) {
			$term = wp_update_term( $folder->id, $folder->taxonomy, array(
				'name' 		=> $folder->name,
				'parent' 	=> $folder->parent,
			) );

			update_term_meta( $folder->id, 'wf_owner_id', ( int ) $folder->ownerId );
		}

		// Delete folder
		if ( 'DELETE' == $method ) {
			$term = wp_delete_term( ( int ) $_REQUEST['id'], sanitize_key( $_REQUEST['taxonomy'] ) );
			// Delete the sort meta for the folder
			delete_metadata( 'post', 0, '_wicked_folder_order__' . sanitize_key( $_REQUEST['taxonomy'] ) . '__' . sanitize_text_field( $_REQUEST['id'] ), false, true );
		}

		if ( is_wp_error( $term ) ) {
			if ( isset( $term->errors['term_exists'] ) ) {
				$response['message'] = __( 'A folder with that name already exists in the selected parent folder. Please enter a different name or select a different parent folder.', 'wicked-folders' );
			} else {
				$response['message'] = $term->get_error_message();
			}
			$response['error'] = true;
			status_header( 400 );
			echo json_encode( $response );
			die();
		} else {
			echo json_encode( $folder );
		}

		wp_die();

	}

	public function ajax_clone_folder() {
		$folders 		= array();
		$id 			= isset( $_REQUEST['id'] ) ? ( int ) $_REQUEST['id'] : false;
		$post_type 		= isset( $_REQUEST['post_type'] ) ? sanitize_key( $_REQUEST['post_type'] ) : false;
		$parent 		= isset( $_REQUEST['parent'] ) ? ( int ) $_REQUEST['parent'] : false;
		$clone_children = isset( $_REQUEST['clone_children'] ) && 'true' == $_REQUEST['clone_children'] ? true : false;
		$taxonomy 		= Wicked_Folders::get_tax_name( $post_type );
		$user_id 		= get_current_user_id();

		try {
			if ( class_exists( 'Wicked_Folders_Folder_Collection_Policy' ) ) {
				$policy = Wicked_Folders_Folder_Collection_Policy::get_taxonomy_policy( $taxonomy );

				// If there's a security policy, enforce it
				if ( $policy ) {
					// Require edit permission to clone folder
					if ( ! $policy->can_edit( $id, $user_id ) ) {
						throw new Exception( __( 'Permission denied.', 'wicked-folders' ) );
					}
				}
			}

			$folder 	= Wicked_Folders::get_folder( $id, $post_type );
			$folders 	= $folder->clone_folder( $clone_children, $parent );

			echo json_encode( $folders );
		} catch ( Exception $e ) {
			status_header( 400 );

			echo esc_html( $e->getMessage() );

			die();
		}

		wp_die();
	}

	public function ajax_save_sort_order() {

		global $wpdb;

		$new_order 		= array();
		$screen 		= sanitize_text_field( $_REQUEST['screen'] );
		$folder_id 		= sanitize_text_field( $_REQUEST['folder_id'] );
		$post_type 		= sanitize_text_field( $_REQUEST['post_type'] );
		$taxonomy 		= sanitize_text_field( $_REQUEST['taxonomy'] );
		$object_ids 	= array_map( 'absint', $_REQUEST['object_ids'] );
		$order 			= sanitize_text_field( $_REQUEST['order'] );
		$orderby 		= sanitize_text_field( $_REQUEST['orderby'] );
		$page_number 	= ( int ) $_REQUEST['page_number'];
		$items_per_page = ( int ) $_REQUEST['items_per_page'];
		$sort_key 		= '_wicked_folder_order__' . $taxonomy . '__' . $folder_id;
		$before 		= $items_per_page * ( $page_number - 1 );
		$after 			= $before + $items_per_page;

		// Initialize folder order. This will ensure that every post in the folder
		// has an order meta key so that we can update later
		Wicked_Folders::initalize_folder_order( $folder_id, $taxonomy );

		// Get IDs of posts assigned to the folder ordered the same way as they
		// were prior to changing the sort order
		$q = array(
		    'post_type'         => $post_type,
		    'posts_per_page'    => -1,
			'fields' 			=> 'ids',
		    'order'             => $order,
			'orderby'           => $orderby,
			'tax_query' => array(
				array(
					'taxonomy' 	=> $taxonomy,
					'field' 	=> 'term_id',
					'terms' 	=> ( int ) $folder_id,
				)
			)
		);

		if ( 'wicked_folder_order' == $orderby ) {
			$q['orderby'] = array(
				'meta_value_num' 	=> $order,
				'title' 			=> 'ASC',
			);
			$q['meta_key'] = $sort_key;
		}

		$post_ids = get_posts( $q );

		$n = count( $post_ids );

		// Get the order of posts on previous pages
		for ( $i = 0; $i < $before; $i++ ) {
			$new_order[] = $post_ids[ $i ];
		}

		// Append the new order of posts for the current page
		$new_order = array_merge( $new_order, $object_ids );

		// Add posts from subsequent pages
		for ( $i = $after; $i < $n; $i++ ) {
			$new_order[] = $post_ids[ $i ];
		}

		// Get the current sort orders
		$current_order = $wpdb->get_results( $wpdb->prepare( "SELECT post_id, meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s ORDER BY post_id", $sort_key ), OBJECT_K );

		foreach ( $new_order as $index => $post_id ) {
			$sort = ( $n - $index ) * -1;
			// Only update posts where the sort order has changed
			if ( $sort != $current_order[ $post_id ]->meta_value ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}postmeta SET meta_value = %s WHERE post_id = %d AND meta_key = %s", $sort, $post_id, $sort_key ) );
			}
		}

	}

	public function ajax_dismiss_message() {
		$result 				= array( 'error' => false );
		$dismissed_messages 	= ( array ) get_user_option( 'wicked_folders_dismissed_messages' );
		$dismissed_messages[] 	= $_POST['key'];

		update_user_meta( get_current_user_id(), 'wicked_folders_dismissed_messages', $dismissed_messages );

		echo json_encode( $result );

		wp_die();
	}

	public function ajax_get_child_folders() {
		global $wpdb;

		$folders 		= array();
		$folder_type 	= sanitize_text_field( $_REQUEST['folder_type'] );
		$folder_id 		= ( int ) $_REQUEST['folder_id'];
		$post_type 		= sanitize_key( $_REQUEST['post_type'] );

		$folder = Wicked_Folders::get_dynamic_folder( $folder_type, $folder_id, $post_type );

		if ( $folder ) {
			$folder->fetch();
			$folders = $folder->get_child_folders();
		}

		echo json_encode( $folders );

		wp_die();
	}

	public function ajax_save_folder_order() {
		global $wpdb;

		$result  			= array( 'error' => false );
		$folders 			= isset( $_REQUEST['folders'] ) && is_array( $_REQUEST['folders' ] ) ? array_map( array( $this, 'sanitize_folder_order_param' ), $_REQUEST['folders'] ) : array();
		$order_field_exists = Wicked_Folders::get_instance()->term_order_field_exists();

		foreach ( $folders as $folder ) {
			update_term_meta( $folder['id'], 'wf_order', ( int ) $folder['order'] );

			// Update wp_terms.term_order if the field exists. This field is
			// used by the Category Order and Taxonomy Terms Order plugin so
			// this should ensure that the folders appear in the expected order
			// for users who use this plugin
			if ( $order_field_exists ) {
				$wpdb->update(
					$wpdb->terms,
					array( 'term_order' => $folder['order'] ),
					array( 'term_id' => ( int ) $folder['id'] ),
					array( '%d' ),
					array( '%d' )
				);
			}
		}

		echo json_encode( $result );

		wp_die();
	}

	public function ajax_fetch_folders() {
		$folders 	= array();
		$taxonomy 	= isset( $_GET['taxonomy'] ) ?  sanitize_text_field( $_GET['taxonomy'] ) : false;

		if ( $taxonomy ) {
			$post_type = Wicked_Folders::get_post_name_from_tax_name( $taxonomy );

			$folders = Wicked_Folders::get_folders( $post_type, $taxonomy );
		}

		echo json_encode( $folders );

		wp_die();
	}

	/**
	 * Sanitizes the value of an entry within a folder order array.
	 */
	public function sanitize_folder_order_param( $value ) {
		return array(
			'id' 	=> ( int ) $value['id'],
			'order' => ( int ) $value['order'],
		);
	}
}
