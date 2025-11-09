<?php
// Minimal WordPress stubs for standalone option sanitization tests.

global $aimentor_test_options;
global $aimentor_test_option_sources;
global $aimentor_test_capabilities;
global $aimentor_test_nonce_results;
$aimentor_test_options         = [];
$aimentor_test_option_sources  = [];
$aimentor_test_capabilities    = [];
$aimentor_test_nonce_results   = [];

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
        define( 'WP_CONTENT_DIR', sys_get_temp_dir() );
}

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

if ( ! function_exists( 'aimentor_test_reset_capabilities' ) ) {
        function aimentor_test_reset_capabilities() {
                global $aimentor_test_capabilities;

                $aimentor_test_capabilities = [];
        }
}

if ( ! function_exists( 'aimentor_test_set_capability' ) ) {
        function aimentor_test_set_capability( $capability, $allowed ) {
                global $aimentor_test_capabilities;

                $aimentor_test_capabilities[ $capability ] = (bool) $allowed;
        }
}

if ( ! function_exists( 'aimentor_test_reset_nonce_verification' ) ) {
        function aimentor_test_reset_nonce_verification() {
                global $aimentor_test_nonce_results;

                $aimentor_test_nonce_results = [];
        }
}

if ( ! function_exists( 'aimentor_test_set_nonce_verification' ) ) {
        function aimentor_test_set_nonce_verification( $action, $result ) {
                global $aimentor_test_nonce_results;

                $aimentor_test_nonce_results[ $action ] = (bool) $result;
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

if ( ! function_exists( 'wp_kses_post' ) ) {
        function wp_kses_post( $content ) {
                return strip_tags( (string) $content, '<a><b><br><em><i><strong><p><span>' );
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

if ( ! function_exists( 'sanitize_key' ) ) {
        function sanitize_key( $key ) {
                $sanitized = strtolower( (string) $key );

                return preg_replace( '/[^a-z0-9_\-]/', '', $sanitized );
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

if ( ! function_exists( 'current_user_can' ) ) {
        function current_user_can( $capability ) {
                global $aimentor_test_capabilities;

                if ( array_key_exists( $capability, $aimentor_test_capabilities ) ) {
                        return (bool) $aimentor_test_capabilities[ $capability ];
                }

                return true;
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

if ( ! function_exists( 'trailingslashit' ) ) {
        function trailingslashit( $string ) {
                $string = (string) $string;

                return rtrim( $string, "/\\" ) . '/';
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

if ( ! function_exists( 'register_uninstall_hook' ) ) {
        function register_uninstall_hook( $file, $callback ) {
                return null;
        }
}

if ( ! function_exists( 'register_activation_hook' ) ) {
        function register_activation_hook( $file, $callback ) {
                return null;
        }
}

if ( ! function_exists( 'register_deactivation_hook' ) ) {
        function register_deactivation_hook( $file, $callback ) {
                return null;
        }
}

if ( ! function_exists( 'admin_url' ) ) {
        function admin_url( $path = '' ) {
                return $path;
        }
}

if ( ! function_exists( 'plugin_basename' ) ) {
        function plugin_basename( $file ) {
                return basename( $file );
        }
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
        function plugin_dir_url( $file ) {
                $dir = dirname( $file );
                $basename = trim( basename( $dir ), '/' );

                if ( '' === $basename ) {
                        return 'https://example.com/';
                }

                return 'https://example.com/' . $basename . '/';
        }
}

if ( ! function_exists( 'load_plugin_textdomain' ) ) {
        function load_plugin_textdomain( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'rest_url' ) ) {
        function rest_url( $path = '', $scheme = 'rest' ) {
                $base = 'https://example.com/wp-json/';
                $trimmed = ltrim( (string) $path, '/' );

                return $base . $trimmed;
        }
}

if ( ! function_exists( 'add_query_arg' ) ) {
        function add_query_arg( $key, $value = null, $url = '' ) {
                if ( is_array( $key ) ) {
                        $params = $key;
                        $url    = (string) $value;
                } else {
                        $params = [ $key => $value ];
                        $url    = (string) $url;
                }

                $parts = parse_url( $url );
                $query = [];

                if ( isset( $parts['query'] ) ) {
                        parse_str( $parts['query'], $query );
                }

                foreach ( $params as $param_key => $param_value ) {
                        $query[ $param_key ] = $param_value;
                }

                $scheme   = isset( $parts['scheme'] ) ? $parts['scheme'] . '://' : '';
                $host     = $parts['host'] ?? '';
                $port     = isset( $parts['port'] ) ? ':' . $parts['port'] : '';
                $user     = $parts['user'] ?? '';
                $pass     = isset( $parts['pass'] ) ? ':' . $parts['pass'] : '';
                $pass     = ( $user || $pass ) ? $pass . '@' : '';
                $path     = $parts['path'] ?? '';
                $fragment = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';
                $querystr = http_build_query( $query );

                $authority = $scheme . $user . $pass . $host . $port;

                if ( '' === $authority && '' === $path ) {
                        $path = $url;
                }

                $result = $authority . $path;

                if ( '' !== $querystr ) {
                        $result .= '?' . $querystr;
                }

                return $result . $fragment;
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

if ( ! function_exists( 'number_format_i18n' ) ) {
        function number_format_i18n( $number, $decimals = 0 ) {
                return number_format( (float) $number, $decimals );
        }
}

if ( ! function_exists( 'wp_trim_words' ) ) {
        function wp_trim_words( $text, $num_words = 55, $more = '…' ) {
                $text = trim( preg_replace( '/\s+/', ' ', (string) $text ) );

                if ( '' === $text ) {
                        return '';
                }

                $words = explode( ' ', $text );

                if ( count( $words ) <= $num_words ) {
                        return implode( ' ', $words );
                }

                $excerpt = array_slice( $words, 0, $num_words );

                return implode( ' ', $excerpt ) . $more;
        }
}

if ( ! function_exists( 'wp_unslash' ) ) {
        function wp_unslash( $value ) {
                return $value;
        }
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
        function wp_verify_nonce( $nonce, $action ) {
                global $aimentor_test_nonce_results;

                if ( isset( $aimentor_test_nonce_results[ $action ] ) ) {
                        return $aimentor_test_nonce_results[ $action ] ? 1 : false;
                }

                return true;
        }
}

if ( ! function_exists( 'current_time' ) ) {
        function current_time( $type ) {
                return time();
        }
}

if ( ! function_exists( 'date_i18n' ) ) {
        function date_i18n( $format, $timestamp ) {
                return date( $format, $timestamp );
        }
}

if ( ! function_exists( 'wp_date' ) ) {
        function wp_date( $format, $timestamp ) {
                return date( $format, $timestamp );
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

if ( ! function_exists( 'wp_register_style' ) ) {
        function wp_register_style( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'wp_style_add_data' ) ) {
        function wp_style_add_data( ...$args ) {
                return null;
        }
}

if ( ! function_exists( 'wp_register_script' ) ) {
        function wp_register_script( ...$args ) {
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

if ( ! class_exists( 'WP_Error' ) ) {
        class WP_Error {
                protected $errors = [];
                protected $error_data = [];

                public function __construct( $code = '', $message = '', $data = null ) {
                        if ( '' !== $code ) {
                                $this->add( $code, $message, $data );
                        }
                }

                public function add( $code, $message, $data = null ) {
                        $this->errors[ $code ][] = $message;

                        if ( null !== $data ) {
                                $this->error_data[ $code ] = $data;
                        }
                }

                public function get_error_message( $code = '' ) {
                        if ( '' !== $code ) {
                                return $this->errors[ $code ][0] ?? '';
                        }

                        $messages = [];

                        foreach ( $this->errors as $error_messages ) {
                                $messages = array_merge( $messages, $error_messages );
                        }

                        return $messages[0] ?? '';
                }

                public function get_error_data( $code = '' ) {
                        if ( '' !== $code ) {
                                return $this->error_data[ $code ] ?? null;
                        }

                        if ( empty( $this->error_data ) ) {
                                return null;
                        }

                        $first = array_key_first( $this->error_data );

                        return $this->error_data[ $first ];
                }

                public function __isset( $key ) {
                        return isset( $this->errors[ $key ] );
                }
        }
}

