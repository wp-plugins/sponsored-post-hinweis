<?php
/**
 * @package Sponsored_Post_Hinweis
 * @version 1.6
 */
/*
Plugin Name: Sponsored Post Hinweis
Plugin URI: http://wordpress.org/plugins/sponsored-post-hinweis/
Description: Dieses PlugIn setzt (default) einfach ein [Sponsored Post] vor alle Artikel. Über ein benutzerdefiniertes Feld mit dem Namen "hinweis" kann als Wert ein beliebiger Text hinterlegt werden. Beim hinterlegten Wert "no" kann der Hinweis für den einzelnen Artikel ausgeblendet werden.
Author: Gernot Gawlik
Version: 1.6
Author URI: http://www.krick-interactive.com
*/

// Die Funktion die den Hinweis vor den Content stellen soll
function contenthinweis($content)
{
$option_string = get_option('sponsoredFunktionen');
$option = array();
$option = json_decode($option_string, true);

//(Standard) Variablen deklarieren
$sponsoredhinweistext = 'Sponsored Post';
$praefixtext ="[";
$suffixtext ="]";
$leerzeichen = " ";


	//Variable für Hinweis deklarieren
	if (isset($option['standardtext'])){
		$sponsoredhinweistext = $option['standardtext'];
	}else{
		$sponsoredhinweistext = 'Sponsored Post';
	}

	//Präfix
	if (isset($option['praefixtext'])){ 
		$praefixtext = $option['praefixtext'];
	}

	//Suffix
	if (isset($option['suffixtext'])){ 
		$suffixtext = $option['suffixtext'];
	}

//Komplett
$sponsoredhinweis = $praefixtext.$sponsoredhinweistext.$suffixtext.$leerzeichen;
	
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
					$sponsoredhinweis = $value." ";
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

<?php
add_action('admin_menu', 'sponsoredFunktionen_menu');
function sponsoredFunktionen_menu() {
	add_options_page('Sponsored Post Hinweis Optionen', 'Sponsored Post Hinweis Optionen', 'manage_options', 'sponsoredFunktionen', 'sponsoredFunktionen_options');
}

function sponsoredFunktionen_options () {
	$option_name = 'sponsoredFunktionen';
	if (!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	if( isset($_POST['standardtext'])) {
		$option = array();
		$option['standardtext'] = esc_html($_POST['standardtext']);
		$option['praefixtext'] = esc_html($_POST['praefixtext']);
		$option['suffixtext'] = esc_html($_POST['suffixtext']);
		update_option($option_name, json_encode($option));
		$outputa .= '<div class="updated"><p><strong>'.__('Einstellungen gespeichert.', 'menu' ).'</strong></p></div>';
	}
	$option = array();
	$option_string = get_option($option_name);
	if ($option_string===false) {
		$option = array();
		$option['standardtext'] = array('standardtext'=>true);
		$option['praefixtext'] = array('praefixtext'=>true);
		$option['suffixtext'] = array('suffixtext'=>true);
		$option_string = get_option($option_name);
	}
	$option = json_decode($option_string, true);
	$outputa .= '
	<div class="wrap">
		<h2>'.__( 'Sponsored Post Hinweis Optionen', 'menu' ).'</h2>
		<form name="form1" method="post" action="">
		<table>
		<tr><td valign="top"><b>'.__("Standardtext", 'menu' ).':</b></td>
		<td style="padding-bottom:20px;">
		<input type="text" name="standardtext" value="'.stripslashes($option['standardtext']).'" size="100"><br />
		<span class="description">z.B.: '.htmlentities('Sponsored Post').'</span>
		</td></tr>
		<tr><td valign="top"><b>'.__("Praefix", 'menu' ).':</b></td>
		<td style="padding-bottom:20px;">
		<input type="text" name="praefixtext" value="'.stripslashes($option['praefixtext']).'" size="100"><br />
		<span class="description">z.B.: '.htmlentities('[').'</span>
		</td></tr>
		<tr><td valign="top"><b>'.__("Suffix", 'menu' ).':</b></td>
		<td style="padding-bottom:20px;">
		<input type="text" name="suffixtext" value="'.stripslashes($option['suffixtext']).'" size="100"><br />
		<span class="description">z.B.: '.htmlentities(']').'</span>
		</td></tr>
		</table>
		<hr />
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="'.esc_attr('Speichern').'" />
		</p>
		</form>
	</div>';
	echo $outputa; 
}
?>