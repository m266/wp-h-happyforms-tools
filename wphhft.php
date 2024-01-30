<?php
/*
Plugin Name:       WP H-Happyforms Tools
Plugin URI:        https://github.com/m266/wp-h-happyforms-tools
Description:       Tools für das Plugin "Happyforms"
Author:            Hans M. Herbrand
Author URI:        https://herbrand.org
Version:           2.3.1
Date:              2024-01-30
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 */
// Externer Zugriff verhindern
defined('ABSPATH') || exit();

//////////////////////////////////////////////////////////////////////////////////////////
// Check Plugin aktiv
if (!function_exists('is_plugin_inactive')) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

// Plugin Happyforms oder Happyforms-Upgrade (Premium-Version) aktiv?
if (is_plugin_active('happyforms/happyforms.php') || (is_plugin_active('happyforms-upgrade/happyforms-upgrade.php'))) {
// Info für Admin
    // E-Mail an Admin senden, wenn inaktiv
    register_activation_hook(__FILE__, 'wphhft_activate'); // Funktions-Name anpassen
    function wphhft_activate()
    { // Funktions-Name anpassen
        $subject = 'Plugin "WP H-Happyforms Tools"'; // Plugin-Name anpassen
        $message = 'Bitte beachten:
Die Entwicklung des Plugins "WP H-Happyforms Tools" wurde eingestellt. Bitte nach dieser Anleitung vorgehen: https://herbrand.org/wordpress/eigene-plugins/wp-h-happyforms-tools!';
        wp_mail(get_option("admin_email"), $subject, $message);
    }
}
//////////////////////////////////////////////////////////////////////////////////////////
?>