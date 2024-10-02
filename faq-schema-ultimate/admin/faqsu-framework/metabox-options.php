<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Metabox of the POST
// Set a unique slug-like ID
//
$prefix_post_opts = '_prefix_post_options';

//
// Create a metabox
//
WPFAQSU::createMetabox( $prefix_post_opts, array(
  'title'        => 'FAQ Post Options',
  'post_type'    => 'post',
  // 'show_restore' => true,
) );

//
// Create a section
//
WPFAQSU::createSection( $prefix_post_opts, array(
  'fields' => array(
    
    array(
      'id'    => 'opt-switcher-1',
      'type'  => 'switcher',
      'title' => 'FAQ On/Off This Post',
      'default'    => false,
    ),

    array(
      'id'        => 'opt-group-1',
      'type'      => 'group',
      'title'     => 'FAQ Schema',
      'button_title' => '+ Add New',
      'fields'    => array(

        array(
          'id'    => 'opt-text',
          'type'  => 'text',
          'title' => 'FAQ Title',
        ),
        array(
          'id'    => 'opt-wp-editor-1',
          'type'  => 'wp_editor',
          'title' => 'FAQ Description',
        ),
      ),

      'default'   => array(
        array(
          'opt-text'     => 'FAQ title',
          'opt-wp-editor-1'     => 'FAQ description',
        ),
      ),
      'dependency' => array( 'opt-switcher-1', '==', 'true' ),
    ),

    array(
      'type'  => 'shortcode',
      'title'     => 'Shortcode',
      'class' => 'wpgp--shortcode-field',
      'dependency' => array( 'opt-switcher-1', '==', 'true' ),
    ),

  )
) );