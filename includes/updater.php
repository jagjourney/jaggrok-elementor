<?php
// ============================================================================
// AiMentor GITHUB UPDATER v1.3.6
// ============================================================================

if ( ! class_exists( 'AiMentor_Updater' ) ) {
	class AiMentor_Updater {
		public string $repo;
		public string $slug;
		public string $manifest_url;

		public function __construct( $repo, $slug ) {
			$this->repo = $repo;
			$this->slug = $slug;
			$this->manifest_url = 'https://aimentor-elementor.jagjourney.com/plugin-info.json';

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		}

		public function check_update( $transient ) {
			if ( empty( $transient->checked ) ) return $transient;

			$remote = $this->get_remote_info();
			if ( ! $remote ) return $transient;

                        $main_file = WP_PLUGIN_DIR . '/aimentor-elementor/aimentor-elementor.php';

                        if ( ! file_exists( $main_file ) ) {
                                $main_file = WP_PLUGIN_DIR . '/jaggrok-elementor/aimentor-elementor.php';
                        }
			$current = get_plugin_data( $main_file );

			if ( version_compare( $remote->version, $current['Version'], 'gt' ) ) {
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
                        $response = wp_remote_get( $this->manifest_url, array( 'user-agent' => 'AiMentor Updater' ) );
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

// Initialize updater
add_action( 'plugins_loaded', function() {
        if ( class_exists( 'AiMentor_Updater' ) ) {
                new AiMentor_Updater( 'jagjourney/aimentor-elementor', 'aimentor-elementor' );
        }
});
