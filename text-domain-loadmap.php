<?php

/*
Plugin Name: Display text domains
Plugin URI: http://www.papik-wordpress.eu/display-text-domains/
Description: Simple loading text domains loading mapper. Shows files Wordpress is trying to lad, says, which actualy of them do exist, which not, show premissions and alert on inaccessible files and count loaded empty translations.
Author: Pavel Riha
Version: 1.1.1
Author URI: http://www.papik-wordpress.cz

*/
if(!defined('ABSPATH')) die('Direct access not allowed!');
// start logging textdomains
if(is_admin()) { add_action( 'load_textdomain', 'cc_log_domains',1,2 ); }
function cc_log_domains(  $domain, $mofile ){
    global $domains;
    if(!isset($domains)) $domains = array();
    if(!isset($domains[$domain])) {$domains[$domain] = array($mofile);}
    else { $domains[$domain][] = $mofile;}
}

function cc_display_domains(){
    global $domains, $wp_version, $l10n;
	$lang_dir = defined('WP_LANG_DIR') ? WP_LANG_DIR : ( defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR.'/languages':  $_SERVER["DOCUMENT_ROOT"].'/wp-content/languages' );
	$locale = $wp_version >= 4 ? get_locale() : WP_LANG;
	
    echo "<div class=\"wrap\">";
	
    ?><h2><?php _e('Display text domains','cc_domain'); ?></h2><?php 
    foreach($domains as $domain => $mofiles){
    echo "<h3>".sprintf(__('Textdomain: <em>%s</em>:','cc_domain'),$domain)."</h3>\n";

        foreach($mofiles as $mofile)
        {
            if(!file_exists($mofile)) { echo "<span style=\"color: red\">".sprintf(__('%s  <em>(FILE NOT FOUND)</em>','cc_domain'),$mofile)."</span><br />\n";}
            else { echo "<span style=\"color: green\">".$mofile." (".substr(sprintf('%o', fileperms($mofile)), -4).")</span><br />\n";}
			$file = $lang_dir.'/plugins/'.$domain.'-'.$locale.'.mo';
			
			
		}
		if (file_exists($file) && !in_array($file,$mofiles)) {
				echo '<span style="color: orange">'.sprintf(__('%s <em>(File NOT loaded!)</em>','cc_domain'),$file)."</span><br/ >\n";
		}
		/*if (isset($l10n[$domain])) {
			$total = count($l10n[$domain]);
			echo $total." ";
			$empty = cc_get_empty_strings_count($l10n[$domain]);
				echo "<strong>".sprintf(__('Loaded %d&#37;, empty %d strings','cc_domain'), (int) (($total - $empty)/$total * 100), $empty)."</strong><br />\n";
			}
			else echo "<strong>".__('Loaded 0%.','cc_domain')."</strong><br />\n";
        echo "<br />\n";*/
    }
    echo "</div>";
}

add_action('admin_menu','cc_display_domains_page');
function cc_display_domains_page(){
    add_management_page(__('Display text domains','cc_domain'), __('Display text domains','cc_domain'), 'manage_options', 'cc-display-domains', 'cc_display_domains');
}

add_action('admin_init','cc_load_text_domain');

function cc_load_text_domain(){
 load_plugin_textdomain( 'cc_domain', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

function cc_get_empty_strings_count($object){
$count = 0;

	 foreach($object as $item){
	  if(empty($item)) $count++;
	  // echo "<pre>".print_r($item,true)."</pre>";
	 }
 
 return $count;
}
