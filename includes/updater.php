<?php
// =============================================================================
// AiMentor GITHUB UPDATER v1.3.6
// =============================================================================

if ( ! class_exists( 'AiMentor_Updater' ) ) {
	class AiMentor_Updater {
		public string $repo;
		public string $slug;
		public string $manifest_url;

		private string $plugin_file;
		private array $legacy_slugs = array();
		private array $legacy_plugin_files = array();
		private array $legacy_repos = array();
		private $remote_info = null;

		public function __construct( $repo = 'jagjourney/aimentor-elementor', $slug = 'aimentor-elementor', array $args = array() ) {
			$defaults = array(
				'manifest_url'        => 'https://aimentor-elementor.jagjourney.com/plugin-info.json',
				'plugin_file'         => 'aimentor-elementor/aimentor-elementor.php',
				'legacy_slugs'        => array( 'jaggrok-elementor' ),
				'legacy_plugin_files' => array( 'jaggrok-elementor/aimentor-elementor.php' ),
				'legacy_repos'        => array( 'jagjourney/jaggrok-elementor' ),
			);

			if ( function_exists( 'wp_parse_args' ) ) {
				$args = wp_parse_args( $args, $defaults );
			} else {
				$args = array_merge( $defaults, $args );
			}

			$this->repo = $this->sanitize_repo( $repo );
			if ( '' === $this->repo ) {
				$this->repo = 'jagjourney/aimentor-elementor';
			}

			$this->slug = $this->sanitize_slug( $slug );
			if ( '' === $this->slug ) {
				$this->slug = 'aimentor-elementor';
			}

			$manifest_url       = $this->sanitize_url( $args['manifest_url'] ?? '' );
			$this->manifest_url = $manifest_url ?: $defaults['manifest_url'];

			$plugin_file = $this->sanitize_plugin_file( $args['plugin_file'] ?? '' );
			if ( '' === $plugin_file ) {
				$plugin_file = $this->sanitize_plugin_file( $defaults['plugin_file'] );
			}
			$this->plugin_file = $plugin_file;

			$this->legacy_slugs        = $this->sanitize_list( $args['legacy_slugs'] ?? array() );
			$this->legacy_plugin_files = $this->sanitize_list( $args['legacy_plugin_files'] ?? array() );
			$this->legacy_repos        = $this->sanitize_list( $args['legacy_repos'] ?? array() );

			if ( function_exists( 'add_filter' ) ) {
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
				add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
			}
		}

		public function check_for_update( $transient ) {
			if ( ! is_object( $transient ) || empty( $transient->checked ) || ! is_array( $transient->checked ) ) {
				return $transient;
			}

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

			$sections = array();
			if ( ! empty( $remote->notes ) ) {
				$sections['description'] = $this->format_markdown( (string) $remote->notes );
			}

			return (object) array(
				'name'          => $remote->name ?? 'AiMentor Elementor',
				'slug'          => $response_slug,
				'author'        => $remote->author ?? 'Jag Journey',
				'homepage'      => $remote->source ?? sprintf( 'https://github.com/%s', $this->repo ),
				'last_updated'  => $remote->released_at ?? '',
				'version'       => $remote->version ?? '',
				'requires'      => $remote->requires ?? '',
				'tested'        => $remote->tested ?? '',
				'requires_php'  => $remote->requires_php ?? '',
				'download_link' => $remote->download_url ?? '',
				'sections'      => $sections,
				'banners'       => array(),
				'icons'         => array(),
			);
		}

		private function sanitize_repo( $repo ): string {
			$repo = is_string( $repo ) ? trim( $repo ) : '';

			if ( '' === $repo ) {
				return '';
			}

			$repo = preg_replace( '/[^A-Za-z0-9._\-\/]/', '', $repo );

			return $repo;
		}

		private function sanitize_slug( $slug ): string {
			$slug = is_string( $slug ) ? trim( $slug ) : '';

			if ( '' === $slug ) {
				return '';
			}

			if ( function_exists( 'sanitize_key' ) ) {
				$slug = sanitize_key( $slug );
			} else {
				$slug = strtolower( preg_replace( '/[^A-Za-z0-9_\-]/', '', $slug ) );
			}

			return $slug;
		}

		private function sanitize_url( $url ): string {
			$url = is_string( $url ) ? trim( $url ) : '';

			if ( '' === $url ) {
				return '';
			}

			if ( function_exists( 'esc_url_raw' ) ) {
				$url = esc_url_raw( $url );
			}

			return $url;
		}

		private function sanitize_plugin_file( $plugin_file ): string {
			$plugin_file = is_string( $plugin_file ) ? trim( $plugin_file ) : '';

			if ( '' === $plugin_file ) {
				return '';
			}

			$plugin_file = str_replace( '\\', '/', $plugin_file );
			$plugin_file = preg_replace( '#/+#', '/', $plugin_file );

			return ltrim( $plugin_file, '/' );
		}

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
			if ( null !== $this->remote_info ) {
				return $this->remote_info;
			}

			if ( '' === $this->manifest_url || ! function_exists( 'wp_remote_get' ) ) {
				$this->remote_info = false;
				return false;
			}

			$response = wp_remote_get(
				$this->manifest_url,
				array(
					'user-agent' => $this->get_user_agent(),
					'timeout'    => 20,
				)
			);

			if ( is_wp_error( $response ) ) {
				$this->remote_info = false;
				return false;
			}

			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				$this->remote_info = false;
				return false;
			}

			$body = wp_remote_retrieve_body( $response );
			if ( ! is_string( $body ) || '' === trim( $body ) ) {
				$this->remote_info = false;
				return false;
			}

			$data = json_decode( $body );
			if ( ! $data || ! is_object( $data ) ) {
				$this->remote_info = false;
				return false;
			}

			if ( empty( $data->slug ) ) {
				$data->slug = $this->slug;
			}

			if ( empty( $data->name ) ) {
				$data->name = 'AiMentor Elementor';
			}

			if ( empty( $data->source ) ) {
				$data->source = sprintf( 'https://github.com/%s', $this->repo );
			}

			$this->remote_info = $data;

			return $this->remote_info;
		}

		private function get_installed_version( $transient ): ?string {
			foreach ( $this->get_all_plugin_files() as $plugin_file ) {
				if ( isset( $transient->checked[ $plugin_file ] ) && ! empty( $transient->checked[ $plugin_file ] ) ) {
					return $transient->checked[ $plugin_file ];
				}

				if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
					continue;
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
                        $response = new \stdClass();
			$response->slug           = $remote->slug ?? $this->slug;
			$response->plugin         = $plugin_file;
			$response->new_version    = $remote->version ?? '';
			$response->package        = $remote->download_url ?? '';
			$response->url            = $remote->source ?? sprintf( 'https://github.com/%s', $this->repo );
			$response->upgrade_notice = $this->extract_upgrade_notice( $remote->notes ?? '' );

			return $response;
		}

		private function get_user_agent(): string {
			$site_url = function_exists( 'home_url' ) ? home_url() : '';

			if ( $site_url ) {
				$sanitized = function_exists( 'esc_url_raw' ) ? esc_url_raw( $site_url ) : $site_url;

				return sprintf( 'AiMentor Updater (%s)', $sanitized );
			}

			return 'AiMentor Updater';
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
	}
}

// Initialize updater
add_action( 'plugins_loaded', function() {
	if ( class_exists( 'AiMentor_Updater' ) ) {
		new AiMentor_Updater( 'jagjourney/aimentor-elementor', 'aimentor-elementor' );
	}
} );
