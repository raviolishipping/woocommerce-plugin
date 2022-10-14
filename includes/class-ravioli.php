<?php

class Ravioli {
  protected $loader;

  protected $plugin_name;

  protected $version;

	const EXCLUDE_RAVIOLI_KEY = 'exclude_from_ravioli';
	const RAVIOLI_TEXT_DOMAIN = 'ravioli';

  public function __construct() {
		if ( defined( 'RAVIOLI_VERSION' ) ) {
			$this->version = RAVIOLI_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ravioli';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

  private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ravioli-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ravioli-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ravioli-public.php';

		$this->loader = new Ravioli_Loader();

	}

  private function define_admin_hooks() {

		$plugin_admin = new Ravioli_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'woocommerce_product_options_advanced', $plugin_admin, 'add_exclude_from_ravioli' );
		$this->loader->add_action( 'woocommerce_admin_process_product_object', $plugin_admin, 'action_woocommerce_admin_process_product_object', 10, 1 ); 
    $this->loader->add_action( 'woocommerce_update_options_ravioli', $plugin_admin, 'ravioli_update_settings' );
    $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_menu_items' );
    $this->loader->add_action( 'woocommerce_settings_tabs_ravioli', $plugin_admin, 'settings_tab' );
    $this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'ravioli_new_order_column' );
    $this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'ravioli_populate_column' );
    $this->loader->add_action( 'admin_print_styles', $plugin_admin, 'ravioli_add_order_column_style' );

	}

	private function define_public_hooks() {

		$plugin_public = new Ravioli_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'ravioli_enqueue_styles_and_scripts' );
		$this->loader->add_action( 'wp_body_open', $plugin_public, 'load_ravioli_modal' );
		// alternative to the wp_body_open hook for older themes that don't support it
		$this->loader->add_action( 'wp_footer', $plugin_public, 'load_ravioli_modal' );
    $this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'remove_ravioli_modal_shown', 10, 2 );
    $this->loader->add_action( 'woocommerce_cart_calculate_fees', $plugin_public, 'add_ravioli_fee', 10 , 1 );
		$this->loader->add_filter( 'esc_html', $plugin_public, 'show_remove_button_for_fee', 10, 2 );
    $this->loader->add_action( 'woocommerce_before_order_notes', $plugin_public, 'ravioli_hidden_field' );
    $this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_public, 'ravioli_update_session' );
    $this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'ravioli_add_order_metadata', 20, 2 );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}

?>