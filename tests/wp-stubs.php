<?php
// Minimal WordPress stubs for standalone option sanitization tests.

global $aimentor_test_options;
global $aimentor_test_option_sources;
$aimentor_test_options        = [];
$aimentor_test_option_sources = [];

if ( ! function_exists( 'aimentor_test_reset_options' ) ) {
        function aimentor_test_reset_options( array $seed = [] ) {
                global $aimentor_test_options;
                global $aimentor_test_option_sources;

                $aimentor_test_options        = [];
                $aimentor_test_option_sources = [];

                foreach ( $seed as $option => $value ) {
                        aimentor_test_preload_option( $option, $value );
                }
        }
}

if ( ! function_exists( 'aimentor_test_preload_option' ) ) {
        function aimentor_test_preload_option( $name, $value ) {
                global $aimentor_test_options;
                global $aimentor_test_option_sources;

                $aimentor_test_options[ $name ]        = $value;
                $aimentor_test_option_sources[ $name ] = 'preloaded';
        }
}

if ( ! function_exists( 'aimentor_test_get_option_source' ) ) {
        function aimentor_test_get_option_source( $name ) {
                global $aimentor_test_option_sources;

                return $aimentor_test_option_sources[ $name ] ?? null;
        }
}

if ( ! function_exists( '__' ) ) {
        function __( $text, $domain = null ) {
                return $text;
        }
}

if ( ! function_exists( '_x' ) ) {
        function _x( $text, $context, $domain = null ) {
                return $text;
        }
}

if ( ! function_exists( 'esc_html__' ) ) {
        function esc_html__( $text, $domain = null ) {
                return $text;
        }
}

if ( ! function_exists( 'esc_attr__' ) ) {
        function esc_attr__( $text, $domain = null ) {
                return $text;
        }
}

if ( ! function_exists( 'esc_html_e' ) ) {
        function esc_html_e( $text, $domain = null ) {
                echo $text;
        }
}

if ( ! function_exists( 'esc_attr_e' ) ) {
        function esc_attr_e( $text, $domain = null ) {
                echo $text;
        }
}

if ( ! function_exists( 'esc_html' ) ) {
        function esc_html( $text ) {
                return $text;
        }
}

if ( ! function_exists( 'esc_attr' ) ) {
        function esc_attr( $text ) {
                return $text;
        }
}

if ( ! function_exists( 'esc_textarea' ) ) {
        function esc_textarea( $text ) {
                return $text;
        }
}

if ( ! function_exists( 'esc_url' ) ) {
        function esc_url( $url ) {
                return $url;
        }
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
        function sanitize_text_field( $str ) {
                if ( is_array( $str ) ) {
                        return '';
                }

                $filtered = (string) $str;
                $filtered = strip_tags( $filtered );
                $filtered = preg_replace( '/[\r\n\t\0\x0B]+/', ' ', $filtered );

                return trim( $filtered );
        }
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
        function sanitize_textarea_field( $str ) {
                if ( is_array( $str ) ) {
                        return '';
                }

                $filtered = (string) $str;
                $filtered = strip_tags( $filtered );
                $filtered = preg_replace( '/[\r\t\0\x0B]+/', ' ', $filtered );

                return trim( $filtered );
        }
}

if ( ! function_exists( 'sanitize_hex_color' ) ) {
        function sanitize_hex_color( $color ) {
                $color = trim( (string) $color );

                if ( '' === $color ) {
                        return '';
                }

                if ( '#' !== substr( $color, 0, 1 ) ) {
                        $color = '#' . $color;
                }

                if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color ) ) {
                        return '#' . strtolower( ltrim( $color, '#' ) );
                }

                return '';
        }
}

if ( ! function_exists( 'absint' ) ) {
        function absint( $value ) {
                return abs( (int) $value );
        }
}

if ( ! function_exists( 'add_action' ) ) {
        function add_action( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'add_filter' ) ) {
        function add_filter( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'apply_filters' ) ) {
        function apply_filters( $tag, $value ) {
                return $value;
        }
}

