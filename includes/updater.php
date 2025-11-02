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

                        $args = wp_parse_args( $args, $defaults );

                        $this->repo          = $repo ?: 'jagjourney/aimentor-elementor';
                        $this->slug          = $slug ?: 'aimentor-elementor';
                        if ( empty( $plugin_file ) && ! empty( $unused_manifest_url ) ) {
                                $plugin_file = $unused_manifest_url;
                        }

                        $this->plugin_file   = $plugin_file ?: 'aimentor-elementor/aimentor-elementor.php';
                        $this->legacy_slugs  = $this->sanitize_list( $args['legacy_slugs'] );
                        $this->legacy_plugin_files  = $this->sanitize_list( $args['legacy_plugin_files'] );
                        $this->legacy_repos         = $this->sanitize_list( $args['legacy_repos'] );

                        $main_file = WP_PLUGIN_DIR . '/aimentor-elementor/aimentor-elementor.php';

                        if ( ! file_exists( $main_file ) ) {
                                $main_file = WP_PLUGIN_DIR . '/jaggrok-elementor/aimentor-elementor.php';
                        }
			$current = get_plugin_data( $main_file );

                private function sanitize_list( $values ): array {
                        if ( ! is_array( $values ) ) {
                                $values = array();
                        }

                        $values = array_map( 'strval', $values );
                        $values = array_map( 'trim', $values );
                        $values = array_filter( $values );

                        return array_values( array_unique( $values ) );
                }

                private function get_all_plugin_files(): array {
                        return array_values(
                                array_unique(
                                        array_merge(
                                                array( $this->plugin_file ),
                                                $this->legacy_plugin_files
                                        )
                                )
                        );
                }

                private function get_all_slugs(): array {
                        return array_values(
                                array_unique(
                                        array_merge(
                                                array( $this->slug ),
                                                $this->legacy_slugs
                                        )
                                )
                        );
                }

		private function get_remote_info() {
                        $response = wp_remote_get( $this->manifest_url, array( 'user-agent' => 'AiMentor Updater' ) );
			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) return false;

                        $remote = $this->get_remote_info();
                        if ( ! $remote || empty( $remote->version ) ) {
                                return $transient;
                        }

                        $installed_version = $this->get_installed_version( $transient );
                        if ( ! $installed_version ) {
                                return $transient;
                        }

                        if ( version_compare( $remote->version, $installed_version, 'gt' ) ) {
                                foreach ( $this->get_all_plugin_files() as $plugin_file ) {
                                        $transient->response[ $plugin_file ] = $this->format_response( $remote, $plugin_file );
                                }
                        }

                        return $transient;
                }

                private function get_installed_version( $transient ): ?string {
                        foreach ( $this->get_all_plugin_files() as $plugin_file ) {
                                if ( isset( $transient->checked[ $plugin_file ] ) && ! empty( $transient->checked[ $plugin_file ] ) ) {
                                        return $transient->checked[ $plugin_file ];
                                }

                                $path = rtrim( WP_PLUGIN_DIR, '/\\' ) . '/' . ltrim( $plugin_file, '/\\' );

                                if ( file_exists( $path ) ) {
                                        if ( ! function_exists( 'get_plugin_data' ) ) {
                                                require_once ABSPATH . 'wp-admin/includes/plugin.php';
                                        }

                                        $plugin_data = get_plugin_data( $path );

                                        if ( ! empty( $plugin_data['Version'] ) ) {
                                                return $plugin_data['Version'];
                                        }
                                }
                        }

                        return null;
                }

                private function format_response( $remote, string $plugin_file ): \stdClass {
                        $response = clone $remote;
                        $response->new_version    = $remote->version;
                        $response->plugin         = $plugin_file;
                        $response->package        = $remote->download_link ?? '';
                        $response->upgrade_notice = $remote->upgrade_notice ?? '';

                        $slug = dirname( $plugin_file );
                        if ( '.' === $slug || '' === $slug ) {
                                $slug = $this->slug;
                        }

                        if ( ! in_array( $slug, $this->get_all_slugs(), true ) ) {
                                $slug = $this->slug;
                        }

                        $response->slug = $slug;

                        return $response;
                }

                public function plugin_info( $result, $action, $args ) {
                        if ( 'plugin_information' !== $action || empty( $args->slug ) ) {
                                return $result;
                        }

                        if ( ! in_array( $args->slug, $this->get_all_slugs(), true ) ) {
                                return $result;
                        }

                        $remote = $this->get_remote_info();
                        if ( ! $remote ) {
                                return $result;
                        }

                        $response_slug = in_array( $args->slug, $this->legacy_slugs, true ) ? $args->slug : $this->slug;

                        return (object) array(
                                'name'         => $remote->name ?? 'AiMentor Elementor',
                                'slug'         => $response_slug,
                                'version'      => $remote->version ?? '',
                                'author'       => $remote->author ?? '',
                                'requires'     => $remote->requires ?? '',
                                'tested'       => $remote->tested ?? '',
                                'requires_php' => $remote->requires_php ?? '',
                                'download_link'=> $remote->download_link ?? '',
                                'homepage'     => $remote->homepage ?? '',
                                'sections'     => isset( $remote->sections ) ? (array) $remote->sections : array(),
                                'banners'      => isset( $remote->banners ) ? (array) $remote->banners : array(),
                                'icons'        => isset( $remote->icons ) ? (array) $remote->icons : array(),
                        );
                }

                private function get_remote_info() {
                        foreach ( $this->get_all_repos() as $repo ) {
                                $remote = $this->fetch_latest_release( $repo );

                                if ( $remote ) {
                                        return $remote;
                                }
                        }

                        return false;
                }

                private function get_all_repos(): array {
                        return array_values(
                                array_unique(
                                        array_merge(
                                                array( $this->repo ),
                                                $this->legacy_repos
                                        )
                                )
                        );
                }

                private function fetch_latest_release( string $repo ) {
                        $repo = trim( $repo );

                        if ( '' === $repo ) {
                                return false;
                        }

                        $url = sprintf( 'https://api.github.com/repos/%s/releases/latest', $repo );

                        $response = wp_remote_get(
                                $url,
                                array(
                                        'user-agent' => $this->get_user_agent(),
                                        'headers'    => array(
                                                'Accept' => 'application/vnd.github+json',
                                        ),
                                        'timeout'    => 20,
                                )
                        );

                        if ( is_wp_error( $response ) ) {
                                return false;
                        }

                        if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
                                return false;
                        }

                        $release = json_decode( wp_remote_retrieve_body( $response ) );
                        if ( ! $release ) {
                                return false;
                        }

                        return $this->transform_release_payload( $release, $repo );
                }

                private function transform_release_payload( $release, string $repo ) {
                        $version = $this->normalize_version( $release->tag_name ?? '' );

                        if ( ! $version ) {
                                $version = $this->normalize_version( $release->name ?? '' );
                        }

                        if ( ! $version ) {
                                return false;
                        }

                        $download_link = $this->find_release_asset( $release );
                        if ( ! $download_link && ! empty( $release->zipball_url ) ) {
                                $download_link = $release->zipball_url;
                        }

                        $plugin_headers = $this->get_plugin_headers();

                        $sections = array();
                        if ( ! empty( $plugin_headers['Description'] ) ) {
                                $sections['description'] = $this->format_markdown( $plugin_headers['Description'] );
                        }

                        if ( ! empty( $release->body ) ) {
                                $sections['changelog'] = $this->format_markdown( $release->body );
                        }

                        $remote = (object) array(
                                'name'          => $plugin_headers['Name'] ?? 'AiMentor Elementor',
                                'slug'          => $this->slug,
                                'version'       => $version,
                                'download_link' => $download_link,
                                'author'        => $plugin_headers['Author'] ?? ( $release->author->login ?? 'AiMentor' ),
                                'requires'      => $plugin_headers['RequiresWP'] ?? '',
                                'tested'        => $plugin_headers['TestedUpTo'] ?? '',
                                'requires_php'  => $plugin_headers['RequiresPHP'] ?? '',
                                'homepage'      => sprintf( 'https://github.com/%s', $repo ),
                                'sections'      => $sections,
                                'upgrade_notice'=> $this->extract_upgrade_notice( $release->body ?? '' ),
                                'last_updated'  => $release->published_at ?? '',
                        );

                        return $remote;
                }

                private function find_release_asset( $release ): string {
                        if ( empty( $release->assets ) || ! is_array( $release->assets ) ) {
                                return '';
                        }

                        $fallback = '';
                        $slug = $this->slug;

                        foreach ( $release->assets as $asset ) {
                                if ( empty( $asset->browser_download_url ) ) {
                                        continue;
                                }

                                $url  = (string) $asset->browser_download_url;
                                $name = isset( $asset->name ) ? strtolower( (string) $asset->name ) : '';

                                if ( '' === $fallback ) {
                                        $fallback = $url;
                                }

                                if ( preg_match( '/\.zip$/', $url ) ) {
                                        if ( '' === $name || false !== strpos( $name, strtolower( $slug ) ) ) {
                                                return $url;
                                        }

                                        $fallback = $url;
                                }
                        }

                        return $fallback;
                }

                private function get_user_agent(): string {
                        $site_url = function_exists( 'home_url' ) ? home_url() : '';

                        if ( $site_url ) {
                                $sanitized = function_exists( 'esc_url_raw' ) ? esc_url_raw( $site_url ) : $site_url;

                                return sprintf( 'AiMentor Updater (%s)', $sanitized );
                        }

                        return 'AiMentor Updater';
                }

                private function normalize_version( $value ): string {
                        $value = trim( (string) $value );

                        if ( '' === $value ) {
                                return '';
                        }

                        if ( preg_match( '/\d+(?:\.\d+)*/', $value, $matches ) ) {
                                return $matches[0];
                        }

                        return $value;
                }

                private function format_markdown( string $content ): string {
                        $content = trim( $content );

                        if ( '' === $content ) {
                                return '';
                        }

                        if ( function_exists( 'wpautop' ) ) {
                                $content = wpautop( $content );
                        } else {
                                $escape_callback = function_exists( 'esc_html' ) ? 'esc_html' : 'htmlspecialchars';
                                $content         = '<p>' . str_replace( array( "\r\n", "\r", "\n" ), '</p><p>', call_user_func( $escape_callback, $content ) ) . '</p>';
                        }

                        return $content;
                }

                private function extract_upgrade_notice( string $content ): string {
                        $content = trim( $content );

                        if ( '' === $content ) {
                                return '';
                        }

                        $lines = preg_split( '/\r\n|\r|\n/', $content );

                        foreach ( $lines as $line ) {
                                $line = trim( $line );

                                if ( '' === $line ) {
                                        continue;
                                }

                                if ( 0 === strpos( $line, '* ' ) ) {
                                        return substr( $line, 2 );
                                }

                                if ( 0 === strpos( $line, '- ' ) ) {
                                        return substr( $line, 2 );
                                }

                                return $line;
                        }

                        return '';
                }

                private function get_plugin_headers(): array {
                        static $headers = null;

                        if ( null !== $headers ) {
                                return $headers;
                        }

                        $headers = array();

                        $path = rtrim( WP_PLUGIN_DIR, '/\\' ) . '/' . ltrim( $this->plugin_file, '/\\' );

                        if ( ! file_exists( $path ) ) {
                                return $headers;
                        }

                        if ( ! function_exists( 'get_plugin_data' ) ) {
                                require_once ABSPATH . 'wp-admin/includes/plugin.php';
                        }

                        $data = get_plugin_data( $path );

                        if ( is_array( $data ) ) {
                                $headers = $data;
                        }

                        return $headers;
                }
        }
}

// Initialize updater
add_action( 'plugins_loaded', function() {
        if ( class_exists( 'AiMentor_Updater' ) ) {
                new AiMentor_Updater( 'jagjourney/aimentor-elementor', 'aimentor-elementor' );
        }
});
