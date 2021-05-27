<?php
/*
Plugin Name:       WP H-HappyForms Tools
Plugin URI:        https://github.com/m266/wp-h-happyforms-tools
Description:       Tools für das Plugin "HappyForms"
Author:            Hans M. Herbrand
Author URI:        https://herbrand.org
Version:           1.6
Date:              2021-05-27
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/m266/wp-h-happyforms-tools
 */

// Externer Zugriff verhindern
defined('ABSPATH') || exit();

//////////////////////////////////////////////////////////////////////////////////////////
// Check Plugin aktiv
if (!function_exists('is_plugin_inactive')) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

// Plugin HappyForms aktiv?
if (is_plugin_active('happyforms/happyforms.php')) {
// Erinnerung an Git Updater
// E-Mail an Admin senden, wenn inaktiv
    register_activation_hook(__FILE__, 'wphhft_activate'); // Funktions-Name anpassen
    function wphhft_activate()
    { // Funktions-Name anpassen
        $subject = 'Plugin "WP H-HappyForms Tools"'; // Plugin-Name anpassen
        $message = 'Falls nicht vorhanden:
Bitte das Plugin "Git Updater" hier https://herbrand.org/tutorials/github/git-updater/ herunterladen, installieren und aktivieren, um weiterhin Updates zu erhalten!';
        wp_mail(get_option("admin_email"), $subject, $message);
    }

//////////////////////////////////////////////////////////////////////////////////////////
// Erlaubt HTML-Code für HappyForms in Mehrfachauswahl-Feldern
// Ersetzt String in der Datei frontend-checkbox.php Zeile 34 (Plugin HappyForms)
    $wphhft_string_orig = "<?php echo esc_attr( \$option['label'] ); ?>";
    $wphhft_string_new = "<?php echo html_entity_decode( \$option['label'] ); ?>";
    $wphhft_path_to_file = ABSPATH . 'wp-content/plugins/happyforms/core/templates/parts/frontend-checkbox.php';
    $wphhft_file_contents = file_get_contents($wphhft_path_to_file); // Inhalt frontend-checkbox.php einlesen
if(strpos($wphhft_file_contents, $wphhft_string_orig) !== false) { //  Original-String vorhanden?
    $wphhft_file_contents = str_replace($wphhft_string_orig, $wphhft_string_new, $wphhft_file_contents);
    file_put_contents($wphhft_path_to_file, $wphhft_file_contents); // String ersetzen
    add_filter('happyforms_part_frontend_template_path_checkbox', function ($wphhft_template) {
        $wphhft_template = ABSPATH . 'wp-content/plugins/happyforms/core/templates/parts/frontend-checkbox.php';
        return $wphhft_template;
    });
}

//////////////////////////////////////////////////////////////////////////////////////////
// Verbesserung Bestätigungs-E-Mail (Block der Zustimmung wird ausgeblendet)
// Der Inhalt der Variable "$label" muss exakt dem Text im Formular entsprechen; bei Bedarf in Zeile 56 anpassen.
add_filter( 'happyforms_email_part_visible', function( $visible, $part, $form ) {
    $label = 'Das Formular kann nur mit der Zustimmung zur Datenschutzerklärung gesendet werden*';
    if ( isset( $part['label'] ) && $label === $part['label'] ) {
        $visible = false;
    }

    return $visible;
}, 10, 3 );
}
?>
