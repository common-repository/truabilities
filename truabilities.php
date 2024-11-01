<?php
/**
 * Plugin Name: TruAbilities Compliance Connector
 * Plugin URI:  https://truabilities.com
 * Description: TruAbilities - Compliance Solution For Your Business. TruAbilities LLC facilitates web accessibility with its next-generation technology, an easy-to-install widget that doesn't use cookies or slow load times. We facilitate accessibility between website owners and the visitors who need support in accessing content. TruAbilities Compliance Solutions offers a ready-to-use widget that is easily added to any website with no impact on load times. The TruAbilities widget can also support website owners in maintaining compliance with data protection regulations such as Europe's GDPR and California's CCPA.
 * Version:     1.0.1
 * Author:      Team TruAbilities
 * License:     GPL v3
 * License URI: https://truabilities.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Path

define('TRUAB_VERSION', '1.0.1');
define('TRUAB_FILE', __FILE__);
define('TRUAB_PATH', plugin_dir_path(TRUAB_FILE));
define('TRUAB_URL', plugin_dir_url(TRUAB_FILE));

add_action('plugins_loaded', 'truab_load_plugin_textdomain');

// Check PHP Version

if (!version_compare(PHP_VERSION, '5.4', '>=')) {
    add_action('admin_notices', 'truab_fail_php_version');
} elseif (!version_compare(get_bloginfo('version'), '4.5', '>=')) {
    add_action('admin_notices', 'truab_fail_wp_version');
} else {
    include TRUAB_PATH . 'includes/class-truab-plugin.php';
}

// Check WP Plugin Slug

function truab_load_plugin_textdomain() {

    load_plugin_textdomain('truab-compliance');

}

// Add Message

function truab_fail_php_version() {

    $message = sprintf(esc_html__('TruAbilities requires PHP version %s+, plugin is currently NOT ACTIVE.', 'truab-compliance'), '5.4');
    $message = sprintf('<div class="error">%s</div>', wpautop($message));

    echo wp_kses_post($message);

}

function truab_fail_wp_version() {

    $message = sprintf(esc_html__('TruAbilities requires WordPress version %s+, plugin is currently NOT ACTIVE.', 'embed-code'), '4.5');
    $message = sprintf('<div class="error">%s</div>', wpautop($message));

    echo wp_kses_post($message);

}

