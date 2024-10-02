<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'WPFAQSU_Field_backup' ) ) {
  class WPFAQSU_Field_backup extends WPFAQSU_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'wpfaqsu_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'wpfaqsu-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      echo $this->field_before();

      echo '<textarea name="wpfaqsu_import_data" class="wpfaqsu-import-data"></textarea>';
      echo '<button type="submit" class="button button-primary wpfaqsu-confirm wpfaqsu-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Import', 'wpfaqsu' ) .'</button>';
      echo '<hr />';
      echo '<textarea readonly="readonly" class="wpfaqsu-export-data">'. esc_attr( json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary wpfaqsu-export" target="_blank">'. esc_html__( 'Export & Download', 'wpfaqsu' ) .'</a>';
      echo '<hr />';
      echo '<button type="submit" name="wpfaqsu_transient[reset]" value="reset" class="button wpfaqsu-warning-primary wpfaqsu-confirm wpfaqsu-reset" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Reset', 'wpfaqsu' ) .'</button>';

      echo $this->field_after();

    }

  }
}
