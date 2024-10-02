<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpqode.com/
 * @since      1.0.0
 *
 * @package    Faq_Schema_Ultimate
 * @subpackage Faq_Schema_Ultimate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Faq_Schema_Ultimate
 * @subpackage Faq_Schema_Ultimate/public
 * @author     WPQode <help@wpqode.com>
 */
class Faq_Schema_Ultimate_Shortcode {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * A shortcode for this plugin.
	 *
	 * @since   1.0.0
	 * @param   string $atts attribute of this shortcode.
	 */
	public function gpsc_shortcode_execute( $atts ) {

		
		$post_id = intval( $atts['id'] );
		$options = get_post_meta( $post_id, '_prefix_post_options', true );

		ob_start();

		echo '<style>
		#faqsu-faq-list {
			background: #F0F4F8;
			border-radius: 5px;
			padding: 15px;
		}
		#faqsu-faq-list .faqsu-faq-single {
			background: #fff;
			padding: 15px 15px 20px;
			box-shadow: 0px 0px 10px #d1d8dd, 0px 0px 40px #ffffff;
			border-radius: 5px;
			margin-bottom: 1rem;
		}
		#faqsu-faq-list .faqsu-faq-single:last-child {
			margin-bottom: 0;
		}
		#faqsu-faq-list .faqsu-faq-question {
			border-bottom: 1px solid #F0F4F8;
			padding-bottom: 0.825rem;
			margin-bottom: 0.825rem;
			position: relative;
			padding-right: 40px;
		}
		#faqsu-faq-list .faqsu-faq-question:after {
			content: "?";
			position: absolute;
			right: 0;
			top: 0;
			width: 30px;
			line-height: 30px;
			text-align: center;
			color: #c6d0db;
			background: #F0F4F8;
			border-radius: 40px;
			font-size: 20px;
		}
		</style>
		
		<section id="faqsu-faq-list" itemscope itemtype="http://schema.org/FAQPage">';
		foreach ($options['opt-group-1'] as $key => $value) {

			echo '<div class="faqsu-faq-single" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
					<h3 class="faqsu-faq-question" itemprop="name">' . $value['opt-text'] . '</h3>
					<div itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
						<div class="faqsu-faq-answare" itemprop="text">' . $value['opt-wp-editor-1'] . '</div>
					</div>
				</div>';
			
		}
		echo '</section>';

		return ob_get_clean();
	}


}
