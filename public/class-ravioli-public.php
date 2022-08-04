<?php

class Ravioli_Public {
  private $plugin_name;

  private $version;


  public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

  public function load_ravioli_modal(){
    include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/ravioli_modal.php';
  }

  public function ravioli_modal_script() {
    // return if not checkout page
    if (!is_checkout() || !empty( is_wc_endpoint_url('order-received'))) {
      return;
    }
  
    //console_log(WC()->session->get( 'ravioli_modal_shown'));
  
    // load ravioli styles
    add_action( 'wp_body_open', 'Ravioli_Public::load_ravioli_modal' );
    wp_enqueue_style( 'ravioli_styles', plugins_url( 'css/styles.css', __FILE__ ) );
  
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
      wp_enqueue_script('ravioli_modal', plugins_url( 'js/ravioli_modal.js', __FILE__ ), array(), false, true);
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
  
  public function add_ravioli_fee($cart) {
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
  
  public function ravioli_hidden_field($checkout) { 
    woocommerce_form_field( 'add_ravioli', array(        
       'type' => 'text',        
       'id' => 'ravioli--add_ravioli_field',
       'class' => array('ravioli--hidden' ),        
       'label' => 'add_ravioli',
       'required' => false,        
       'default' => 'false',        
    )); 
  }
  
  public function ravioli_update_session( $posted_data) {
    parse_str($posted_data, $posted_data);
    WC()->session->set( 'add_ravioli' , sanitize_text_field($posted_data["add_ravioli"]) );
  }
  
  
  public function ravioli_add_order_metadata($order_id, $posted_data) {
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
}
?>