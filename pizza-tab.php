<?php
/**
 *
 * @link                https://neoslab.online
 * @since               1.0.0
 * @package             pizza-tab
 *
 * @wordpress-plugin
 * Plugin Name:         Cook Pizza 
 * Plugin URI:          https://neoslab.online
 * Description:         Pizza builder for Woocommerce
 * Version:             1.0.0
 * Requires at least:   6.0
 * Requires PHP:        7.4
 * Author:              Neos Lab
 * Author URI:          https://neoslab.online
 * License:             GPL2
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         bcpg-textdomain
 * Domain Path:         /languages
 */

defined('ABSPATH') || exit;

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
    exit;


if (!defined('U_PIZZA_PATH')) 
    define('U_PIZZA_PATH', plugin_dir_path(__FILE__));

if (!defined('U_PIZZA_DIR')) 
    define('U_PIZZA_DIR', __FILE__);

class U_Pizza_Install {


    protected static $instance = null;

    public static function instance() 
    {

        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    public function __construct() 
    {
        $this->include();
    }

    public function include() 
    {

        //require_once U_PIZZA_PATH . 'includes/u-pizza-product.php';
        require_once U_PIZZA_PATH . 'includes/traits/instantiable.php';
        require_once U_PIZZA_PATH . 'includes/pizza-functions.php';
        require_once U_PIZZA_PATH . 'includes/pizza-product.php';
        require_once U_PIZZA_PATH . 'includes/pizza.php';
        require_once U_PIZZA_PATH . 'includes/pizza-display.php';
        require_once U_PIZZA_PATH . 'includes/pizza-cart.php';

        U_Pizza::instance();
        U_Pizza_Display::instance();
        U_Pizza_Cart::instance();


    }

}

function init_plugin_pizza() {
    U_Pizza_Install::instance();
}

add_action('plugins_loaded', 'init_plugin_pizza', 5);