if ( ! function_exists( 'register_setting' ) ) {
        function register_setting( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'wp_list_pluck' ) ) {
        function wp_list_pluck( $list, $field ) {
                $values = [];

                if ( ! is_array( $list ) ) {
                        return $values;
                }

                foreach ( $list as $item ) {
                        if ( is_array( $item ) && isset( $item[ $field ] ) ) {
                                $values[] = $item[ $field ];
                        } elseif ( is_object( $item ) && isset( $item->$field ) ) {
                                $values[] = $item->$field;
                        }
                }

                return $values;
        }
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
        function wp_create_nonce( $action ) {
                return 'nonce';
        }
}

if ( ! function_exists( 'admin_url' ) ) {
        function admin_url( $path = '' ) {
                return $path;
        }
}

if ( ! function_exists( 'get_option' ) ) {
        function get_option( $name, $default = false ) {
                global $aimentor_test_options;

                if ( array_key_exists( $name, $aimentor_test_options ) ) {
                        return $aimentor_test_options[ $name ];
                }

                return $default;
        }
}

if ( ! function_exists( 'update_option' ) ) {
        function update_option( $name, $value ) {
                global $aimentor_test_options;
                global $aimentor_test_option_sources;

                $aimentor_test_options[ $name ] = $value;

                $aimentor_test_option_sources[ $name ] = 'updated';

                return true;
        }
}

if ( ! function_exists( 'add_option' ) ) {
        function add_option( $name, $value ) {
                global $aimentor_test_options;
                global $aimentor_test_option_sources;

                if ( ! array_key_exists( $name, $aimentor_test_options ) ) {
                        $aimentor_test_options[ $name ] = $value;
                        $aimentor_test_option_sources[ $name ] = 'default';
                }

                return true;
        }
}

if ( ! function_exists( 'delete_option' ) ) {
        function delete_option( $name ) {
                global $aimentor_test_options;
                global $aimentor_test_option_sources;

                unset( $aimentor_test_options[ $name ] );
                unset( $aimentor_test_option_sources[ $name ] );

                return true;
        }
}

if ( ! function_exists( 'wp_json_encode' ) ) {
        function wp_json_encode( $data ) {
                        return json_encode( $data );
        }
}

if ( ! function_exists( 'wp_unslash' ) ) {
        function wp_unslash( $value ) {
                return $value;
        }
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
        function wp_verify_nonce( $nonce, $action ) {
                return true;
        }
}

if ( ! function_exists( 'current_time' ) ) {
        function current_time( $type ) {
                return time();
        }
}

if ( ! function_exists( 'human_time_diff' ) ) {
        function human_time_diff( $from, $to ) {
                $diff = max( 0, $to - $from );

                return $diff . ' seconds';
        }
}

if ( ! function_exists( 'wp_send_json_error' ) ) {
        function wp_send_json_error( $data, $status = 400 ) {
                throw new RuntimeException( 'wp_send_json_error: ' . $status );
        }
}

if ( ! function_exists( 'wp_send_json_success' ) ) {
        function wp_send_json_success( $data ) {
                throw new RuntimeException( 'wp_send_json_success' );
        }
}

if ( ! function_exists( 'plugin_dir_path' ) ) {
        function plugin_dir_path( $file ) {
                return dirname( $file ) . '/';
        }
}

if ( ! function_exists( 'wp_mkdir_p' ) ) {
        function wp_mkdir_p( $dir ) {
                return true;
        }
}

if ( ! function_exists( 'checked' ) ) {
        function checked( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'selected' ) ) {
        function selected( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'submit_button' ) ) {
        function submit_button( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
        function wp_enqueue_script( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'wp_localize_script' ) ) {
        function wp_localize_script( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'wp_add_inline_script' ) ) {
        function wp_add_inline_script( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'get_current_screen' ) ) {
        function get_current_screen() {
                return null;
        }
}

if ( ! function_exists( 'get_current_user_id' ) ) {
        function get_current_user_id() {
                return 1;
        }
}

if ( ! function_exists( 'is_wp_error' ) ) {
        function is_wp_error( $thing ) {
                return false;
        }
}

if ( ! function_exists( 'wp_normalize_path' ) ) {
        function wp_normalize_path( $path ) {
                return $path;
        }
}

if ( ! interface_exists( 'AiMentor_Provider_Interface' ) ) {
        interface AiMentor_Provider_Interface {
                public function request( $prompt, $args = [] );
        }
}
