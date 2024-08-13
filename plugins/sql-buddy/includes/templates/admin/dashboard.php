<?php
/**
 * Plugin Dashboard.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

// don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<div id="sql-buddy-dashboard">
	<h1 class="loading-message"><?php esc_html_e( 'Please wait...', 'sql-buddy' ); ?></h1>
</div>
