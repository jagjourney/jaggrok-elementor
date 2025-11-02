<?php
// ============================================================================
// AiMentor Updater v1.4.0
// ============================================================================

if ( ! class_exists( 'AiMentor_Updater' ) ) {
        class AiMentor_Updater {
                public string $repo;
                public string $slug;
                public string $manifest_url;
                public string $plugin_file;
                /**
                 * @var string[]
                 */
                public array $legacy_slugs = array();
                /**
                 * @var string[]
                 */
                public array $legacy_plugin_files = array();
                /**
                 * @var string[]
                 */
                public array $legacy_repos = array();
                /**
                 * @var string[]
                 */
                public array $legacy_manifest_urls = array();

                public function __construct(
                        string $repo = 'aimentor/aimentor-elementor',
                        string $slug = 'aimentor-elementor',
                        string $manifest_url = 'https://updates.aimentor.ai/elementor/plugin-info.json',
                        string $plugin_file = 'aimentor-elementor/aimentor-elementor.php',
                        array $args = array()
                ) {
                        $defaults = array(
                                'legacy_slugs'         => array( 'jaggrok-elementor' ),
                                'legacy_plugin_files'  => array(
                                        'jaggrok-elementor/aimentor-elementor.php',
                                        'jaggrok-elementor/jaggrok-elementor.php',
                                ),
                                'legacy_repos'         => array( 'jagjourney/aimentor-elementor' ),
                                'legacy_manifest_urls' => array(
                                        'https://aimentor-elementor.jagjourney.com/plugin-info.json',
                                ),
                        );

                        $args = wp_parse_args( $args, $defaults );

                        $this->repo          = $repo ?: 'aimentor/aimentor-elementor';
                        $this->slug          = $slug ?: 'aimentor-elementor';
                        $this->manifest_url  = $manifest_url ?: 'https://updates.aimentor.ai/elementor/plugin-info.json';
                        $this->plugin_file   = $plugin_file ?: 'aimentor-elementor/aimentor-elementor.php';
                        $this->legacy_slugs  = $this->sanitize_list( $args['legacy_slugs'] );
                        $this->legacy_plugin_files  = $this->sanitize_list( $args['legacy_plugin_files'] );
                        $this->legacy_repos         = $this->sanitize_list( $args['legacy_repos'] );
                        $this->legacy_manifest_urls = $this->sanitize_list( $args['legacy_manifest_urls'] );

                        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
                        add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
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

                public function check_update( $transient ) {
                        if ( empty( $transient->checked ) ) {
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
                        $urls = array_merge( array( $this->manifest_url ), $this->legacy_manifest_urls );

                        foreach ( $urls as $url ) {
                                if ( empty( $url ) ) {
                                        continue;
                                }

                                $response = wp_remote_get(
                                        $url,
                                        array(
                                                'user-agent' => 'AiMentor Updater',
                                        )
                                );

                                if ( is_wp_error( $response ) ) {
                                        continue;
                                }

                                if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
                                        continue;
                                }

                                $remote = json_decode( wp_remote_retrieve_body( $response ) );
                                if ( ! $remote ) {
                                        continue;
                                }

                                if ( empty( $remote->slug ) ) {
                                        $remote->slug = $this->slug;
                                }

                                $remote->sections = isset( $remote->sections ) ? (array) $remote->sections : array();
                                $remote->banners  = isset( $remote->banners ) ? (array) $remote->banners : array();
                                $remote->icons    = isset( $remote->icons ) ? (array) $remote->icons : array();

                                return $remote;
                        }

                        return false;
                }
        }
}

if ( class_exists( 'AiMentor_Updater' ) && ! class_exists( 'JagGrok_Updater' ) ) {
        class_alias( 'AiMentor_Updater', 'JagGrok_Updater' );
}
