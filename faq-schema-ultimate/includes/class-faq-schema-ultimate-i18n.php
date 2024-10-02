<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpqode.com/
 * @since      1.0.0
 *
 * @package    Faq_Schema_Ultimate
 * @subpackage Faq_Schema_Ultimate/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Faq_Schema_Ultimate
 * @subpackage Faq_Schema_Ultimate/includes
 * @author     WPQode <help@wpqode.com>
 */
class Faq_Schema_Ultimate_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'faq-schema-ultimate',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
