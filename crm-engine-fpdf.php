<?php
/*
	Plugin Name: CRM Engine FPDF
	Plugin URI: https://www.crmengine.co.uk
	Description: CRM Engine FPDF 
	Author: Business Equip
	Author URI: http://www.businessequip.co.uk

	Version: 1.0.0

	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
*/

    add_action( 'wp_enqueue_scripts', 'crme_fpdf_stylesheet' );

    /**
     * Add stylesheet to the page
     */
    function crme_fpdf_stylesheet() {
        wp_enqueue_style( 'crme-fpdf-style', plugins_url('style.css', __FILE__) );
    }


/**
 * Initialize the blocks
 */
function crme_fpdf_database_loader() {

include_once plugin_dir_path( __FILE__ ) . 'code-pdf.php';
include_once plugin_dir_path( __FILE__ ) . 'code-pdf2.php';
include_once plugin_dir_path( __FILE__ ) . 'code-joininginstructions.php';
include_once plugin_dir_path( __FILE__ ) . 'code-sponsorletter.php';
}
add_action( 'plugins_loaded', 'crme_fpdf_database_loader' );


/**
 * Initialize the database on Activate
 */

register_activation_hook( __FILE__, 'crm_engine_fpdf_setup');
 
function crm_engine_fpdf_setup() {
Global $wpdb;


}


?>