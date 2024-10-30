<?php

/**
 * Plugin Name: mPandco
 * Tags: Woocommerce, Payment, Gateway, mPandco, Venezuela
 * Plugin URI: https://www.mpandco.com
 * Description: Pasarela de pago mPandco compatible con woocommerce.
 * Version: 1.0.11
 * Author: Joyacorp
 * Author URI: https://joyacorp.com
 * Text Domain: mpandco
 * Domain Path: /languages/
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright: (c) 2019 mPandco.
 * Requires PHP: 5.6
 * Requires at least: 4.8.1
 * Stable tag: 5.1.1
 * Tested up to: 5.2
 * WC requires at least: 3.5.0
 * WC tested up to: 3.6.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', 'mpandco_admin_notice_woocommerce_not_found');
    return;
}

/**
 *
 */
function mpandco_admin_notice_woocommerce_not_found() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php __( 'mPandco se ha desactivado, requiere de woocommerce por favor instale woocommerce para poder usar mPandco', 'sample-text-domain' ); ?></p>
    </div>
    <?php
    deactivate_plugins(plugin_basename(__FILE__));
}

/**
 * @param $gateways
 * @return array
 */
function mpandco_add_gateway_class($gateways ) {
    $gateways[] = 'mpandco_gateway';
    return $gateways;
}

/**
 *
 */
function mpandco_init(){
    load_plugin_textdomain( 'mpandco', false, basename( dirname( __FILE__ ) ) . '/languages' );
    if (! class_exists('mpandco_gateway')){
        include_once dirname(__FILE__) . '/includes/mpandco_gateway_request.php';
        include_once dirname(__FILE__) . '/includes/mpandco_gateway_execute.php';
        include_once dirname(__FILE__) . '/includes/mpandco_gateway.php';
    }
}

/**
 * @param $links
 * @return array
 */
function mpandco_action_links($links ) {
    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=wc-settings&tab=checkout&section=mpandco_gateway' ) ) . '">' . __( 'configuración', 'mpandco' ) . '</a>'
    ), $links );
    return $links;
}

require_once __DIR__.'/vendor/autoload.php';

add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mpandco_action_links' );
add_filter('woocommerce_payment_gateways','mpandco_add_gateway_class');
add_action('plugins_loaded','mpandco_init');