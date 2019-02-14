# Klarna Checkout External Payment Method for WooCommerce plugin

This plugin provides a sample integration of an _External Payment Method_ with [Klarna Checkout for WooCommerce plugin](https://wordpress.org/plugins/klarna-checkout-for-woocommerce/) (Klarnas v3 platform).

* External Payments Methods needs to be activated by Klarna in the merchants account before installing this plugin.
* This code may be used in a production store, but _this code is not supported by Klarna or Krokedil_.

If Woo merchants would like to integrate other External Payment Methods supported by Klarna, it is recommended that they build a custom plugin similar on this sample. The available supported External Payment Methods can be provided by Klarna; the exact name of the External Payment Method sent to Klarna must match one in the list of supported Klarna methods or the data will be ignored and the External Payment Method won't be displayed in Klarna Checkout.


Below are some items to check if PayPal isn't working as expected in the Woo store:

* The connection settings to PayPal are pulled from the _WooCommerce -> Settings -> Payments -> PayPal_ section;  the Klarna Checkout only contains 3 PayPal settings: title, description, and Image url.
* Confirm that the PayPal Woo gateway is active in the Woo admin.
* Confirm that PayPal works correctly via standard Woo checkout, without Klarna Checkout.
* Confirm that there are no expected required fields for a customer to login that aren't covered in Klarna Checkout.

Read more about External Payment Methods in KCO v3 here - https://developers.klarna.com/en/se/kco-v3/checkout/external-payment-methods/.

## Changelog
**2019.02.14  - version 1.1.0**
* Tweak         - Removed kcoepm_retrieve_order() function. Sine v1.8.0 of KCO plugin, request to correct API is handled by the main plugin for external payment methods if kco-external-payment exist in URL (in redirect to confirmatioin page).

**Version 1.0**
* Initial commit