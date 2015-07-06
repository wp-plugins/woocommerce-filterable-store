<?php
/**
 * Plugin Name: WooCommerce Filterable Store
 * Plugin URI: http://webcodingplace.com/woocommerce-filterable-store
 * Description: A new style WooCommerce Store with Product Filtering
 * Version: 1.0
 * Author: Rameez
 * Author URI: http://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: filterable-store
 */

/*

  Copyright (C) 2015  Rameez  rameez.iqbal@live.com
*/
require_once('plugin.class.php');

if( class_exists('WCP_Filterable_Store')){
	
	$just_initialize = new WCP_Filterable_Store;
}
?>