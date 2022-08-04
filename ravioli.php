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
 * Version: 0.0.1 
 * Author: Ravioli
 * Author URI: https://getravioli.de
 * Text Domain: ravioli 
 * License: GPL AGPLv3
 * License URI: https://www.gnu.org/licenses/agpl-3.0.en.html */


// use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
// use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
// use Automattic\WooCommerce\Admin\Features\Features;


function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

// if (
//   ! method_exists( Screen::class, 'register_post_type' ) ||
//   ! method_exists( Menu::class, 'add_plugin_item' ) ||
//   ! method_exists( Menu::class, 'add_plugin_category' ) ||
//   ! Features::is_enabled( 'navigation' )
// ) {
//   return;
// }

function register_menu_items() {
  add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . 'add_settings_tab', 50 );
}

function add_settings_tab( $settings_tabs ) {
  $settings_tabs['ravioli'] = __( 'Ravioli', 'ravioli' );
  return $settings_tabs;
}

function settings_tab() {
  woocommerce_admin_fields( ravioli_get_settings() );
}

function ravioli_get_settings() {
  $settings = array(
      'section_title' => array(
          'name'     => __( 'Ravioli Settings', 'ravioli_settings_tab' ),
          'type'     => 'title',
          'desc'     => '',
          'id'       => 'wc_settings_tab_ravioli_section_title'
      ),
      'ravioli_popup' => array(
        'name' => __( 'Show Ravioli Popup?', 'ravioli_settings_tab' ),
        'type' => 'checkbox',
        'default' => 'yes',
        'desc' => __( 'Show the Ravioli popup on your checkout page', 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_popup'
    ),
      'ravioli_fee' => array(
          'name' => __( 'Ravioli fee (€)', 'ravioli_settings_tab' ),
          'type' => 'number',
          'default' => '1',
          'css' => 'width: 8ch;',
          'desc' => __( 'How much do you want to charge your customers for shipping in a Ravioli box?', 'ravioli_settings_tab' ),
          'id'   => 'ravioli_settings_tab_fee'
      ),
      'ravioli_weight' => array(
        'name' => __( 'Maximum weight (kg)', 'ravioli_settings_tab' ),
        'type' => 'number',
        'default' => '0',
        'css' => 'width: 8ch;',
        'desc' => __( "Customer won't see the Ravioli option if the order total weight is above this (enter 0 for no limit)", 'ravioli_settings_tab' ),
        'id'   => 'ravioli_settings_tab_weight'
    ),
      'section_end' => array(
           'type' => 'sectionend',
           'id' => 'wc_settings_ravioli_section_end'
      )
  );
  return apply_filters( 'wc_settings_tab_ravioli_settings', $settings );
}

add_action( 'woocommerce_update_options_ravioli', 'ravioli_update_settings' );

function ravioli_update_settings() {
    woocommerce_update_options( ravioli_get_settings() );
}

function ravioli_modal_script() {
  // return if not checkout page
  if (!is_checkout() || !empty( is_wc_endpoint_url('order-received'))) {
    return;
  }

  console_log(WC()->session->get( 'ravioli_modal_shown'));

  // load ravioli styles
  wp_enqueue_style('ravioli_styles', plugins_url( 'styles.css', __FILE__ ));

  if (WC()->session->get( 'ravioli_modal_shown')) {
    return;
  }

  // get settings
  $settings_show_ravioli = get_option( 'ravioli_settings_tab_popup' );
  $settings_max_weight = get_option( 'ravioli_settings_tab_weight' );
  $total_cart_weight = WC()->cart->get_cart_contents_weight();

  $weight_ok = empty($settings_max_weight) || $settings_max_weight == 0 || $total_cart_weight <= $settings_max_weight;


  // only show Ravioli modal if it's turned on in settings and total cart weight is less than max weight in settings
  if ($settings_show_ravioli == "yes" && $weight_ok) {
    $show_modal = true;
    WC()->session->set( 'ravioli_modal_shown' , true );
    wp_enqueue_script('ravioli_modal', plugins_url( 'ravioli_modal.js', __FILE__ ), array(), false, true);
    wp_localize_script(
      'ravioli_modal',
      'ravioli_data',
      array(
        "base_url" => plugins_url( '', __FILE__ ),
        "checkout_url" => wc_get_checkout_url(),
        "fee" => esc_html(trim(get_option( 'ravioli_settings_tab_fee' ))),
        "show_modal" => $show_modal,
      )
    );
  }
}

function add_ravioli_fee($cart) {
  if (!is_checkout()) {
    return;
  }

  if (is_admin() && !defined('DOING_AJAX')) {
		return;
	}

  if (WC()->session->get( 'add_ravioli' ) == "true") {
    $ravioli_fee = get_option( 'ravioli_settings_tab_fee' );
    $cart->add_fee( __('📦 Wiederverwendbare Verpackung (Ravioli)', 'woocommerce'), $ravioli_fee, true );
  }
}

function ravioli_hidden_field($checkout) { 
  woocommerce_form_field( 'add_ravioli', array(        
     'type' => 'text',        
     'id' => 'ravioli--add_ravioli_field',
     'class' => array('ravioli--hidden' ),        
     'label' => 'add_ravioli',
     'required' => false,        
     'default' => 'false',        
  )); 
}

function ravioli_update_session( $posted_data) {
  parse_str($posted_data, $posted_data);
  WC()->session->set( 'add_ravioli' , sanitize_text_field($posted_data["add_ravioli"]) );
}


function ravioli_add_order_metadata($order_id, $posted_data) {
  $order = wc_get_order( $order_id );
  $add_ravioli = "no";
  if (WC()->session->get( 'add_ravioli' ) == "true") {
    $add_ravioli = "yes";
  }
  $order->update_meta_data( 'ship_with_ravioli', $add_ravioli );
  $order->save();
}

function remove_ravioli_modal_shown() {
  WC()->session->__unset( 'ravioli_modal_shown');
}

add_action( 'woocommerce_thankyou', 'remove_ravioli_modal_shown', 10, 2 ); 

//add_filter( 'query_vars', 'ravioli_query_vars' );

add_action( "admin_menu", "register_menu_items" );
add_action( 'woocommerce_settings_tabs_ravioli', 'settings_tab' );

add_action( 'wp_enqueue_scripts', 'ravioli_modal_script' );

add_action( 'woocommerce_cart_calculate_fees','add_ravioli_fee', 10 , 1 );

add_action( 'woocommerce_before_order_notes', 'ravioli_hidden_field' );

add_action('woocommerce_checkout_update_order_review', 'ravioli_update_session');

add_action('woocommerce_checkout_update_order_meta', 'ravioli_add_order_metadata', 20, 2);


function ravioli_new_order_column( $columns ) {

  $new_columns = array();
  foreach ( $columns as $column_name => $column_info ) {
      $new_columns[ $column_name ] = $column_info;

      if ( 'order_status' === $column_name ) {
          $new_columns['ravioli'] = __( 'Ravioli', 'ravioli' );
      }
  }
  return $new_columns;
}

function ravioli_populate_column($column) {
  global $post;

  if ( 'ravioli' === $column ) {
    $order = wc_get_order( $post->ID );
    $ship_with_ravioli = $order->get_meta("ship_with_ravioli");
    if ($ship_with_ravioli == "yes") {
      echo "<mark class='order-status status-processing'>";
      echo "<span>Ja</span>";
      echo "</mark>";
    } else {
      echo "<mark class='order-status status-pending'>";
      echo "<span>Nein</span>";
      echo "</mark>";
    }
  }
}

function ravioli_add_order_column_style() {
  $css = '.column-ravioli { width: 6ch !important; }';
  wp_add_inline_style( 'woocommerce_admin_styles', $css );
}

add_filter( 'manage_edit-shop_order_columns', 'ravioli_new_order_column' );

add_action( 'manage_shop_order_posts_custom_column', 'ravioli_populate_column' );

add_action( 'admin_print_styles', 'ravioli_add_order_column_style' );

?>