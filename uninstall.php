<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('ABSPATH')) {
    exit;
}
// Delete option key
delete_option('truabLicense');