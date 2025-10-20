<?php
// ============================================================================
// JAGJourney GITHUB UPDATER v1.0.0
// ============================================================================

if ( ! class_exists( 'JagGrok_Updater' ) ) {
	class JagGrok_Updater {
		public function __construct( $repo, $slug ) {
			$this->repo = $repo; // e.g., 'jagjourney/jaggrok-elementor'
			$this->slug = $slug; // 'jaggrok-elementor'
			$this->manifest_url = 'https://jagjourney.github.io/jaggrok-elementor/plugin-info.json'; // Your JSON

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		}

		public function check_update( $transient ) {
			if ( empty( $transient->checked ) ) return $transient;

			$remote = $this->get_remote_info();
			if ( ! $remote ) return $transient;

			$current = get_plugin_data( plugin_dir_path( __DIR__ ) . $this->slug . '.php' );
			if ( version_compare( $remote->version, $current['Version'], 'gt' ) ) {
				$transient->response[ $this->slug . '/' . $this->slug . '.php' ] = $remote;
			}
			return $transient;
		}

		public function plugin_info( $result, $action, $args ) {
			if ( $action !== 'plugin_information' || $args->slug !== $this->slug ) return $result;

			return $this->get_remote_info();
		}

		private function get_remote_info() {
			$response = wp_remote_get( $this->manifest_url, array( 'user-agent' => 'JagGrok Updater' ) );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) return false;

			$remote = json_decode( wp_remote_retrieve_body( $response ) );
			$remote->download_link = str_replace( '%repo%', $this->repo, $remote->download_link ?? '' ); // If dynamic
			$remote->sections = isset( $remote->sections ) ? (array) $remote->sections : array();
			$remote->banners = isset( $remote->banners ) ? (array) $remote->banners : array();
			$remote->icons = array(); // Add if needed
			return $remote;
		}
	}
}

// Initialize in main file
add_action( 'plugins_loaded', function() {
	if ( jaggrok_check_dependencies() ) { // From our core
		new JagGrok_Updater( 'jagjourney/jaggrok-elementor', 'jaggrok-elementor' );
	}
});