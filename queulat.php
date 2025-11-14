<?php
/**
 * Plugin Name: Queulat
 * Plugin URI: https://github.com/bloom-ux/queulat
 * Description: Developers toolset for WordPress
 * Version: 0.1.0
 * Author: bloom.lat
 * Author URI: https://www.bloom.lat
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: queulat
 * Domain Path: src/languages
 *
 * @package Queulat
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$queulat_plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

if ( is_readable( $queulat_plugin_dir . 'vendor/autoload.php' ) ) {
	require_once $queulat_plugin_dir . 'vendor/autoload.php';
} elseif ( is_readable( trailingslashit( dirname( $queulat_plugin_dir ) ) . 'queulat/vendor/autoload.php' ) ) {
	require_once trailingslashit( dirname( $queulat_plugin_dir ) ) . 'queulat/vendor/autoload.php';
}

require_once $queulat_plugin_dir . 'src/helpers/class-autoloader.php';
\Queulat\Helpers\Autoloader::boot( 'Queulat\\', __DIR__ . '/src' );

( new Queulat\Bootstrap() )->init();
