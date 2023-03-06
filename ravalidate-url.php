<?php
/*
Plugin Name:  Revalidate NextJS URL
Description:  A small plugin that will revalidate the URL of a post when it is saved. Works with NextJS revalidate.
Version:      0.1.2
Author:       Hugo Winder
Author URI:   https://www.hugowinder.com
Plugin URI:   https://github.com/Hugow1/WP-Revalidate-NextJS-URL
License:      GPL3
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
Text Domain:  wp-revalidate-nextjs-url
Domain Path:  /languages
*/

add_action('save_post', 'revalidate_post', 20, 1);

function revalidate_post($post_id) {
  $wp_revalidate_nextjs_url_options = get_option( 'wp_revalidate_nextjs_url_option_name' );
  $secret_token = $wp_revalidate_nextjs_url_options['secret_token_0'];
  $nextjs_url = $wp_revalidate_nextjs_url_options['nextjs_revalidate_api_path_1'];

  $post = get_post($post_id);
  $url = get_permalink($post_id);
  $url = str_replace(site_url(), '', $url);

  $curl = curl_init($nextjs_url . "?path=" . $url . "&token=" . $secret_token);

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

  $response = curl_exec($curl);
  curl_close($curl);
}

// Settings page in admin
class WPRevalidateNextjsURL {
	private $wp_revalidate_nextjs_url_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wp_revalidate_nextjs_url_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'wp_revalidate_nextjs_url_page_init' ) );
	}

	public function wp_revalidate_nextjs_url_add_plugin_page() {
		add_options_page(
			'WP Revalidate Nextjs URL', // page_title
			'WP Revalidate Nextjs URL', // menu_title
			'manage_options', // capability
			'wp-revalidate-nextjs-url', // menu_slug
			array( $this, 'wp_revalidate_nextjs_url_create_admin_page' ) // function
		);
	}

	public function wp_revalidate_nextjs_url_create_admin_page() {
		$this->wp_revalidate_nextjs_url_options = get_option( 'wp_revalidate_nextjs_url_option_name' ); ?>

		<div class="wrap">
			<h2>WP Revalidate Nextjs URL</h2>
			<p>Add your secret token and the path to the api endpoint here.</p>
			<!-- <?php settings_errors(); ?> -->

			<form method="post" action="options.php">
				<?php
					settings_fields( 'wp_revalidate_nextjs_url_option_group' );
					do_settings_sections( 'wp-revalidate-nextjs-url-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function wp_revalidate_nextjs_url_page_init() {
		register_setting(
			'wp_revalidate_nextjs_url_option_group', // option_group
			'wp_revalidate_nextjs_url_option_name', // option_name
			array( $this, 'wp_revalidate_nextjs_url_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'wp_revalidate_nextjs_url_setting_section', // id
			'Settings', // title
			array( $this, 'wp_revalidate_nextjs_url_section_info' ), // callback
			'wp-revalidate-nextjs-url-admin' // page
		);

		add_settings_field(
			'secret_token_0', // id
			'Secret token', // title
			array( $this, 'secret_token_0_callback' ), // callback
			'wp-revalidate-nextjs-url-admin', // page
			'wp_revalidate_nextjs_url_setting_section' // section
		);

		add_settings_field(
			'nextjs_revalidate_api_path_1', // id
			'Nextjs revalidate api path', // title
			array( $this, 'nextjs_revalidate_api_path_1_callback' ), // callback
			'wp-revalidate-nextjs-url-admin', // page
			'wp_revalidate_nextjs_url_setting_section' // section
		);
	}

	public function wp_revalidate_nextjs_url_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['secret_token_0'] ) ) {
			$sanitary_values['secret_token_0'] = sanitize_text_field( $input['secret_token_0'] );
		}

		if ( isset( $input['nextjs_revalidate_api_path_1'] ) ) {
			$sanitary_values['nextjs_revalidate_api_path_1'] = sanitize_text_field( $input['nextjs_revalidate_api_path_1'] );
		}

		return $sanitary_values;
	}

	public function wp_revalidate_nextjs_url_section_info() {

	}

	public function secret_token_0_callback() {
		printf(
			'<input required class="regular-text" type="text" name="wp_revalidate_nextjs_url_option_name[secret_token_0]" id="secret_token_0" value="%s">',
			isset( $this->wp_revalidate_nextjs_url_options['secret_token_0'] ) ? esc_attr( $this->wp_revalidate_nextjs_url_options['secret_token_0']) : ''
		);
	}

	public function nextjs_revalidate_api_path_1_callback() {
		printf(
			'<input required class="regular-text" type="text" name="wp_revalidate_nextjs_url_option_name[nextjs_revalidate_api_path_1]" id="nextjs_revalidate_api_path_1" value="%s">',
			isset( $this->wp_revalidate_nextjs_url_options['nextjs_revalidate_api_path_1'] ) ? esc_attr( $this->wp_revalidate_nextjs_url_options['nextjs_revalidate_api_path_1']) : ''
		);
	}

}
if ( is_admin() )
	$wp_revalidate_nextjs_url = new WPRevalidateNextjsURL();
