<?php
/**
 * Helper class for registering blocks and associated scripts
 *
 * @package WooCommerce\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper class for Featured Product callback.
 */
class WC_Block_Registration {
	/**
	 * Base JS dependencies for the blocks.
	 *
	 * @var array
	 */
	protected static $js_dependencies = array(
		'wp-api-fetch',
		'wp-blocks',
		'wp-components',
		'wp-compose',
		'wp-data',
		'wp-element',
		'wp-editor',
		'wp-i18n',
		'wp-url',
		'lodash',
		'wc-vendors',
	);

	/**
	 * Base CSS dependencies for the blocks.
	 *
	 * @var array
	 */
	protected static $css_dependencies = array(
		'wc-vendors',
		'wp-edit-blocks',
		'wc-products-grid',
	);

	/**
	 * Registers a block type and the associated scripts.
	 *
	 * @since 5.0.0
	 *
	 * @param string $block_type Name for the block type & script/style handles.
	 * @param array  $args {
	 *     Array of scripts/styles to be used for this block.
	 *
	 *     @type string   $script          Relative file path to the front end script for this block.
	 *     @type string   $style           Relative file path to the front end style for this block.
	 *     @type string   $editor_script   Relative file path to the editor script for this block.
	 *     @type string   $editor_style    Relative file path to the editor style for this block.
	 *     @type callable $render_callback Callback used to render blocks of this block type.
	 *     @type array    $attributes      Block attributes mapping, property name to schema.
	 * }
	 */
	public static function register( $block_type, $args = array() ) {
		$handle            = "wc-block-{$block_type}";
		$registration_args = array();
		if ( isset( $args['render_callback'] ) ) {
			$registration_args['render_callback'] = $args['render_callback'];
		}
		if ( isset( $args['attributes'] ) ) {
			$registration_args['attributes'] = $args['attributes'];
		}
		$args = wp_parse_args(
			array(
				'css_dependencies' => array(),
				'js_dependencies'  => array(),
			),
			$args
		);

		if ( isset( $args['script'] ) ) {
			wp_register_style(
				"{$handle}",
				plugins_url( $args['script'], __FILE__ ),
				$args['js_dependencies'],
				wgpb_get_file_version( $args['script'] )
			);
			$registration_args['script'] = $handle;

			// Attach the translations to each script.
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( $handle, 'woo-gutenberg-products-block' );
			}
		}

		if ( isset( $args['style'] ) ) {
			wp_register_style(
				"{$handle}",
				plugins_url( $args['style'], __FILE__ ),
				$args['css_dependencies'],
				wgpb_get_file_version( $args['style'] )
			);
			$registration_args['style'] = $handle;
		}

		if ( isset( $args['editor_script'] ) ) {
			wp_register_script(
				"{$handle}-editor",
				plugins_url( $args['editor_script'], __FILE__ ),
				array_merge( self::$js_dependencies, $args['js_dependencies'] ),
				wgpb_get_file_version( $args['editor_script'] ),
				true
			);
			$registration_args['editor_script'] = "{$handle}-editor";

			// Attach the translations to each script.
			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( "{$handle}-editor", 'woo-gutenberg-products-block' );
			}
		}

		if ( isset( $args['editor_style'] ) ) {
			wp_register_style(
				"{$handle}-editor",
				plugins_url( $args['editor_style'], __FILE__ ),
				array_merge( self::$css_dependencies, $args['css_dependencies'] ),
				wgpb_get_file_version( $args['editor_style'] )
			);
			$registration_args['editor_style'] = "{$handle}-editor";
		}

		register_block_type(
			"woocommerce/{$block_type}",
			$registration_args
		);
	}
}
