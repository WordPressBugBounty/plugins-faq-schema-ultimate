<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: map
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'WPFAQSU_Field_map' ) ) {
  class WPFAQSU_Field_map extends WPFAQSU_Fields {

    public $version = '1.7.1';
    public $cdn_url = 'https://cdn.jsdelivr.net/npm/leaflet@';

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args              = wp_parse_args( $this->field, array(
        'placeholder'    => esc_html__( 'Search...', 'wpfaqsu' ),
        'latitude_text'  => esc_html__( 'Latitude', 'wpfaqsu' ),
        'longitude_text' => esc_html__( 'Longitude', 'wpfaqsu' ),
        'address_field'  => '',
        'height'         => '',
      ) );

      $value             = wp_parse_args( $this->value, array(
        'address'        => '',
        'latitude'       => '20',
        'longitude'      => '0',
        'zoom'           => '2',
      ) );

      $default_settings   = array(
        'center'          => array( $value['latitude'], $value['longitude'] ),
        'zoom'            => $value['zoom'],
        'scrollWheelZoom' => false,
      );

      $settings = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
      $settings = wp_parse_args( $settings, $default_settings );

      $style_attr  = ( ! empty( $args['height'] ) ) ? ' style="min-height:'. esc_attr( $args['height'] ) .';"' : '';
      $placeholder = ( ! empty( $args['placeholder'] ) ) ? array( 'placeholder' => $args['placeholder'] ) : '';

      echo $this->field_before();

      if ( empty( $args['address_field'] ) ) {
        echo '<div class="wpfaqsu--map-search">';
        echo '<input type="text" name="'. esc_attr( $this->field_name( '[address]' ) ) .'" value="'. esc_attr( $value['address'] ) .'"'. $this->field_attributes( $placeholder ) .' />';
        echo '</div>';
      } else {
        echo '<div class="wpfaqsu--address-field" data-address-field="'. esc_attr( $args['address_field'] ) .'"></div>';
      }

      echo '<div class="wpfaqsu--map-osm-wrap"><div class="wpfaqsu--map-osm" data-map="'. esc_attr( json_encode( $settings ) ) .'"'. $style_attr .'></div></div>';

      echo '<div class="wpfaqsu--map-inputs">';

        echo '<div class="wpfaqsu--map-input">';
        echo '<label>'. esc_attr( $args['latitude_text'] ) .'</label>';
        echo '<input type="text" name="'. esc_attr( $this->field_name( '[latitude]' ) ) .'" value="'. esc_attr( $value['latitude'] ) .'" class="wpfaqsu--latitude" />';
        echo '</div>';

        echo '<div class="wpfaqsu--map-input">';
        echo '<label>'. esc_attr( $args['longitude_text'] ) .'</label>';
        echo '<input type="text" name="'. esc_attr( $this->field_name( '[longitude]' ) ) .'" value="'. esc_attr( $value['longitude'] ) .'" class="wpfaqsu--longitude" />';
        echo '</div>';

      echo '</div>';

      echo '<input type="hidden" name="'. esc_attr( $this->field_name( '[zoom]' ) ) .'" value="'. esc_attr( $value['zoom'] ) .'" class="wpfaqsu--zoom" />';

      echo $this->field_after();

    }

    public function enqueue() {

      if ( ! wp_script_is( 'wpfaqsu-leaflet' ) ) {
        wp_enqueue_script( 'wpfaqsu-leaflet', esc_url( $this->cdn_url . $this->version .'/dist/leaflet.js' ), array( 'wpfaqsu' ), $this->version, true );
      }

      if ( ! wp_style_is( 'wpfaqsu-leaflet' ) ) {
        wp_enqueue_style( 'wpfaqsu-leaflet', esc_url( $this->cdn_url . $this->version .'/dist/leaflet.css' ), array(), $this->version );
      }

      if ( ! wp_script_is( 'jquery-ui-autocomplete' ) ) {
        wp_enqueue_script( 'jquery-ui-autocomplete' );
      }

    }

  }
}
