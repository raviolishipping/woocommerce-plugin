<?php

class Ravioli_Admin {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

  public function register_menu_items() {
    add_filter( 'woocommerce_settings_tabs_array', 'Ravioli_Admin::add_settings_tab', 50 );
  }
  
  public static function add_settings_tab( $settings_tabs ) {
    $settings_tabs['ravioli'] = __( 'Ravioli', 'ravioli' );
    return $settings_tabs;
  }
  
  public function settings_tab() {
    woocommerce_admin_fields( $this->ravioli_get_settings() );
  }
  
  public function ravioli_get_settings() {
    $currency = get_woocommerce_currency();
    
    $settings = array(
        'section_title' => array(
            'name'     => __( 'Ravioli Settings', 'ravioli_settings_tab' ),
            'type'     => 'title',
            'desc'     => '',
            'id'       => 'wc_settings_tab_ravioli_section_title'
        ),
        'ravioli_popup' => array(
          'name' => __( 'Show Ravioli pop-up?', 'ravioli_settings_tab' ),
          'type' => 'checkbox',
          'default' => 'yes',
          'desc' => __( 'Show the Ravioli pop-up on your checkout page', 'ravioli_settings_tab' ),
          'id'   => 'ravioli_settings_tab_popup'
        ),
        'ravioli_fee' => array(
            'name' => __( "Ravioli fee ($currency)", 'ravioli_settings_tab' ),
            'type' => 'number',
            'default' => 1,
            'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
            'css' => 'width: 11ch;',
            'desc' => __( 'How much do you want to charge your customers for shipping in a Ravioli box?', 'ravioli_settings_tab' ),
            'id'   => 'ravioli_settings_tab_fee'
        ),
        'ravioli_weight' => array(
          'name' => __( 'Maximum total weight (kg)', 'ravioli_settings_tab' ),
          'type' => 'number',
          'default' => 0,
          'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
          'css' => 'width: 11ch;',
          'desc' => __( "Customer won't see the Ravioli option if the order total weight is above this (enter 0 for no limit, don't forget to set product weights)", 'ravioli_settings_tab' ),
          'id'   => 'ravioli_settings_tab_weight'
        ),
        'ravioli_volume' => array(
          'name' => __( 'Maximum total volume (cm³)', 'ravioli_settings_tab' ),
          'type' => 'number',
          'default' => 0,
          'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
          'css' => 'width: 11ch;',
          'desc' => __( "Customer won't see the Ravioli option if the order total volume is above this (enter 0 for no limit, don't forget to set product dimensions)", 'ravioli_settings_tab' ),
          'id'   => 'ravioli_settings_tab_volume'
        ),
        'section_end' => array(
             'type' => 'sectionend',
             'id' => 'wc_settings_ravioli_section_end'
        )
    );
    return apply_filters( 'wc_settings_tab_ravioli_settings', $settings );
  
  }
  
  public function ravioli_update_settings() {
    woocommerce_update_options( $this->ravioli_get_settings() );
  }

  public function ravioli_new_order_column( $columns ) {

    $new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
  
        if ( 'order_status' === $column_name ) {
            $new_columns['ravioli'] = __( 'Ravioli', 'ravioli' );
        }
    }
    return $new_columns;
  }
  
  public function ravioli_populate_column($column) {
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
  
  public function ravioli_add_order_column_style() {
    $css = '.column-ravioli { width: 6ch !important; }';
    wp_add_inline_style( 'woocommerce_admin_styles', $css );
  }
}
?>