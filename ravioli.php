<?php 
/** 
 * Ravioli for WooCommerce
 * 
 * @package Ravioli for WooCommerce
 * @author Ravioli for WooCommerce
 * @copyright 2022 Ravioli Logistik UG (haftungsbeschränkt) 
 * @license GPL3
 * 
 * @wordpress-plugin 
 * Plugin Name: Ravioli for WooCommerce
 * Description: Let your customers choose if they want to get their order shipped in a reusable Ravioli box with this official Ravioli plugin. Works only with WooCommerce.
 * Version: 1.0.4
 * Author: Ravioli
 * Author URI: https://getravioli.de
 * Text Domain: ravioli 
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'RAVIOLI_VERSION', '1.0.4' );


require plugin_dir_path( __FILE__ ) . 'includes/class-ravioli.php';

function run_ravioli() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
  }

	$plugin = new Ravioli();
	$plugin->run();

}

add_action( 'plugins_loaded', 'run_ravioli', 10 );
?>