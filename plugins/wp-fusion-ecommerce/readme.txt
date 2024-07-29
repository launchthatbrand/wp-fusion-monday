=== WP Fusion - Ecommerce Addon ===
Contributors: verygoodplugins
Tags: wp fusion, ecommerce, woocommerce, easy digital downloads
Requires at least: 4.0
Tested up to: 5.2.2
Stable tag: 1.10
WC tested up to: 3.3.3

Connects WordPress ecommerce plugins to your CRM's ecommerce system to record transactions

== Description ==

Connects WordPress ecommerce plugins to your CRM's ecommerce system to record transactions

== Changelog ==

= 1.10 - 8/7/2019 =
* Added Event Espresso support
* Fixes for deleted products

= 1.9.3 - 7/28/2019 =
* Added support for sale prices on products with Ontraport
* Fixed additional "Properties Value is not an integer" warnings with Drip Events

= 1.9.2 - 7/8/2019 =
* Added refund support for Infusionsoft
* Added option to send product prices tax-inclusive
* Added product image to ActiveCampaign order payload
* Added product categories to Drip Shopper Activity data
* Added product image to Drip order payload
* Added support for free trials in Woo Subscriptions

= 1.9.1 - 5/17/2019 =
* AgileCRM performance improvements
* Updated Drip with option for newer v3 API
* Fixed "Properties value is not an integer" error in Drip

= 1.9 - 4/12/2019 =
* Added LifterLMS support

= 1.8.2 - 4/8/2019 =
* AgileCRM bugfixes

= 1.8.1 - 4/6/2019 =
* Order date tweaks in WooCommerce
* Better date handling for orders with AgileCRM
* Fix for variation product IDs not saving

= 1.8 - 2/15/2019 =
* Added refunds support to Ontraport
* Added option to update deal stages in HubSpot when WooCommerce order status is changed

= 1.7.3 - 2/5/2019 =
* Added tax line item support with Drip
* Drip now receives proper order date (and time zone)

= 1.7.2 - 1/25/2019 =
* Error handling for WooCommerce order meta data that is not a meta object

= 1.7.1 - 1/23/2019 =
* Option for turning off Conversion tracking with Drip
* Added product ID into product dropdowns for Infusionsoft / Ontraport
* Integration classes can now be accessed via wp_fusion_ecommerce()->integrations->woocommerce (etc)

= 1.7 - 12/24/2018 =
* Hubspot integration
* Restrict Content Pro integration
* Error handling for "The integration already exists in the system." message with ActiveCampaign
* Added EDD payment gateway selector
* Added SKU to Ontraport product data

= 1.6.2 - 11/11/2018 =
* Added bulk processing tool for WooCommerce orders
* Fix for & symbols in product names causing errors with Infusionsoft
* Added support for EDD Discounts Pro

= 1.6.1 - 10/23/2018 =
* Bugfixes for addons / variations handling with Infusionsoft

= 1.6 - 9/13/2018 =
* Added support for WooCommerce Addons in ecommerce data
* Improvements to support changes in Drip's ecommerce functionality
* Amounts less than a dollar now syncing correctly with ActiveCampaign's Deep Data

= 1.5.1 - 2/26/2018 =
* AgileCRM bugfixes
* Fixed product lookup issues for Infusionsoft products with ampersands in the title

= 1.5 - 2/3/2018 = 
* Added AgileCRM ecommerce support
* Addded Ontraport referral tracking

= 1.4 =
* Added Drip ecommerce support
* Fixed GMT offset issues with Infusionsoft

= 1.3.5 =
* Order dates now use the date from the order instead of the current time
* Ontraport bugfixes

= 1.3.4 =
* Russian character encoding fixes
* Added admin tools for resetting wpf_ec_complete hooks on WooCommerce / EDD orders
* Prevent duplicate orders being sent on WooCommerce subscription auto-renewals

= 1.3.3 =
* Disabled invoices being sent by Ontraport
* Added backwards compatibility support for WC < 3.0
* Integrated WPF logging tools
* AC Deep Data integrations now triggers the "Makes A Purchase" action
* Added error handling and logging for API failures

= 1.3.2 =
* Misc. ActiveCampaign improvements
* Fixed Infusionsoft affiliate cookie expiration

= 1.3.1 =
* Bugfixes for WooCommerce 3.0.3

= 1.3 =
* Added Ontraport ecommerce integration
* Updated to support WooCommerce 3.0

= 1.2 =
* Added ActiveCampaign Deep Data Integration
* Better support for coupons using Easy Digital Downloads
* Added support for Infusionsoft referral partner links

= 1.1 =
* Added support for EDD Recurring Payments

= 1.0 =
* Further fixes to Asian character encodings

= 0.9 =
* Updates for Woo Subscriptions 2.x

= 0.8 =
* Support for Infusionsoft products with special character encodings

= 0.7 =
* Support for WooCommerce variations
* Ability to manually associate WooCommerce products with Infusionsoft products
* Speed improvements for ActiveCampaign users with no configured sales pipelines

= 0.6 =
* Pull revenue field before calculating if local record is empty
* Better handling for batch processing of old orders

= 0.5 =
* Fix for special characters in product names in Infusionsoft

= 0.4 =
* Bugfixes

= 0.3 =
* Added ActiveCampaign integration

= 0.2 =
* Added Woo Subscriptions support

= 0.1 =
* Initial release