<?php 
/** 
 * Ravioli
 * 
 * @package Ravioli
 * @author Ravioli
 * @copyright 2022 Ravioli Logistik UG (haftungsbeschränkt) 
 * @license AGPLv3
 * 
 * @wordpress-plugin 
 * Plugin Name: Ravioli
 * Plugin URI: https://getravioli.de
 * Description: Lets your customers choose if they want to get their order shipped in a reusable Ravioli box. 
 * Version: 1.0.0
 * Author: Ravioli
 * Author URI: https://getravioli.de
 * Text Domain: ravioli 
 * License: GPL AGPLv3
 * License URI: https://www.gnu.org/licenses/agpl-3.0.en.html */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'RAVIOLI_VERSION', '1.0.0' );


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