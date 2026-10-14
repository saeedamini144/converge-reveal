<?php
/**
 * Plugin Name:       Converge-Reveal
 * Description:       Custom Elementor widgets for Mada Ceram. Includes the Scroll Reveal Collection widget.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author: Saeed Amini
 * Author URI: https://github.com/saeedamini144
 * Text Domain:       converge-reveal
 * Domain Path:       /languages
 * Elementor tested up to: 3.25
 *
 * @package Converge_Reveal
 */

defined( 'ABSPATH' ) || exit;

define( 'CONVERGE_REVEAL_VERSION', '1.0.0' );
define( 'CONVERGE_REVEAL_FILE', __FILE__ );
define( 'CONVERGE_REVEAL_PATH', plugin_dir_path( __FILE__ ) );
define( 'CONVERGE_REVEAL_URL', plugin_dir_url( __FILE__ ) );
define( 'CONVERGE_REVEAL_MIN_ELEMENTOR', '3.20.0' );
define( 'CONVERGE_REVEAL_MIN_PHP', '7.4' );

/**
 * Main plugin class.
 */
final class Converge_Reveal_Plugin {

	/**
	 * Single instance.
	 *
	 * @var Converge_Reveal_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the single instance.
	 *
	 * @return Converge_Reveal_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ], 20 );
	}

	/**
	 * Run compatibility checks, then hook everything up.
	 *
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain( 'converge-reveal', false, dirname( plugin_basename( CONVERGE_REVEAL_FILE ) ) . '/languages' );

		if ( version_compare( PHP_VERSION, CONVERGE_REVEAL_MIN_PHP, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_min_php' ] );
			return;
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_elementor' ] );
			return;
		}

		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, CONVERGE_REVEAL_MIN_ELEMENTOR, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_min_elementor' ] );
			return;
		}

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
	}

	/**
	 * Register the Elementor category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'converge-reveal',
			[
				'title' => esc_html__( 'Converge-Reveal', 'converge-reveal' ),
				'icon'  => 'eicon-font',
			]
		);
	}

	/**
	 * Register widgets. Add every future widget here.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		require_once CONVERGE_REVEAL_PATH . 'widgets/class-mdc-reveal.php';

		$widgets_manager->register( new \Converge_Reveal\Widgets\Reveal() );
	}

	/**
	 * Register (not enqueue) stylesheets. Widgets load them via get_style_depends().
	 *
	 * @return void
	 */
	public function register_styles() {
		wp_register_style(
			'mdc-reveal',
			CONVERGE_REVEAL_URL . 'assets/css/reveal.css',
			[],
			CONVERGE_REVEAL_VERSION
		);
	}

	/**
	 * Register (not enqueue) scripts. Widgets load them via get_script_depends().
	 *
	 * @return void
	 */
	public function register_scripts() {
		wp_register_script(
			'mdc-reveal',
			CONVERGE_REVEAL_URL . 'assets/js/reveal.js',
			[],
			CONVERGE_REVEAL_VERSION,
			[
				'in_footer' => true,
				'strategy'  => 'defer',
			]
		);
	}

	/**
	 * Admin notice: PHP too old.
	 *
	 * @return void
	 */
	public function notice_min_php() {
		$this->print_notice(
			sprintf(
				/* translators: 1: plugin name, 2: required PHP version */
				esc_html__( '%1$s requires PHP %2$s or newer.', 'converge-reveal' ),
				'<strong>Converge-Reveal</strong>',
				esc_html( CONVERGE_REVEAL_MIN_PHP )
			)
		);
	}

	/**
	 * Admin notice: Elementor missing.
	 *
	 * @return void
	 */
	public function notice_missing_elementor() {
		$this->print_notice(
			sprintf(
				/* translators: 1: plugin name, 2: Elementor */
				esc_html__( '%1$s requires %2$s to be installed and activated.', 'converge-reveal' ),
				'<strong>Converge-Reveal</strong>',
				'<strong>Elementor</strong>'
			)
		);
	}

	/**
	 * Admin notice: Elementor too old.
	 *
	 * @return void
	 */
	public function notice_min_elementor() {
		$this->print_notice(
			sprintf(
				/* translators: 1: plugin name, 2: Elementor, 3: required version */
				esc_html__( '%1$s requires %2$s version %3$s or newer.', 'converge-reveal' ),
				'<strong>Converge-Reveal</strong>',
				'<strong>Elementor</strong>',
				esc_html( CONVERGE_REVEAL_MIN_ELEMENTOR )
			)
		);
	}

	/**
	 * Print an admin warning notice. $message is already escaped by the callers.
	 *
	 * @param string $message Escaped HTML message.
	 * @return void
	 */
	private function print_notice( $message ) {
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}

Converge_Reveal_Plugin::instance();
