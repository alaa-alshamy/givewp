<?php

namespace Give\DonorProfiles;

use Give\ServiceProviders\ServiceProvider as ServiceProviderInterface;
use Give\Helpers\Hooks;
use Give\DonorProfiles\Shortcode as Shortcode;
use Give\DonorProfiles\Block as Block;
use Give\DonorProfiles\App as App;
use Give\DonorProfiles\RequestHandler as RequestHandler;

use Give\DonorProfiles\Tabs\ProfileTab\Tab as ProfileTab;
use Give\DonorProfiles\Tabs\DonationHistoryTab\Tab as DonationHistoryTab;

use Give\DonorProfiles\Tabs\TabsRegister;

class ServiceProvider implements ServiceProviderInterface {

	/**
	 * @inheritDoc
	 */
	public function register() {

		give()->singleton( 'donorProfileTabs', TabsRegister::class );

	}

	/**
	 * @inheritDoc
	 */
	public function boot() {

		Hooks::addAction( 'give_embed_head', App::class, 'loadAssets' );

		Hooks::addFilter( 'query_vars', RequestHandler::class, 'filterQueryVars' );
		Hooks::addAction( 'parse_request', RequestHandler::class, 'parseRequest' );

		Hooks::addAction( 'init', Shortcode::class, 'addShortcode' );

		if ( function_exists( 'register_block_type' ) ) {
			Hooks::addAction( 'init', Block::class, 'addBlock' );
			Hooks::addAction( 'enqueue_block_editor_assets', Block::class, 'loadEditorAssets' );
		}

		// Register Tabs
		Hooks::addAction( 'init', ProfileTab::class, 'registerTab' );
		Hooks::addAction( 'init', DonationHistoryTab::class, 'registerTab' );

		Hooks::addAction( 'give_embed_head', TabsRegister::class, 'enqueueTabAssets' );
		Hooks::addAction( 'rest_api_init', TabsRegister::class, 'registerTabRoutes' );

	}
}
