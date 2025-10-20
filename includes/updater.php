<?php
// ============================================================================
// JAGJourney GITHUB UPDATER v1.0.4 (FULL WP COMPATIBILITY)
// ============================================================================

if ( ! class_exists( 'JagGrok_Updater' ) ) {
	class JagGrok_Updater {
		// PHP 8.2+ FIX: Explicitly declare properties
		public string $repo;
		public string $slug;
		public string $manifest_url;

		public function __construct( $repo, $slug ) {
			$this->repo = $repo; // e.g., 'jagjourney/jaggrok-elementor'
			$this->slug = $slug; // 'jaggrok-elementor'
			$this->manifest_url = 'https://jaggrok-elementor.jagjourney.com/plugin-info.json';

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		}

		public function check_update( $transient ) {
			if ( empty( $transient->checked ) ) return $transient;

			$remote = $this->get_remote_info();
			if ( ! $remote ) return $transient;

			// FIXED PATH: Use WP_PLUGIN_DIR for correct main file location
			$main_file = WP_PLUGIN_DIR . '/jaggrok-elementor/jaggrok-elementor.php';
			$current = get_plugin_data( $main_file );

			if ( version_compare( $remote->version, $current['Version'], 'gt' ) ) {
				// CRITICAL FIX v1.0.4: WP requires 'new_version' property
				$remote->new_version = $remote->version;
				$remote->plugin = $this->slug . '/' . $this->slug . '.php';
				$remote->package = $remote->download_link;
				$remote->upgrade_notice = '';

				$transient->response[ $this->slug . '/' . $this->slug . '.php' ] = $remote;
			}
			return $transient;
		}

		public function plugin_info( $result, $action, $args ) {
			if ( $action !== 'plugin_information' || $args->slug !== $this->slug ) return $result;

			$remote = $this->get_remote_info();
			if ( ! $remote ) return $result;

			// WP Plugin API format
			return (object) array(
				'name' => $remote->name,
				'slug' => $remote->slug,
				'version' => $remote->version,
				'author' => $remote->author,
				'requires' => $remote->requires,
				'tested' => $remote->tested,
				'requires_php' => $remote->requires_php,
				'download_link' => $remote->download_link,
				'homepage' => $remote->homepage,
				'sections' => $remote->sections,
				'banners' => $remote->banners,
				'icons' => $remote->icons
			);
		}

		private function get_remote_info() {
			$response = wp_remote_get( $this->manifest_url, array( 'user-agent' => 'JagGrok Updater' ) );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) return false;

			$remote = json_decode( wp_remote_retrieve_body( $response ) );
			if ( ! $remote ) return false;

			$remote->sections = isset( $remote->sections ) ? (array) $remote->sections : array();
			$remote->banners = isset( $remote->banners ) ? (array) $remote->banners : array();
			$remote->icons = isset( $remote->icons ) ? (array) $remote->icons : array();
			return $remote;
		}
	}
}

// Initialize updater (v1.0.4)
add_action( 'plugins_loaded', function() {
	if ( class_exists( 'JagGrok_Updater' ) ) {
		new JagGrok_Updater( 'jagjourney/jaggrok-elementor', 'jaggrok-elementor' );
	}
});