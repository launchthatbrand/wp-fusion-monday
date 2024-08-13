<?php
/*
Plugin Name: SQL Buddy
Plugin URI: https://deliciousbrains.com/sql-buddy
Description: Your one-stop solution for WordPress database management. Edit your table data with a clean and straightforward user interface.
Author: Delicious Brains
Author URI: https://deliciousbrains.com
Version: 1.0.0
Domain Path: /languages
Requires at least: 5.3
Requires PHP: 5.6
Text Domain: sql-buddy

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SQL_BUDDY_VERSION', '1.0.0' );
define( 'SQL_BUDDY_PLUGIN_FILE', __FILE__ );
define( 'SQL_BUDDY_PLUGIN_DIR_PATH', plugin_dir_path( SQL_BUDDY_PLUGIN_FILE ) );
define( 'SQL_BUDDY_PLUGIN_DIR_URL', plugin_dir_url( SQL_BUDDY_PLUGIN_FILE ) );
define( 'SQL_BUDDY_ASSETS_URL', SQL_BUDDY_PLUGIN_DIR_URL . '/assets' );
define( 'SQL_BUDDY_MINIMUM_PHP_VERSION', '7.0' );

if ( file_exists( __DIR__ . '/includes/vendor/autoload.php' ) ) {
	include __DIR__ . '/includes/vendor/autoload.php';
}

// Main plugin initialization happens there so that this file is still parsable in PHP < 7.0.
require __DIR__ . '/includes/namespace.php';
