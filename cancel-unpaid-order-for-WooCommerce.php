
<?php
/**
 * cancel unpaid order for WooCommerce           
 *
 * @copyright Copyright (C) 2021-2022, engsalah.com - salah@engsalah.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: cancel unpaid order for WooCommerce
 * Version:     5.8
 * Plugin URI:  https://engsalah.com/
 * Description: Cancel all unpaid orders after held duration to prevent stock lock for those products.
 * Author:      salah elhamouly
 * Author URI:  https://engsalah.com
 * Text Domain: cancel unpaid order for WooCommerce
 * Domain Path: /languages/
 * License:     GPL v3
 * Requires at least: 5.5
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



if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}




 function autocancel_wc_orders(){
	 $statusData =array();
	 if(get_option ( 'woo-cancelorder-text1' ) !=""){
		$statusData[]= get_option ( 'woo-cancelorder-text1' );
	 }
	 if(get_option ( 'woo-cancelorder-text2' ) !=""){
		$statusData[]= get_option ( 'woo-cancelorder-text2' );
	 }
	 if(get_option ( 'woo-cancelorder-text3' ) !=""){
		$statusData[] = get_option ( 'woo-cancelorder-text3' );
	 }
	 if(get_option ( 'woo-cancelorder-text4' ) !=""){	 
		$statusData[] = get_option ( 'woo-cancelorder-text4' );
	 }
	 
	$query = ( array(
		'limit'   => 5,
		'orderby' => 'date',
		'order'   => 'DESC',
		'status'  => $statusData
	) );
	
	$durationNumber =1;
	$dateStyle = "h";
	$orders = wc_get_orders( $query );
	foreach( $orders as $order ){		

		$date     = new DateTime( $order->get_date_created() );
		$today    = new DateTime();
		$interval = $date->diff($today);
	
	    if(get_option ( 'woo-cancelorder-secondtext1' ) !=""){	 
			$dateStyle = get_option ( 'woo-cancelorder-secondtext1' );
		}
		
		$datediff = $interval->format('%'.$dateStyle);

		
		if(get_option ( 'woo-cancelorder-thirdtext1' ) !=""){	 
			$durationNumber = get_option ( 'woo-cancelorder-thirdtext1' );
		}
	   
		if( $datediff >= $durationNumber ){
			$order->update_status('cancelled', 'Cancelled for missing payment');
		}
		
		
	}

}

add_action( 'admin_init', 'autocancel_wc_orders' );



/**
 * Adding Submenu under Settings Tab
 *
 * @since 1.0
 */
function cancelorder_add_menu() {
	add_submenu_page ( "options-general.php", "woocommerce Cancel Order", "woocommerce Cancel Order", "manage_options", "woo-cancelorder", "woo_cancelorder_page" );
}
add_action ( "admin_menu", "cancelorder_add_menu" );
 
/**
 * Setting Page Options
 * - add setting page
 * - save setting page
 *
 * @since 1.0
 */
function woo_cancelorder_page() {
	?>
<div class="wrap">
	<h1> Woocommerce Cancel Order Plugin </h1>
 
	<form method="post" action="options.php">
     <?php
	settings_fields ( "woo_cancelorder_config" );
	do_settings_sections ( "woo-cancelorder" );
	submit_button ();
	?>
         </form>
</div>
 
<?php
}
 
/**
 * Init setting section, Init setting field and register settings page
 *
 * @since 1.0
 */
function woo_cancelorder_settings() {
	add_settings_section ( "woo_cancelorder_config", "", null, "woo-cancelorder" );
	
	
	add_settings_field ( "woo-cancelorder-text1", "add woocommerce order status slugs that you want be canceled",
	
	 "woo_cancelorder_options", "woo-cancelorder", "woo_cancelorder_config" );
	add_settings_field ( "woo-cancelorder-text2", "", "", "woo-cancelorder", "woo_cancelorder_config" );
	
	add_settings_field ( "woo-cancelorder-text3", "", "", "woo-cancelorder", "woo_cancelorder_config" );
	add_settings_field ( "woo-cancelorder-text4", "", "", "woo-cancelorder", "woo_cancelorder_config" );
	
	
	register_setting ( "woo_cancelorder_config", "woo-cancelorder-text1" );
	register_setting ( "woo_cancelorder_config", "woo-cancelorder-text2" );
	register_setting ( "woo_cancelorder_config", "woo-cancelorder-text3" );
	register_setting ( "woo_cancelorder_config", "woo-cancelorder-text4" );
	
	
	
	add_settings_field ( "woo-cancelorder-secondtext1", "Add duration of time write y for years or m for months or d for days or h for hours or i for minute or s for secound ", "woo_cancelorder_options_second", "woo-cancelorder", "woo_cancelorder_config" );
	register_setting ( "woo_cancelorder_config", "woo-cancelorder-secondtext1" );
	
	
	
	add_settings_field ( "woo-cancelorder-thirdtext1", "write the number of minutes or days months or years or secound ", "woo_cancelorder_options_third", "woo-cancelorder", "woo_cancelorder_config" );
	register_setting ( "woo_cancelorder_config", "woo-cancelorder-thirdtext1" );
	
	
}
add_action ( "admin_init", "woo_cancelorder_settings" );
 
/**
 * Add simple textfield value to setting page
 *
 * @since 1.0
 */
function woo_cancelorder_options() {
    
// find the 

	?>
	<style>
	.mbottom{
	    margin-bottom:10px;
	}
	.form-table th {
       
        width: 119px !important;
        
    }
	</style>
	
<div class="postbox" style="width: 95%; padding: 30px;">
	<input type="text" class="mbottom" name="woo-cancelorder-text1"
		value="<?php
	echo stripslashes_deep ( esc_attr ( get_option ( 'woo-cancelorder-text1' ) ) );
	?>"/>
	
	<input type="text" class="mbottom" name="woo-cancelorder-text2"
		value="<?php
	echo stripslashes_deep ( esc_attr ( get_option ( 'woo-cancelorder-text2' ) ) );
	?>"/>

	<input type="text" name="woo-cancelorder-text3"
		value="<?php
	echo stripslashes_deep ( esc_attr ( get_option ( 'woo-cancelorder-text3' ) ) );
	?>"/>
	
	<input type="text" name="woo-cancelorder-text4"
		value="<?php
	echo stripslashes_deep ( esc_attr ( get_option ( 'woo-cancelorder-text4' ) ) );
	?>"/>

	
	</div>
<?php
}


/**
 * Add simple textfield value to setting page
 *
 * @since 1.0
 */
function woo_cancelorder_options_second() {
	?>
		<style>
	.mbottom{
	    margin-bottom:10px;
	}
	.form-table th {
       
        width: 119px !important;
        
    }
	</style>
<div class="postbox" style="width: 65%; padding: 30px;">
	<input type="text" name="woo-cancelorder-secondtext1"
		value="<?php
	echo stripslashes_deep ( esc_attr ( get_option ( 'woo-cancelorder-secondtext1' ) ) );
	?>"/>
	
	
	</div>
<?php
}



/**
 * Add simple textfield value to setting page
 *
 * @since 1.0
 */
function woo_cancelorder_options_third() {
	?>
		<style>
	.mbottom{
	    margin-bottom:10px;
	}
	.form-table th {
       
        width: 119px !important;
        
    }
	</style>
<div class="postbox" style="width: 65%; padding: 30px;">
	<input type="number" name="woo-cancelorder-thirdtext1"
		value="<?php
	echo stripslashes_deep ( esc_attr ( get_option ( 'woo-cancelorder-thirdtext1' ) ) );
	?>"/>
	
	
	</div>
<?php
}



