<?php
/**
 * @package Sponsored_Post_Hinweis
 * @version 1.1
 */
/*
Plugin Name: Sponsored Post Hinweis
Plugin URI: http://wordpress.org/plugins/sponsored-post-hinweis/
Description: Dieses PlugIn setzt (default) einfach ein [Sponsored Post] vor alle Artikel. Über ein benutzerdefiniertes Feld mit dem Namen "hinweis" kann als Wert ein beliebiger Text hinterlegt werden. Beim hinterlegten Wert "no" kann der Hinweis für den einzelnen Artikel ausgeblendet werden.
Author: Gernot Gawlik
Version: 1.1
Author URI: http://www.krick-interactive.com
*/

// Die Funktion die den Hinweis vor den Content stellen soll
function contenthinweis($content)
{
	//Variable für Hinweis deklarieren
	$sponsoredhinweis = '[Sponsored Post] ';
	//Absatzkorrektur
	$search = "<p>";

	if ( $keys = get_post_custom_keys() ) {
		foreach ( $keys as $key ) {
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');

			if ( $key == 'hinweis' ) {
				//wenn $value = no, dann kein Hinweis
				if ( $value == 'no'){
					$sponsoredhinweis = "";
				}else{
					$sponsoredhinweis = "$value";
				}
			}
		}
	}

	if (!is_page())
	// Sponsored Hinweis davor setzen
	$content = trim(sprintf('%s%s',$sponsoredhinweis, $content));
	// Absätze korrigieren
	$content = "<p>".preg_replace('/'.$search.'/', "", $content, 1);

	return $content;
}


// Die Funktion zum Bearbeiten des Inhalts als Content-Filter registrieren
add_filter(
	'the_content', //Name des Hooks
	'contenthinweis' //Name der Funktion
);
?>