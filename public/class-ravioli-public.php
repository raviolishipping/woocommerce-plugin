<?php

class Ravioli_Public {
  private $plugin_name;

  private $version;

  public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

  // checks if all products in the cart contain 'exclude_from_ravioli' == 'yes'
  private function all_products_excluded( $cart ) {
  
    if (!$cart) return false;

    foreach ($cart as $k => $cart_item) {
      $product = wc_get_product($cart_item['product_id']);
      $all_meta_data = $product->get_meta_data();

      // return false if no meta data
      if (!$all_meta_data) return false;

      $excluded_found = false;
      foreach ($all_meta_data as $md) {
        if ($md->key == Ravioli::EXCLUDE_RAVIOLI_KEY && $md->value == 'yes') {
          // if any product is excluded from ravioli, set this to true
          $excluded_found = true;
        }
      }
      // if this product didn't have excluded_from_ravioli == yes, return false
      if (!$excluded_found) return false;
    }

    return true;
  }

  // decide to show modal or not
  public function show_modal() {
    if ( !is_checkout() || !empty( is_wc_endpoint_url( 'order-received' )) ) {
      return false;
    }

    if (WC()->session->get( 'ravioli_modal_shown' )) {
      return false;
    }

    $cart = WC()->cart->get_cart();    

    // if all products have "exclude from Ravioli?" checked, don't show modal
    if ($this->all_products_excluded($cart)) return false;

    // get settings and other values
    $settings_show_ravioli = get_option( 'ravioli_settings_tab_popup' );
    $settings_max_weight = get_option( 'ravioli_settings_tab_weight' );
    $settings_max_volume = get_option( 'ravioli_settings_tab_volume' );
    $total_cart_weight = WC()->cart->get_cart_contents_weight();
    $total_cart_volume = $this->calculate_cart_volume($cart);
  
    $weight_ok = empty($settings_max_weight) || $settings_max_weight == 0 || $total_cart_weight <= $settings_max_weight;
    $volume_ok = empty($settings_max_volume) || $settings_max_volume == 0 || $total_cart_volume == 0 || $total_cart_volume <= $settings_max_volume;

    return $settings_show_ravioli == "yes" && $weight_ok && $volume_ok;
  }

  public function load_ravioli_modal(){
    // backwards compatibility with themes that don't support the wp_body_open hook
    if ( doing_action( 'wp_body_open' ) ) {
      remove_action ( 'wp_footer', 'wpdocs_my_function' );
      return;
      
    }

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
        "checkout_url" => wc_get_checkout_url()
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
      $cart->add_fee( __('Mehrwegversandbox (Ravioli)', 'woocommerce'), $ravioli_fee, true );
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

  private function calculate_cart_volume($cart) {
    // calculate total cart volume
    $total_cart_volume = 0;
    foreach ($cart as $cart_item) {
      // initialize values
      $product = wc_get_product( $cart_item["product_id"] );
      $height = 0;
      $width = 0;
      $length = 0;

      if ($cart_item["variation_id"] != 0) {
        // if the product has variations, we first need to get the variation, and then get the dimensions
        // variations take precedence (i.e. if there's a variation, its dimensions will be taken,
        // not the general dimensions)
        $variations = $product->get_available_variations();
        foreach ($variations as $variation) {
          if ( $cart_item["variation_id"] == $variation["variation_id"] ) {
            // if variation id found, assign to dimension values and break out of loop
            $height = $variation["dimensions"]["height"];
            $width = $variation["dimensions"]["width"];
            $length = $variation["dimensions"]["length"];
            break;
          }
        }
      } else {
        // in this case, the product doesn't have variations and we can simply get the dimensions
        $height = $product->get_height();
        $width = $product->get_width();
        $length = $product->get_length();
      }

      if ($height == 0 || empty($height) || $width == 0 || empty($width) || $length == 0 || empty($length)) {
        // if a product's dimensions are not set, reset volume and break out, not counting the total volume limit
        $total_cart_volume = 0;
        break;
      }

      // calculate this products volume times quantity
      $total_cart_volume += $height * $width * $length * $cart_item["quantity"];
    }

    return $total_cart_volume;
  }
}
?>