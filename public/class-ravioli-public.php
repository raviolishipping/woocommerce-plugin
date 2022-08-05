<?php

class Ravioli_Public {
  private $plugin_name;

  private $version;


  public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

  public function show_modal() {
    if (!is_checkout() || !empty( is_wc_endpoint_url('order-received'))) {
      return false;
    }

    if (WC()->session->get( 'ravioli_modal_shown')) {
      return false;
    }

    // get settings
    $settings_show_ravioli = get_option( 'ravioli_settings_tab_popup' );
    $settings_max_weight = get_option( 'ravioli_settings_tab_weight' );
    $total_cart_weight = WC()->cart->get_cart_contents_weight();
  
    $weight_ok = empty($settings_max_weight) || $settings_max_weight == 0 || $total_cart_weight <= $settings_max_weight;

    return $settings_show_ravioli == "yes" && $weight_ok;
  }

  public function load_ravioli_modal(){
    if (!$this->show_modal()) {
      return;
    }
    WC()->session->set( 'ravioli_modal_shown' , true );
    include plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/ravioli_modal.php';
    
  }

  public function ravioli_enqueue_styles_and_scripts (){
    if (!is_checkout() || !empty( is_wc_endpoint_url('order-received'))) {
      return;
    }

    wp_enqueue_style( 'ravioli_styles', plugins_url( 'css/styles.css', __FILE__ ) );

    if (!$this->show_modal()) {
      return;
    }

    wp_enqueue_script( 'ravioli_modal', plugins_url( 'js/ravioli_modal.js', __FILE__ ), array(), false, true );

    wp_localize_script(
      'ravioli_modal',
      'ravioli_data',
      array(
        "base_url" => plugins_url( '', __FILE__ ),
        "checkout_url" => wc_get_checkout_url(),
        "fee" => esc_html(trim(get_option( 'ravioli_settings_tab_fee' )))
      )
    );
  }
  
  public function add_ravioli_fee($cart) {
    if (!is_checkout()) {
      return;
    }
  
    if (is_admin() && !defined('DOING_AJAX')) {
      return;
    }
  
    if (WC()->session->get( 'add_ravioli' ) == "true" || WC()->session->get( 'ravioli_added' ) == 'true') {
      $ravioli_fee = get_option( 'ravioli_settings_tab_fee' );
      $cart->add_fee( __('📦 Wiederverwendbare Verpackung (Ravioli)', 'woocommerce'), $ravioli_fee, true );
      WC()->session->set( 'ravioli_added', 'true' );
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
    if (WC()->session->get( 'add_ravioli' ) == 'true') {
      $add_ravioli = "yes";
    }
    $order->update_meta_data( 'ship_with_ravioli', $add_ravioli );
    $order->save();
  }
  
  function remove_ravioli_modal_shown() {
    WC()->session->__unset( 'ravioli_modal_shown');
    WC()->session->__unset( 'add_ravioli');
    WC()->session->__unset( 'ravioli_added');
  }
}
?>