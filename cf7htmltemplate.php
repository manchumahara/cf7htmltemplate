<?php
	/**
	 * The plugin bootstrap file
	 *
	 * This file is read by WordPress to generate the plugin information in the plugin
	 * admin area. This file also includes all of the dependencies used by the plugin,
	 * registers the activation and deactivation functions, and defines a function
	 * that starts the plugin.
	 *
	 * @link              codeboxr.com
	 * @since             1.0.0
	 * @package           cf7htmltemplate
	 *
	 * @wordpress-plugin
	 * Plugin Name:       Html Template for Contact Form 7
	 * Plugin URI:        https://codeboxr.com/product/contact-form-7-html-templates/
	 * Description:       Html template(s) for contact form 7
	 * Version:           1.0.3
	 * Author:            Codeboxr Team
	 * Author URI:        https://codeboxr.com
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       cf7htmltemplate
	 * Domain Path:       /languages
	 */


	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	//Html Template for Contact Form 7

	defined( 'CF7HTMLTEMPLATE_PLUGIN_NAME' ) or define( 'CF7HTMLTEMPLATE_PLUGIN_NAME', 'cf7htmltemplate' );
	defined( 'CF7HTMLTEMPLATE_PLUGIN_VERSION' ) or define( 'CF7HTMLTEMPLATE_PLUGIN_VERSION', '1.0.3' );
	defined( 'CF7HTMLTEMPLATE_BASE_NAME' ) or define( 'CF7HTMLTEMPLATE_BASE_NAME', plugin_basename( __FILE__ ) );
	defined( 'CF7HTMLTEMPLATE_ROOT_PATH' ) or define( 'CF7HTMLTEMPLATE_ROOT_PATH', plugin_dir_path( __FILE__ ) );
	defined( 'CF7HTMLTEMPLATE_ROOT_URL' ) or define( 'CF7HTMLTEMPLATE_ROOT_URL', plugin_dir_url( __FILE__ ) );

	//register_activation_hook( __FILE__, 'cf7htmltemplate_activation' );

	function cf7htmltemplate_activation() {

		//Check if contact form 7 active
		if ( ! in_array( 'contact-form-7/wp-contact-form-7.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

			// Deactivate the plugin
			deactivate_plugins( __FILE__ );

			// Throw an error in the wordpress admin console
			$error_message = __( 'This plugin requires <a target="_blank" href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7</a> plugin to be active!', 'cf7htmltemplate' );
			die( $error_message );
		}
	}//end function  cf7htmltemplate_activation

	class CF7HtmlTemplate {
		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;

		public function __construct() {
			$this->plugin_name = CF7HTMLTEMPLATE_PLUGIN_NAME;
			$this->version     = CF7HTMLTEMPLATE_PLUGIN_VERSION;

			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

			//add_filter( 'wpcf7_contact_form_properties', array( $this, 'contact_form_properties' ), 10, 2 );
			add_action( 'wpcf7_editor_panels', array( $this, 'editor_panels' ), 10, 1 );
			//add_action( 'wpcf7_save_contact_form', array( $this, 'save_contact_form' ), 10, 3 );
			add_action( 'wpcf7_after_save', array( $this, 'save_contact_form' ), 10, 1 );

			add_filter( 'wpcf7_mail_components', array( $this, 'mail_components_body_do_shortcode' ), 50, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

		}//end of constructor

		/**
		 *
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'cf7htmltemplate', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		}//end function load_plugin_textdomain

		public function plugin_action_links( $links ) {
			$links[] = '<a href="https://codeboxr.com/product/contact-form-7-html-templates/" target="_blank">' . esc_html__( 'Documentation & Support', 'cf7htmltemplate' ) . '</a>';

			return $links;
		}//end method plugin_action_links

		/**
		 * Add scrips and styles
		 */
		public function enqueue_scripts_styles() {
			global $post_type, $post;

			$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';

			if ( $page == 'wpcf7' ) {

				wp_register_style( 'spectrum', plugin_dir_url( __FILE__ ) . 'assets/js/spectrum/spectrum.css', array(), $this->version );
				wp_register_style( 'cf7htmltemplate', plugin_dir_url( __FILE__ ) . 'assets/css/cf7htmltemplate.css', array( 'spectrum' ), $this->version );

				wp_enqueue_style( 'spectrum' );
				wp_enqueue_style( 'cf7htmltemplate' );


				wp_register_script( 'spectrum', plugin_dir_url( __FILE__ ) . 'assets/js/spectrum/spectrum.js', array( 'jquery' ), $this->version, true );
				wp_register_script( 'cf7htmltemplate', plugin_dir_url( __FILE__ ) . 'assets/js/cf7htmltemplate.js', array( 'jquery', 'spectrum' ), $this->version, true );

				// Localize the script with new data
				$spectrum_admin_js_vars = apply_filters( 'spectrum_admin_js_vars',
					array(
						'spectrum'       => array(
							'cancelText'            => esc_html__( 'cancel', 'cf7htmltemplate' ),
							'chooseText'            => esc_html__( 'choose', 'cf7htmltemplate' ),
							'clearText'             => esc_html__( 'Clear Color Selection', 'cf7htmltemplate' ),
							'noColorSelectedText'   => esc_html__( 'No Color Selected', 'cf7htmltemplate' ),
							'togglePaletteMoreText' => esc_html__( 'more', 'cf7htmltemplate' ),
							'togglePaletteLessText' => esc_html__( 'less', 'cf7htmltemplate' ),
						),

						'please_select' => esc_html__( 'Please Select', 'cf7htmltemplate' ),
						'upload_title'  => esc_html__( 'Choose Photo/Image', 'cf7htmltemplate' ),
					) );
				wp_localize_script( 'cf7htmltemplate', 'cf7htmltemplate', $spectrum_admin_js_vars );

				wp_enqueue_media();
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'spectrum' );
				wp_enqueue_script( 'cf7htmltemplate' );
			}
		}//end method enqueue_scripts_styles

		/**
		 * Add extra default setting for new tab properties
		 *
		 * @param $properties
		 * @param $ref
		 *
		 * @return array
		 */
		public function contact_form_properties( $properties, $ref ) {
			if ( ! isset( $properties['cf7htmltemplate_settings'] ) ) {
				$properties['cf7htmltemplate_settings'] = array();
			}

			return $properties;
		}//end method contact_form_properties

		/**
		 * Add new tab panel for Trello setting
		 */
		public function editor_panels( $panels ) {
			$panels['cf7htmltemplate-settings-panel'] = array(
				'title'    => esc_html__( 'Html Template', 'cf7htmltemplate' ),
				'callback' => array( $this, 'editor_panel_cf7htmltemplate_settings' )
			);

			return $panels;
		}//end method editor_panels


		/**
		 * Show extra trello fields
		 *
		 * @param $post
		 */
		public function editor_panel_cf7htmltemplate_settings( $post ) {


            $form_id = $post->id();

			$cf7htmltemplate = get_post_meta($form_id, '_cf7htmltemplate_settings', true);

			$enable 	   = isset( $cf7htmltemplate['enable'] ) ? intval( $cf7htmltemplate['enable'] ) : 0;
			$use_header    = isset( $cf7htmltemplate['use_header'] ) ? intval( $cf7htmltemplate['use_header'] ) : 1;
			$header_text   = isset( $cf7htmltemplate['header_text'] ) ? sanitize_text_field( $cf7htmltemplate['header_text'] ) : esc_html__('Contact Form Notification', 'cf7htmltemplate');
			$header_image  = isset( $cf7htmltemplate['header_image'] ) ? sanitize_text_field( $cf7htmltemplate['header_image'] ) : '';
			$footer_text   = isset( $cf7htmltemplate['footer_text'] ) ? sanitize_textarea_field( $cf7htmltemplate['footer_text'] ) : '{sitename}';

			$base_color    = isset( $cf7htmltemplate['base_color'] ) ? sanitize_hex_color( $cf7htmltemplate['base_color'] ) : '#557da1';
			$bg_color      = isset( $cf7htmltemplate['bg_color'] ) ? sanitize_hex_color( $cf7htmltemplate['bg_color'] ) : '#f5f5f5';
			$body_bg_color = isset( $cf7htmltemplate['body_bg_color'] ) ? sanitize_hex_color( $cf7htmltemplate['body_bg_color'] ) : '#fdfdfd';
			$text_color    = isset( $cf7htmltemplate['text_color'] ) ? sanitize_hex_color( $cf7htmltemplate['text_color'] ) : '#505050';


			?>
			<h2><?php echo esc_html__( 'Html Template Setting', 'cf7htmltemplate' ); ?></h2>
			<p>
				<label for="cf7htmltemplate_enable"><input type="checkbox" id="cf7htmltemplate_enable" name="cf7htmltemplate[enable]" value="1" <?php echo intval($enable) ? ' checked="checked"' : ''; ?> /> <?php echo __( 'Enable Html Template(Also depends on <strong>Use HTML content type</strong> on Email Tab)', 'cf7htmltemplate' ); ?>
				</label>
			</p>
			<p>
				<label for="cf7htmltemplate_use_header"><input type="checkbox" id="cf7htmltemplate_use_header" name="cf7htmltemplate[use_header]" value="1" <?php echo intval($use_header) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html__( 'Use a Heading in Email', 'cf7htmltemplate' ); ?>
				</label>
			</p>
			<p>
				<label for="cf7htmltemplate_header_text"><?php esc_html_e( 'Header Text(Support any form field syntax)', 'cf7htmltemplate' ); ?></label><br/>
				<input type="text" id="cf7htmltemplate_header_text" name="cf7htmltemplate[header_text]" size="70" class="large-text code" value="<?php echo esc_attr( $header_text ); ?>" />
			</p>
			<p>
				<label for="cf7htmltemplate_header_image"><?php esc_html_e( 'Site Logo', 'cf7htmltemplate' ); ?>
					<input type="text" class="regular-text cf7htmltemplate-photo-url" id="cf7htmltemplate_header_image" name="cf7htmltemplate[header_image]" value="<?php echo esc_url($header_image); ?>" /> <input type="button" class="button cf7htmltemplate-photo" value="<?php esc_html_e( 'Choose File', 'cf7htmltemplate' ); ?>" /> </label>
			</p>
			<div id="cf7htmltemplate_footer_text_wrapper">
				<label for="cf7htmltemplate_footer_text"><?php esc_html_e( 'Footer Text(Support any form field syntax)', 'cf7htmltemplate' ); ?></label>
				<?php wp_editor( stripcslashes( $footer_text ), 'cf7htmltemplate_footer_text', $settings = array(
					'teeny'         => true,
					'media_buttons' => false,
					'textarea_name' => 'cf7htmltemplate[footer_text]',
					'editor_class'  => 'cf7htmltemplate_footer_text',
					'textarea_rows' => 8,
					'quicktags'     => true,
					'tinymce'       => array(
						'init_instance_callback' => 'function(editor) {
													editor.on("change", function(){
														tinymce.triggerSave();
														//jQuery("#" + editor.id).valid();
													});
												}'
					)
				) );

					\_WP_Editors::editor_js();
				?>
			</div>
			<p>
				<input type="text" id="cf7htmltemplate_base_color" name="cf7htmltemplate[base_color]" size="70" class="large-text code spectrum_color" value="<?php echo esc_attr( $base_color ); ?>" /> <label for="cf7htmltemplate_base_color"><?php esc_html_e( 'Base Color(The base color of the email)', 'cf7htmltemplate' ); ?></label>
			</p>
			<p>
				<input type="text" id="cf7htmltemplate_bg_color" name="cf7htmltemplate[bg_color]" size="70" class="large-text code spectrum_color" value="<?php echo esc_attr( $bg_color ); ?>" /> <label for="cf7htmltemplate_bg_color"><?php esc_html_e( 'Background Colour(The background color of the email)', 'cf7htmltemplate' ); ?></label>
			</p>
			<p>
				<input type="text" id="cf7htmltemplate_body_bg_color" name="cf7htmltemplate[body_bg_color]" size="70" class="large-text code spectrum_color" value="<?php echo esc_attr( $body_bg_color ); ?>" /> <label for="cf7htmltemplate_body_bg_color"><?php esc_html_e( 'Body Background Color(The background colour of the main body of email)', 'cf7htmltemplate' ); ?></label>
			</p>
			<p>
				<input type="text" id="cf7htmltemplate_body_text_color" name="cf7htmltemplate[body_text_color]" size="70" class="large-text code spectrum_color" value="<?php echo esc_attr( $text_color ); ?>" /> <label for="cf7htmltemplate-_ody_text_color"><?php esc_html_e( 'Body Text Color(The body text colour of the main body of email)', 'cf7htmltemplate' ); ?></label>
			</p>
			<?php
		}//end method editor_panel_cf7htmltemplate_settings

		/**
		 * Save fields
		 */
		public function save_contact_form( $args ) {

			if (!empty($_POST)){
			    $form_id =  intval($args->id());

				$store_data = array();

				$post_data = isset( $_POST['cf7htmltemplate'] ) ? $_POST['cf7htmltemplate'] : [];


				$store_data['enable']        = isset( $post_data['enable'] ) ? intval( $post_data['enable'] ) : 0;
				$store_data['use_header']    = isset( $post_data['use_header'] ) ? intval( $post_data['use_header'] ) : 0;
				$store_data['header_text']   = isset( $post_data['header_text'] ) ? sanitize_text_field( $post_data['header_text'] ) : esc_html__('Contact Form Notification', 'cf7htmltemplate');
				$store_data['header_image']  = isset( $post_data['header_image'] ) ? sanitize_text_field( $post_data['header_image'] ) : '';
				$store_data['footer_text']   = isset( $post_data['footer_text'] ) ? sanitize_textarea_field( $post_data['footer_text'] ) : '{sitename}';

				$store_data['base_color']    = isset( $post_data['base_color'] ) ? sanitize_hex_color( $post_data['base_color'] ) : '#557da1';
				$store_data['bg_color']      = isset( $post_data['bg_color'] ) ? sanitize_hex_color( $post_data['bg_color'] ) : '#f5f5f5';
				$store_data['body_bg_color'] = isset( $post_data['body_bg_color'] ) ? sanitize_hex_color( $post_data['body_bg_color'] ) : '#fdfdfd';
				$store_data['text_color']    = isset( $post_data['text_color'] ) ? sanitize_hex_color( $post_data['text_color'] ) : '#505050';

                update_post_meta($form_id, '_cf7htmltemplate_settings', $store_data);
            }

		}//end method save_contact_form


		/**
		 * Parse any shortcode in cf7 email body. normally regular cf7 shortcode is parsed by it's own, but if you use any other wordpress
		 * shortcode in the email body this method will do the job using do_shortcode
		 *
		 * @param      $mail_params
		 * @param null $form
		 *
		 * @return mixed
		 */
		function mail_components_body_do_shortcode( $mail_params, $form = null ) {

			$mail = wp_parse_args( $form->prop( 'mail' ), array(
				'active'             => false,
				'recipient'          => '',
				'sender'             => '',
				'subject'            => '',
				'body'               => '',
				'additional_headers' => '',
				'attachments'        => '',
				'use_html'           => false,
				'exclude_blank'      => false,
			) );

			$use_html = isset( $mail['use_html'] ) ? intval( $mail['use_html'] ) : 0;

			if ( $use_html ) {

				$properties = get_post_meta($form->id(), '_cf7htmltemplate_settings', true);

				//$properties      = $form->prop( 'cf7htmltemplate_settings' );
				$enable = isset($properties['enable'])? intval($properties['enable']): 0;

				if($enable){
					require_once plugin_dir_path( __FILE__ ) . 'includes/emogrifier.php';
					require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7htmltemplate-mailtemplate.php';

					$properties['header_text'] = wpcf7_mail_replace_tags(esc_html($properties['header_text']));
					$properties['footer_text'] = wpcf7_mail_replace_tags($properties['footer_text']);

					$template_engine = new CF7HtmlTemplateMailTemplate( $properties );
					$message         = $template_engine->getHtmlTemplate();
					$message         = str_replace( '{mainbody}', $mail_params['body'], $message ); //replace mainbody

					$message         = $template_engine->htmlEmeilify( wpcf7_mail_replace_tags($message) );

					$mail_params['body'] = $message;
				}
			}

			return $mail_params;
		}//end method mail_components_body_do_shortcode
	}//end class CF7HtmlTemplate

	add_action( 'plugins_loaded', 'cf7htmltemplate_init' );

	/**
	 *
	 */
	function cf7htmltemplate_init() {

		if ( defined( 'WPCF7_VERSION' ) ) {
			new CF7HtmlTemplate();
		}
	}//end function cf7htmltemplate_init