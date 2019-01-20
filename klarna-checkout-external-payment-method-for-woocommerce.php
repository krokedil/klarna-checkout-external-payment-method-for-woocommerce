<?php
/*
Plugin Name: Klarna Checkout (V3) External Payment Method for WooCommerce
Plugin URI: http://krokedil.com
Description: Adds PayPal as an extra payment method in Klarna Checkout iframe. Works with V3 of Klarna Checkout
Version: 1.0
Author: Krokedil
Author URI: http://krokedil.com
*/

/**
 * Extends KCO settings with External Payment Method - PayPal settings.
 */
add_filter( 'kco_wc_gateway_settings', 'kcoepm_form_fields' );
function kcoepm_form_fields( $settings ) {
	$settings['epm_paypal_settings_title'] = array(
		'title' => __( 'External Payment Method - PayPal', 'kco-epm-wc' ),
		'type'  => 'title',
	);
	$settings['epm_paypal_name']           = array(
		'title'       => __( 'Name', 'kco-epm-wc' ),
		'type'        => 'text',
		'description' => __( 'Title for PayPal payment method. This controls the title which the user sees in the checkout form.', 'kco-epm-wc' ),
		'default'     => __( 'PayPal', 'kco-epm-wc' ),
	);
	$settings['epm_paypal_description']    = array(
		'title'       => __( 'Description', 'kco-epm-wc' ),
		'type'        => 'textarea',
		'description' => __( 'Description for PayPal payment method. This controls the description which the user sees in the checkout form.', 'kco-epm-wc' ),
		'default'     => '',
	);
	$settings['epm_paypal_img_url']        = array(
		'title'       => __( 'Image url', 'kco-epm-wc' ),
		'type'        => 'text',
		'description' => __( 'The url to the PayPal payment Icon.', 'kco-epm-wc' ),
		'default'     => 'https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png',
	);
	$settings['epm_paypal_disable_button']        = array(
		'title'       => __( 'Disable other gateway button', 'kco-epm-wc' ),
		'type'        => 'checkbox',
		'description' => __( 'Disables the "Select another Payment method" button on the Klarna Checkout.', 'kco-epm-wc' ),
		'default'     => 'no',
	);

	return $settings;
}

/**
 * Add PayPal as Payment Method to the KCO iframe.
 */
add_filter( 'kco_wc_create_order', 'kcoepm_create_order_paypal' );
function kcoepm_create_order_paypal( $create ) {
	$merchant_urls    = KCO_WC()->merchant_urls->get_urls();
	$confirmation_url = $merchant_urls['confirmation'];

	$kco_settings = get_option( 'woocommerce_kco_settings' );
	$name         = isset( $kco_settings['epm_paypal_name'] ) ? $kco_settings['epm_paypal_name'] : '';
	$image_url    = isset( $kco_settings['epm_paypal_img_url'] ) ? $kco_settings['epm_paypal_img_url'] : '';
	$description  = isset( $kco_settings['epm_paypal_description'] ) ? $kco_settings['epm_paypal_description'] : '';

	$klarna_external_payment = array(
		'name'         => $name,
		'redirect_url' => add_query_arg( 'kco-external-payment', 'paypal', $confirmation_url ),
		'image_url'    => $image_url,
		'description'  => $description,
	);

	$klarna_external_payment            = array( $klarna_external_payment );
	$create['external_payment_methods'] = $klarna_external_payment;

	return $create;
}

add_action( 'kco_wc_before_submit', 'kcoepm_payment_method' );
function kcoepm_payment_method() {
	if ( isset ( $_GET['kco-external-payment'] ) && 'paypal' == $_GET['kco-external-payment'] ) { ?>
		
        $('input#payment_method_paypal').prop('checked', true);
		// Check terms and conditions to prevent error.
		$('input#legal').prop('checked', true);
		
	<?php }
}

add_filter( 'kco_wc_klarna_order_pre_submit', 'kcoepm_retrieve_order' );
function kcoepm_retrieve_order( $klarna_order ) {
	if ( isset ( $_GET['kco-external-payment'] ) && 'paypal' == $_GET['kco-external-payment'] ) {
		$klarna_order_id = WC()->session->get( 'kco_wc_order_id' );
		$response        = KCO_WC()->api->request_pre_retrieve_order( $klarna_order_id );
		$klarna_order    = $response;
	}

	return $klarna_order;
}

add_action( 'init', 'kcoepm_remove_other_gateway_button' );
function kcoepm_remove_other_gateway_button() {
	$kco_settings   = get_option( 'woocommerce_kco_settings' );
	$disable_button = isset( $kco_settings['epm_paypal_disable_button'] ) ? $kco_settings['epm_paypal_disable_button'] : 'no' ;
	if ( 'yes' === $disable_button ) {
		remove_action( 'kco_wc_after_order_review', 'kco_wc_show_another_gateway_button', 20 );
	}
}
