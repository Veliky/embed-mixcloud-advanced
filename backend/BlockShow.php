<?php

namespace Veliky\EMA;

/**
 * Class BlockShow
 *
 * @package Veliky\EMA
 */
class BlockShow {

	/**
	 * @var array
	 */
	private $config = [
		'editor_script' => 'dist/block-show-editor-bundle.min.asset.php',
		'script'        => 'dist/public-bundle.min.asset.php',
	];

	/**
	 * @var string
	 */
	private $api_script_url = 'https://widget.mixcloud.com/media/js/widgetApi.js';

	/**
	 * BlockShow constructor.
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'register_block' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 5 );
	}

	public function wp_enqueue_scripts() {

		wp_register_script( prefix( 'mixcloud_api' ), $this->api_script_url, [], null, true );

		$editor_script_data                   = include( PLUGIN_DIR . $this->config['editor_script'] );
		$editor_script_data['dependencies'][] = prefix( 'mixcloud_api' );

		wp_register_script(
			prefix( 'block_show_editor_script' ),
			PLUGIN_URL . 'dist/block-show-editor-bundle.min.js',
			$editor_script_data['dependencies'],
			$editor_script_data['version']
		);

		$script_data = include( PLUGIN_DIR . $this->config['script'] );

		wp_register_script(
			prefix( 'public_script' ),
			PLUGIN_URL . 'dist/public-bundle.min.js',
			[ prefix( 'mixcloud_api' ) ],
			$script_data['version'],
			true
		);

		wp_register_style(
			prefix( 'block_show_editor_style' ),
			PLUGIN_URL . 'dist/block-show.min.css',
			[],
			filemtime( PLUGIN_DIR . 'dist/block-show.min.css' )
		);

		wp_set_script_translations( prefix( 'block_show_editor_script' ), 'embed-mixcloud-advanced', PLUGIN_DIR . 'languages' );
		wp_set_script_translations( prefix( 'public_script' ), 'embed-mixcloud-advanced', PLUGIN_DIR . 'languages' );
	}

	/**
	 * Registers all block assets so that they can be enqueued through Gutenberg in
	 * the corresponding context.
	 *
	 * Passes translations to JavaScript.
	 */
	public function register_block() {

		register_block_type( 'veliky/mixcloud-show', [
			'editor_script' => prefix( 'block_show_editor_script' ),
			'style'         => prefix( 'block_show_editor_style' ),
			'script'        => prefix( 'public_script' ),
		] );
	}
}
