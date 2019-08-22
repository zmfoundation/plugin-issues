<?php
/**
 * Plugin Name: Element King
 * Description: <a href="https://www.ekaboom.com">Element King</a> - A Companion Plugin for elementor with a package of custom widgets.
 * Author: Ekaboom
 * Author URI: https://ekaboom.com
 * Version: 1.0.0
 * Text Domain: element-king
 * License: GPLv2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:  /languages
 */

/*
 * Security Check
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/*
 * Class Creation
 * */

final class Elementor_King
{

    /**
     * Plugin Version
     */

    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     */

    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

    /**
     * Minimum PHP Version
     */

    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Instance Initialization
     */

    private static $_instance = null;

    /**
     * Instance Checking
     */

    public static function instance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * Constructor Method
     */
    public function __construct() {

        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init']);

    }

    /**
     * Load Textdomain
     */
    public function i18n() {

        load_plugin_textdomain('element-king');

    }

    /**
     * Initialize the plugin
     */

    public function init() {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }

        // Add Plugin actions
        add_action('elementor/widgets/widgets_registered', [$this, 'init_widgets']);

        // External CSS file including
        add_action('elementor/editor/after_enqueue_styles', [$this, 'ek_external_icon']);

        // Front end files (CSS)
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'ek_external_css']);

        // front end files (JS)
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'ek_external_js']);

        // New Category Creation
        add_action('elementor/elements/categories_registered', [$this, 'ek_new_category_register']);
    }

    // External Icon Pack
    public function ek_external_icon() {
        wp_enqueue_style('element-king', plugin_dir_url(__FILE__) . '/assets/admin/css/element-king-icon.css');
    }

    // Front end Files
    public function ek_external_css() {
        wp_enqueue_style('hover-css', plugin_dir_url(__FILE__) . '/assets/front/css/hover.css');
        wp_enqueue_style('element-king-css', plugin_dir_url(__FILE__) . '/assets/front/css/element-king.css');
        wp_enqueue_style('morphtext', plugin_dir_url(__FILE__) . '/assets/front/css/morphext.css');
    }

    public function ek_external_js() {
        wp_enqueue_script('morphtext', plugin_dir_url(__FILE__) . '/assets/front/js/morphext.min.js', array('jquery'), '2.4.4', true);
        wp_enqueue_script('element-king', plugin_dir_url(__FILE__) . '/assets/front/js/element-king.js', array('jquery'), time(), true);
        wp_enqueue_script('typed-js', plugin_dir_url(__FILE__) . '/assets/front/js/typed.min.js', array('jquery'), time(), true);
    }

    // New Category
    public function ek_new_category_register($cat_manager) {
        $cat_manager->add_category(
            'element-king',
            [
                'title' => __('Element King', 'element-king'),
                'icon'  => 'fa fa-etsy',
            ]
        );
    }

    /**
     * Admin notice
     */

    public function admin_notice_missing_main_plugin() {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'element-king'),
            '<strong>' . esc_html__('Elementor Test Extension', 'element-king') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'element-king') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin notice
     */
    public function admin_notice_minimum_elementor_version() {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'element-king'),
            '<strong>' . esc_html__('Elementor Test Extension', 'element-king') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'element-king') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);

    }

    /**
     * Admin notice
     */
    public function admin_notice_minimum_php_version() {

        if (isset($_GET['activate'])) unset($_GET['activate']);

        $message = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'element-king'),
            '<strong>' . esc_html__('Elementor Test Extension', 'element-king') . '</strong>',
            '<strong>' . esc_html__('PHP', 'element-king') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);

    }

    /**
     * Init Widgets
     */
    public function init_widgets() {

        // Include Advanced Button Widget files
        require_once(__DIR__ . '/widgets/advanced-button/advanced-button.php');

        // Include Animated Heading Widget files
        require_once(__DIR__ . '/widgets/animated-heading/animated-heading.php');

        // Register Advanced Button widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Advanced_button());

        // Register Animated Heading widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \AnimatedHeading());

    }

}

Elementor_King::instance();

