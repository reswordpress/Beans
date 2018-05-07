<?php
/**
 * This class provides the means to set the options and actions of the Beans Compiler.
 *
 * @package Beans\Framework\API\Compiler
 *
 * @since   1.0.0
 */

/**
 * Options and Actions used by Beans Compiler.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Compiler
 */
final class _Beans_Compiler_Options {

	/**
	 * Initialize the hooks.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_init', array( $this, 'flush' ), - 1 );
		add_action( 'admin_notices', array( $this, 'render_success_notice' ) );
		add_action( 'beans_field_flush_cache', array( $this, 'render_flush_button' ) );
		add_action(
			'beans_field_description_beans_compile_all_styles_append_markup',
			array( $this, 'render_styles_not_compiled_notice' )
		);
		add_action(
			'beans_field_description_beans_compile_all_scripts_group_append_markup',
			array( $this, 'render_scripts_not_compiled_notice' )
		);
	}

	/**
	 * Register options.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function register() {
		$fields = require dirname( __FILE__ ) . '/config/fields.php';

		// If styles are not supported, from the style fields.
		if ( ! beans_get_component_support( 'wp_styles_compiler' ) ) {
			unset( $fields['beans_compile_all_styles'] );
		}

		// If scripts are not supported, from the script fields.
		if ( ! beans_get_component_support( 'wp_scripts_compiler' ) ) {
			unset( $fields['beans_compile_all_scripts_group'] );
		}

		return beans_register_options(
			$fields,
			'beans_settings',
			'compiler_options',
			array(
				'title'   => __( 'Compiler options', 'tm-beans' ),
				'context' => 'normal',
			)
		);
	}

	/**
	 * Flush the cached files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function flush() {

		if ( ! beans_post( 'beans_flush_compiler_cache' ) ) {
			return;
		}

		beans_remove_dir( beans_get_compiler_dir() );
	}

	/**
	 * Renders the success notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_success_notice() {

		if ( ! beans_post( 'beans_flush_compiler_cache' ) ) {
			return;
		}

		include dirname( __FILE__ ) . '/views/flushed-notice.php';
	}

	/**
	 * Render the flush button, which is used to flush the cache.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Registered options.
	 *
	 * @return void
	 */
	public function render_flush_button( $field ) {

		if ( 'beans_compiler_items' !== $field['id'] ) {
			return;
		}

		include dirname( __FILE__ ) . '/views/flush-button.php';
	}

	/**
	 * Render a notice when styles should not be compiled.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_styles_not_compiled_notice() {

		if ( ! _beans_is_compiler_dev_mode() ) {
			return;
		}

		if ( ! get_option( 'beans_compile_all_styles' ) ) {
			return;
		}

		$message = __( 'Styles are not compiled in development mode.', 'tm-beans' );

		include dirname( __FILE__ ) . '/views/not-compiled-notice.php';
	}

	/**
	 * Maybe show disabled notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_scripts_not_compiled_notice() {

		if ( ! _beans_is_compiler_dev_mode() ) {
			return;
		}

		if ( ! get_option( 'beans_compile_all_scripts' ) ) {
			return;
		}

		$message = __( 'Scripts are not compiled in development mode.', 'tm-beans' );

		include dirname( __FILE__ ) . '/views/not-compiled-notice.php';
	}
}
