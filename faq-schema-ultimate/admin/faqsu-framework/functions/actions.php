<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'wpfaqsu_get_icons' ) ) {
  function wpfaqsu_get_icons() {

    $nonce = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'wpfaqsu_icon_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'wpfaqsu' ) ) );
    }

    ob_start();

    $icon_library = ( apply_filters( 'wpfaqsu_fa4', false ) ) ? 'fa4' : 'fa5';

    WPFAQSU::include_plugin_file( 'fields/icon/'. $icon_library .'-icons.php' );

    $icon_lists = apply_filters( 'wpfaqsu_field_icon_add_icons', wpfaqsu_get_default_icons() );

    if ( ! empty( $icon_lists ) ) {

      foreach ( $icon_lists as $list ) {

        echo ( count( $icon_lists ) >= 2 ) ? '<div class="wpfaqsu-icon-title">'. esc_attr( $list['title'] ) .'</div>' : '';

        foreach ( $list['icons'] as $icon ) {
          echo '<i title="'. esc_attr( $icon ) .'" class="'. esc_attr( $icon ) .'"></i>';
        }

      }

    } else {

      echo '<div class="wpfaqsu-error-text">'. esc_html__( 'No data available.', 'wpfaqsu' ) .'</div>';

    }

    $content = ob_get_clean();

    wp_send_json_success( array( 'content' => $content ) );

  }
  add_action( 'wp_ajax_wpfaqsu-get-icons', 'wpfaqsu_get_icons' );
}

/**
 *
 * Export
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'wpfaqsu_export' ) ) {
  function wpfaqsu_export() {

    $nonce  = ( ! empty( $_GET[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'nonce' ] ) ) : '';
    $unique = ( ! empty( $_GET[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_GET[ 'unique' ] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'wpfaqsu_backup_nonce' ) ) {
      die( esc_html__( 'Error: Invalid nonce verification.', 'wpfaqsu' ) );
    }

    if ( empty( $unique ) ) {
      die( esc_html__( 'Error: Invalid key.', 'wpfaqsu' ) );
    }

    // Export
    header('Content-Type: application/json');
    header('Content-disposition: attachment; filename=backup-'. gmdate( 'd-m-Y' ) .'.json');
    header('Content-Transfer-Encoding: binary');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo json_encode( get_option( $unique ) );

    die();

  }
  add_action( 'wp_ajax_wpfaqsu-export', 'wpfaqsu_export' );
}

/**
 *
 * Import Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'wpfaqsu_import_ajax' ) ) {
  function wpfaqsu_import_ajax() {

    $nonce  = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';
    $unique = ( ! empty( $_POST[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'unique' ] ) ) : '';
    $data   = ( ! empty( $_POST[ 'data' ] ) ) ? wp_kses_post_deep( json_decode( wp_unslash( trim( $_POST[ 'data' ] ) ), true ) ) : array();

    if ( ! wp_verify_nonce( $nonce, 'wpfaqsu_backup_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'wpfaqsu' ) ) );
    }

    if ( empty( $unique ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid key.', 'wpfaqsu' ) ) );
    }

    if ( empty( $data ) || ! is_array( $data ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: The response is not a valid JSON response.', 'wpfaqsu' ) ) );
    }

    // Success
    update_option( $unique, $data );

    wp_send_json_success();

  }
  add_action( 'wp_ajax_wpfaqsu-import', 'wpfaqsu_import_ajax' );
}

/**
 *
 * Reset Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'wpfaqsu_reset_ajax' ) ) {
  function wpfaqsu_reset_ajax() {

    $nonce  = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';
    $unique = ( ! empty( $_POST[ 'unique' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'unique' ] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'wpfaqsu_backup_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'wpfaqsu' ) ) );
    }

    // Success
    delete_option( $unique );

    wp_send_json_success();

  }
  add_action( 'wp_ajax_wpfaqsu-reset', 'wpfaqsu_reset_ajax' );
}

/**
 *
 * Chosen Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'wpfaqsu_chosen_ajax' ) ) {
  function wpfaqsu_chosen_ajax() {

    $nonce = ( ! empty( $_POST[ 'nonce' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'nonce' ] ) ) : '';
    $type  = ( ! empty( $_POST[ 'type' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'type' ] ) ) : '';
    $term  = ( ! empty( $_POST[ 'term' ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'term' ] ) ) : '';
    $query = ( ! empty( $_POST[ 'query_args' ] ) ) ? wp_kses_post_deep( $_POST[ 'query_args' ] ) : array();

    if ( ! wp_verify_nonce( $nonce, 'wpfaqsu_chosen_ajax_nonce' ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid nonce verification.', 'wpfaqsu' ) ) );
    }

    if ( empty( $type ) || empty( $term ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: Invalid term ID.', 'wpfaqsu' ) ) );
    }

    $capability = apply_filters( 'wpfaqsu_chosen_ajax_capability', 'manage_options' );

    if ( ! current_user_can( $capability ) ) {
      wp_send_json_error( array( 'error' => esc_html__( 'Error: You do not have permission to do that.', 'wpfaqsu' ) ) );
    }

    // Success
    $options = WPFAQSU_Fields::field_data( $type, $term, $query );

    wp_send_json_success( $options );

  }
  add_action( 'wp_ajax_wpfaqsu-chosen', 'wpfaqsu_chosen_ajax' );
}
