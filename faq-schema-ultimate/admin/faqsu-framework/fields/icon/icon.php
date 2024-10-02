<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'WPFAQSU_Field_icon' ) ) {
  class WPFAQSU_Field_icon extends WPFAQSU_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'wpfaqsu' ),
        'remove_title' => esc_html__( 'Remove Icon', 'wpfaqsu' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'wpfaqsu_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="wpfaqsu-icon-select">';
      echo '<span class="wpfaqsu-icon-preview'. esc_attr( $hidden ) .'"><i class="'. esc_attr( $this->value ) .'"></i></span>';
      echo '<a href="#" class="button button-primary wpfaqsu-icon-add" data-nonce="'. esc_attr( $nonce ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button wpfaqsu-warning-primary wpfaqsu-icon-remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="wpfaqsu-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

    public function enqueue() {
      add_action( 'admin_footer', array( 'WPFAQSU_Field_icon', 'add_footer_modal_icon' ) );
      add_action( 'customize_controls_print_footer_scripts', array( 'WPFAQSU_Field_icon', 'add_footer_modal_icon' ) );
    }

    public static function add_footer_modal_icon() {
    ?>
      <div id="wpfaqsu-modal-icon" class="wpfaqsu-modal wpfaqsu-modal-icon hidden">
        <div class="wpfaqsu-modal-table">
          <div class="wpfaqsu-modal-table-cell">
            <div class="wpfaqsu-modal-overlay"></div>
            <div class="wpfaqsu-modal-inner">
              <div class="wpfaqsu-modal-title">
                <?php esc_html_e( 'Add Icon', 'wpfaqsu' ); ?>
                <div class="wpfaqsu-modal-close wpfaqsu-icon-close"></div>
              </div>
              <div class="wpfaqsu-modal-header">
                <input type="text" placeholder="<?php esc_html_e( 'Search...', 'wpfaqsu' ); ?>" class="wpfaqsu-icon-search" />
              </div>
              <div class="wpfaqsu-modal-content">
                <div class="wpfaqsu-modal-loading"><div class="wpfaqsu-loading"></div></div>
                <div class="wpfaqsu-modal-load"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

  }
}
