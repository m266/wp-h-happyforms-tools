<?php
/*
Plugin Name:       WP H-Happyforms Tools
Plugin URI:        https://github.com/m266/wp-h-happyforms-tools
Description:       Tools für das Plugin "Happyforms"
Author:            Hans M. Herbrand
Author URI:        https://herbrand.org
Version:           2.3
Date:              2024-01-29
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

// Plugin Happyforms oder Happyforms-Upgrade (Premium-Version) aktiv?
if (is_plugin_active('happyforms/happyforms.php') || (is_plugin_active('happyforms-upgrade/happyforms-upgrade.php'))) {
// E-Mail an Admin senden, wenn aktiv
    register_activation_hook(__FILE__, 'wphhft_activate'); // Funktions-Name anpassen
    function wphhft_activate()
    { // Funktions-Name anpassen
        $subject = 'Plugin "WP H-Happyforms Tools: Entwicklung eingestellt!"'; // Plugin-Name anpassen
        $message = 'Bitte nach dieser Anleitung vorgehen:
https://herbrand.org/wordpress/eigene-plugins/wp-h-happyforms-tools/';
        wp_mail(get_option("admin_email"), $subject, $message);
    }
//////////////////////////////////////////////////////////////////////////////////////////
/* Erlaubt Links in Mehrfachauswahl-Feldern von Happyforms
Credits/Special thanks: Ignazio Setti https://thethemefoundry.com/
*/
add_shortcode( 'happyforms_link', function( $atts, $content = '' ) {
    $atts = shortcode_atts( array( 'href' => '#' ), $atts );
    $atts['href'] = str_replace( '&quot;', '', $atts['href'] );
    $link = "<a href=\"{$atts['href']}\" target=\"_blank\">{$content}</a>";

    return $link;
}, 10, 2 );

add_action( 'happyforms_part_before', function( $part ) {
    if ( 'checkbox' !== $part['type'] ) {
        return;
    }

    ob_start();
} );

add_action( 'happyforms_part_after', function( $part ) {
    if ( 'checkbox' !== $part['type'] ) {
        return;
    }

    echo do_shortcode( ob_get_clean() );
} );

//////////////////////////////////////////////////////////////////////////////////////////
// Verbesserung Bestätigungs-E-Mail (Block der Zustimmung wird ausgeblendet)
// Der Inhalt der Variable "$label" muss exakt dem Text im Formular entsprechen; bei Bedarf in Zeile 70 anpassen.
add_filter( 'happyforms_email_part_visible', function( $visible, $part, $form ) {
    $label = 'Das Formular kann nur mit der Zustimmung zur Datenschutzerklärung gesendet werden*';
    if ( isset( $part['label'] ) && $label === $part['label'] ) {
        $visible = false;
    }

    return $visible;
}, 10, 3 );

//////////////////////////////////////////////////////////////////////////////////////////
/*
Feld "Nachricht" wird mit Kommentar-Blacklist abgeglichen
Credits/Special thanks: Ignazio Setti https://thethemefoundry.com/
*/
add_filter( 'happyforms_validate_submission', function( $is_valid, $request, $form ) {
    $mod_keys = trim( get_option( 'disallowed_keys' ) );

    if ( '' === $mod_keys ) {
        return $is_valid;
    }

    foreach( $form['parts'] as $part ) {
        if ( $part['type'] === 'multi_line_text' ) {
            $part_name = happyforms_get_part_name( $part, $form );
            $part_value = $request[$part_name];

            foreach ( explode( "\n", $mod_keys ) as $word ) {
                $word = trim( $word );
                $length = strlen( $word );

                if ( $length < 2 or 256 < $length ) {
                    continue;
                }

                $pattern = sprintf( '#%s#i', preg_quote( $word, '#' ) );

                if ( preg_match( $pattern, $part_value ) ) {
                    $is_valid = false;
                }
            }
        }
    }

    return $is_valid;
}, 10, 3 );
}
?>