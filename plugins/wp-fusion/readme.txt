=== WP Fusion ===
Contributors: verygoodplugins
Tags: infusionsoft, crm, marketing automation, user meta, sync, woocommerce, wpfusion
Requires at least: 4.6
Tested up to: 6.1.1
Stable tag: 3.40.39
Requires PHP: 7.3

The only plugin you need for integrating your WordPress site with your CRM.

== Description ==

WP Fusion is a WordPress plugin that connects what happens on your website to your CRM or marketing automation tool. Using WP Fusion you can build a membership site, keep your customers’ information in sync with CRM contact records, capture new leads, record ecommerce transactions, and much more.

= Features =

* Automatically create new contacts in your CRM when new users are added in WordPress
	* Can limit user creation to specified user roles
	* Assign tags to newly-created users
* Restrict access to content based on a user's CRM tags
	* Option to redirect to alternate page if requested page is locked
	* Shortcodes to selectively hide/show content within posts
* Apply tags when a user visits a certain page (with configurable delay)
* Configurable synchronization of user meta fields with contact fields
	* Update a contact record in your CRM when a user's profile is updated
* LearnDash, Sensei, and LifterLMS integrations for managing online courses
* Integration with numerous membership and ecommerce plugins

== Installation ==

Upload and activate the plugin, then go to Settings >> WP Fusion. Select your desired CRM, enter your API credentials and click "Test Connection" to verify the connection and perform the first synchronization. This may take some time if you have many user accounts on your site. See our [Getting Started Guide](https://wpfusion.com/documentation/#getting-started-guide) for more information on setting up your application.

== Frequently Asked Questions ==
See our [FAQ](https://wpfusion.com/documentation/).

== Changelog ==

= 3.40.40 - 11/21/2022 =
* Added [Intercom event tracking](https://wpfusion.com/documentation/event-tracking/intercom-event-tracking/)
* Added [Intercom site tracking](https://wpfusion.com/documentation/tutorials/site-tracking-scripts/#intercom)
* Added [Klaviyo event tracking](https://wpfusion.com/documentation/event-tracking/klaviyo-event-tracking/)
* Added option with FluentCRM (REST API and same site) to set the default status for new contacts to either Subscribed or Pending (and trigger a double opt-in email)
* Added support for using CRM tags in Gravity Forms feed conditions
* Added link to edit the contact in the CRM for HighLevel (requires a Resync Contact IDs operation to load the edit URLs)
* Added Previous User Email field for sync (to track email address changes)
* Improved - Updated Klaviyo integration to use the new v2022-10-17 API, which will greatly improve performance
* Improved - Clicking Process WP Fusion Actions Again on a WooCommerce or EDD order, for a registered user, will force lookup that user's contact ID in the CRM (in case it's changed or been merged)
* Improved - Reorganized FooEvents fields in the settings into two groups, Attendee Fields and Event Fields
* Improved - When a WooCommerce User Membership is deleted, the user's status will be synced as cancelled, and the expiration date will be set to the current time
* Improved - When using the MemberPress offline gateway, and "Admin Must Manually Complete Transactions" is enabled, no tags will be applied until the admin has completed the transaction
* Improved - If a contact was created in staging mode, deactvating staging mode will also remove the user's `staging_xxxx` contact ID
* Fixed custom properties created with the core Groundhogg plugin (not the Better Meta extension) not being available for sync
* Fixed error resyncing available fields when the Groundhogg Better Meta plugin was active
* Fixed all phone numbers getting synced to HighLevel with a +1 country code
* Fixed: Removed Drip site tracking code (Drip no longer supports site tracking)
* Fixed being unable to clear out the User dropdown filter in the logs once a user was selected
* Fixed AccessAlly integration not listing more than 1000 available tags
* Fixed Resync Tags and Fields not working with Mautic over OAuth
* Fixed EDD Recurring Payments Statuses batch operation processing subscriptions from newest to oldest
* Fixed Easy Digital Downloads Orders batch operation processing orders from newest to oldest
* Fixed fatal conflict with Thrive Ultimatum
* Fixed fatal error adding new WP Fusion Thrive Leads API connection
* Developers: Added filter [wpf_event_tickets_apply_tags](https://wpfusion.com/documentation/filters/wpf_event_tickets_apply_tags/)

= 3.40.39 - 11/14/2022 =
* Added [Thrive Leads integration](https://wpfusion.com/documentation/lead-generation/thrive-leads/)
* Added OAuth API integration with Mautic (requires settings reset)
* Improved - The tags specified on a WooCommerce product for "Apply tags when initial transaction failed" will no longer be applied during a failed WooCommerce Subscriptions renewal order
* Fixed Defer Until Activation setting with BuddyPress / BuddyBoss causing tags linked to groups not to be applied
* Fixed Gravity Forms conditonal logic scripts getting output when pages were loaded via the REST API (i.e. by Yoast's link indexer)
* Fixed Thrive Automator integration not applying tags with CRMs that use tag IDs
* Fixed Thrive Automator integration not syncing the name field to the CRM
* Fixed WP Fusion integration showing up as an empty box under Thrive Product Manager >> API Integrations
* Fixed Groundhogg (REST API) integration not loading more than 25 available tags
* Fixed fatal error with Thrive Ultimatum `Call to undefined method WPF_Thrive_Autoresponder_Main::get_email_merge_tag()`

= 3.40.38 - 11/10/2022 =
* Fixed notice "Subscription status was changed to active, but the user is not currently subscribed to the product. No tags will be applied." with MemberPress since 3.40.36
* Fixed PHP warning `Undefined variable $code` in Zoho integration when handling an API error
* Fixed users with no tags showing in Users Insights as `a:0:{}`
* Fixed tags select not initializing properly when adding a new variable price option in Easy Digital Downloads

= 3.40.37 - 11/7/2022 =
* Added support for [AffiliateWP referrer tracking](https://wpfusion.com/documentation/other/affiliate-wp/#syncing-referrer-meta-fields) with Fluent Forms
* Improved - With the [WooCommerce Subscriptions Gifting integration](https://wpfusion.com/documentation/ecommerce/gifting-for-woocommerce-subscriptions/), if a separate shipping address was provided by the customer at checkout, the shipping details will be synced to the gift recipient's contact record
* Improved - With CRMs which require an email address for some API calls, WP Fusion will now check to see if the email can be retrieved from a WooCommerce order before making an API call to load the contact (improves performance)
* Fixed both the WooCommerce and WooCommerce Subscriptions applying the same tags on a new subscription order
* Fixed WP Fusion tag select boxes not usable after adding a new price option on a download product with EDD 3.0+
* Fixed membership meta fields not syncing with WooCommerce Memberships when no tags were configured on the membership plan
* Developers: Added filter `wpf_get_email_from_contact_id`
* [Updated Growmatik integration](https://github.com/verygoodplugins/wp-fusion-lite/pull/20)

= 3.40.36 - 10/31/2022 =
* Tested for WordPress 6.1.0
* Fixed Groundhogg syncing new user registrations to Groundhogg before WP Fusion
* Fixed WooCommerce Memberships Status field not syncing when a paused membership was reactivated due to a successful subscription payment
* Fixed LearnDash lessons protected by "Required Tags (Not)" not being protected when the parent course was set to apply tags on course completion
* Fixed WP Fusion overriding conditional visibility controls on Bricks Builder elements
* Fixed Staging Mode checkbox not un-checkable when the site was in staging mode due to detecting a change in the site's URL
* Fixed default profile type not being set with BuddyBoss when a linked tag was removed, when the profile type directory key didn't match the type name

= 3.40.35 - 10/24/2022 =
* Added [Group Leader Email and Group Name fields for sync with the Restrict Content Pro Group Accounts addon](https://wpfusion.com/documentation/membership/restrict-content-pro/#groups)
* Fixed automated enrollments into BuddyBoss / BuddyPress profile types not working when the type `directory_slug` didn't match the type `post_name`
* Fixed group license tags not being applied in Uncanny LearnDash Groups when a user was added to a group via an enrollment key
* Fixed JetEngine integration crashing the Listing Grid widget when Elementor wasn't active
* Fixed WooCommerce Subscriptions integration syncing 0 as the end date for subscriptions with no expiration
* Fixed variable price settings not displaying in Easy Digital Downloads since EDD 3.0
* Fixed WooCommerce Shipment Tracking tracking link not syncing for guest checkouts
* Fixed WooCommerce Shipment Tracking tracking link only syncing custom tracking links, not standard carriers
* Fixed WooCommerce Memberships membership status field not syncing when a subscription was reactivated in the user's frontend account area
* Fixed deprecated method `isSequential()` in WishListMember integration
* Fixed PHP warnings in Maropost integration
* Updated Autonami to FunnelKit Automations
* Developers: Tested for PHP 8.1.9

= 3.40.34 - 10/17/2022 =
* Fixed form field mappings not saving in the admin since 3.40.33

= 3.40.33 - 10/17/2022 =
* Added [Pipedrive CRM integration](https://wpfusion.com/documentation/installation-guides/how-to-connect-pipedrive-to-wordpress/)
* Improved - Exporting the activity logs will now automatically unserialize any serialized data
* Improved - The Refresh Tags and Fields link in the admin bar will now only be shown to administrators (`manage_options`)
* Improved - Dates will be force to UTC for timestamp conversion before beng synced (fixes issues with other plugins calling `date_default_timezone_set()` and messing up the conversion)
* Improved - The tags list in the All Users list now has a max height set, and can be clicked to expand
* Fixed Learnpress course content protection not working since LearnPress 4.1.6.9
* Fixed Filter Queries not working on LearnPress courses
* Fixed the TipTip JS file getting enqueued twice with WooCommerce (made tooltips not automatically close)
* Fixed password resets via Clean Login not being synced to the CRM
* Fixed expiration date not syncing with Paid Memberships Pro when a member was manually added to a level with a custom end date
* Fixed fatal error registering new users in the admin with ACF multiselect repeater data, when BuddyPress was active

= 3.40.32 - 10/20/2022 =
* Fixed access control system broken in 3.40.31

= 3.40.31 - 10/20/2022 =
* Added Refresh Available Tags & Fields button to the admin toolbar
* Added support for the Groundhogg White Label Branding extension
* Improved - If an Elementor element is protected by an invalid tag (for example a deleted tag, or tag from a prior CRM), the element will no longer be hidden
* Fixed - All links to CRM contact records in the logs will now open in a new browser window
* Fixed Gravity Perks Nested Forms getting processed before the parent entry was synced
* Fixed Event Tracking not working with HubSpot
* Fixed deleting an Event Tickets attendee applying the Deleted tags to the user who made the purchase, not the attendee
* Fixed Invalid JSON Response error when editing pages in Gutenberg and Gravity Forms was active
* Fixed fatal error loading the API Connections panel within Thrive Ovation

= 3.40.30 - 10/3/2022 =
* Added support for the Tickets Commerce payment gateways with Event Tickets and Event Tickets Plus
* Added support for guest checkouts with RestroPress
* Added Subscription Price field for sync with Paid Memberships Pro
* Added Event Ticket ID field for sync with Event Tickets Pro
* Improved - The Bento integration will force all email addresses to lowercase, both for syncing and for contact ID lookups
* **Warning** - Bento users - Note that this change will cause WordPress users to become disconnected from their Bento subscriber records if their email addresses in Bento contain capital letters. To fix this, it's recommended to run a Push User Meta operation one time, and WP Fusion will update all your subscriber records in Bento to use lowercase email addresses
* Improved - If Autonami is running on the same site, tag changes will be synced across to WP Fusion immediately, without requiring an API call
* Improved - If a MemberPress transaction expires, and the user has another active transaction to the same product, the Transaction Expired tags will not be applied
* Fixed lists not loading with Dynamics Marketing 365
* Fixed staging mode only auto-activating in the admin of the staging site
* Fixed Preview With Tag not working with tags with apostrophes in the tag name
* Fixed the BuddyPress integration syncing the profile type slug instead of profile type name when a user was added to a profile type via a linked tag
* Fixed being unable to remove a saved tag in the "Apply tags when a product with this term is purchased" setting with WooCommerce
* Fixed the Delivery Address fields not syncing with RestroPress
* Fixed Event Check-in tags not being applied with Event Tickets Plus when the ticket was sold via WooCommerce
* Fixed Attendee Deleted tags not being applied with Event Tickets Plus when the ticket was sold via WooCommerce
* Fixed the MailerLite integration logging an error when a subscriber record isn't found for an email address (should just be an info message)
* Fixed PHP warning syncing the Gender field with BuddyPress when it was empty
* Fixed custom fields not loading with Autonami 2.2.0
* Fixed PHP warning loading usermeta fields from Autonami (same site)
* Fixed unhandled fatal error when a HubSpot access token refresh failed
* Fixed fatal error starting an auto login session with Dynamics 365 when the contact wasn't on any lists

= 3.40.29 - 9/26/2022 =
* Added Asynchronous Checkout support to CartFlows
* Added support for syncing avatar URLs with wpForo
* Improved Asynchronous Checkout support for WooFunnels
* Improved - The HTTP API logs will now be associated with the user who initiaited the API call, instead of "system"
* Fixed address and phone fields not updating with Groundhogg (REST API)
* Fixed wpForo integration not loading since wpForo 2.0
* Fixed Filter Queries - Advanced not working when no post types were specified, since 3.40.28
* Fixed Filter Course Steps with LearnDash not working correctly when steps from multiple courses were listed on the same page
* Fixed error `The entity "ccedil" was referenced, but not declared.` when syncing a country value of `Curaçao` to Infusionsoft
* Fixed fatal error loading the Contact Fields settings tab when the "Remove WooCommerce Billing Address Fields for Free Checkout" plugin was active
* Fixed error `Uncaught Error: Too few arguments to function WPF_WPBakery::shortcode_output()` when using the Accordion shortcode with WPBakery
* Developers: Fixed WPF_Pods::post_save_user() running when editing users in the admin (was intended to be for frontend edits only)

= 3.40.28 - 9/19/2022 =
* Added Asynchronous Checkout support to WooFunnels
* Added "Add to CRM" checkbox on admin Add New User form
* Added as-you-type filter to Preview With Tags admin bar dropdown when there are more than 20 available tags
* Added [event check-in support to Modern Events Calendar integration](https://wpfusion.com/documentation/events/modern-events-calendar/#event-check-ins)
* Added Corporate Account Parent Email field for sync with MemberPress
* Improved - Filter Queries Advanced will now take into account any `post__in` arguments when building up the array of post IDs to exclude (improves performance)
* Improved - Moved MemberPress transaction expiration process from the `mepr-event-transaction-expired` to the `mepr-txn-expired hook`, should be more reliable
* Fixed Filter Queries - Advanced not working on `post` post type when not speficied as the `post_type` in the `WP_Query` arguments
* Fixed un-selected checkboxes not being loaded from HubSpot
* Fixed PHP warning in Users Insights integration when users did not have a contact record
* Developers: When a user's tags are modified, the memory cache at WPF_Access_Control::$can_access_posts will be cleared
* Developers: `wpf_crm_loaded` action will now pass the active CRM as a parameter

= 3.40.27 - 9/15/2022 =
* Fixed WP Fusion overriding Gravity Forms conditional logic for logged in users since 3.40.24

= 3.40.26 - 9/14/2022 =
* Fixed PHP warning marking content complete in LearnDash integration since 3.40.24
* Fixed missing `use_utm_names` flag with Ontraport when updating existing contacts (prevented lead sources from being set)
* Fixed `WPF_WooCommerce::get_contact_id_from_order()` returning an empty contact ID during guest checkout if the order was just placed
* Fixed MemberPress Membership Statuses batch operation not applying Expired tags for free memberships

= 3.40.25 - 9/12/2022 =
* Fixed fatal error `Call to undefined method WPF_Thrive_Autoresponder_Main::get_data_for_setup()` when editing pages with Thrive Architect since 3.40.24

= 3.40.24 - 9/12/2022 =
* Added [Thrive Automator integration](https://wpfusion.com/documentation/other/thrive-automator/)
* Added [Thrive Apprentice integration](https://wpfusion.com/documentation/learning-management/thrive-apprentice/)
* Added [LearnDash Progress Meta batch operation](https://wpfusion.com/documentation/learning-management/learndash/#progress-meta)
* Improved performance when using Filter Queries in Advanced mode and a query is for multiple post types
* Improved staging site automatic detection on hosts that find/replace the site URL throughout the database when copying from live to staging
* Improved and simplified duplicate site and staging site notices in the admin
* Improved - Stopped saving LearnDash course progress to the `wp_usermeta` table, it will now just be synced as needed as users progress through courses
* Fixed `?wpf-end-auto-login=true` query parameter not working
* Fixed PHP warning applying LearnDash lesson attributes on LearnDash versions below 4.2.0
* Fixed BuddyPress / BuddyBoss profile type names not syncing when the type was granted by a linked tag
* Fixed Gravity Forms conditional logic not saving the condition in the admin if you don't click on the operator dropdown first
* Fixed PHP error trying to sync the `order_notes` field with RestroPress
* Fixed PHP warning `undefined array key user_id` in PeepSo integration
* Fixed Advanced Ads integration settings not saving

= 3.40.23 - 9/6/2022 =
* Added integration with Subscriptions for WooCommerce
* Added integration with YITH WooCommerce Checkout Manager
* Improved support for Lock Lessons feature with LearnDash 4.2.0+ (now works in focus mode)
* Improved - When a product is fully refunded from a partially refunded WooCommerce order, the tags applied with that product will be removed, and the refund tags for that product will be applied
* Improved - With WooFunnels and Drip + ActiveCampaign, if an upsell is accepted after the order has been processed by Enhanced Ecommerce, it will be processed again (i.e. the existing invoice will be updated)
* Improved - Made WP Fusion menu item and settings page title able to be white labelled via the `gettext` filter
* Fixed fatal error viewing WooCommerce order received page, with WooFunnels, using an invalid order ID
* Fixed memory leak when using [the_excerpt] shortcode inside a post's main content area
* Fixed Defer Until Activation setting with WP Members not being respected when using the Limit User Roles feature (in the WP Fusion Advanced settings)
* Fixed fatal error `Too few arguments to function WPF_Access_Control::login_redirect()` with some themes since 3.40.21
* Fixed PHP warning during auto-login session when a Return After Login redirect was attempted

= 3.40.22 - 8/29/2022 =
* Added [tag-based conditional logic to Gravity Forms form fields](https://wpfusion.com/documentation/lead-generation/gravity-forms/#form-field-visibility)
* Added support for [syncing custom profile and registration fields with LearnPress](https://wpfusion.com/documentation/learning-management/learnpress/#syncing-meta-fields)
* Added an integration with the [Modern Events Calendar RSVP Addon](https://wpfusion.com/documentation/events/modern-events-calendar/#RSVPs)
* Added latitude and longitude fields for sync with NationBuilder
* Improved - The WooCommerce Subscriptions Statuses batch operation will now retroactively apply the Free Trial Over tag to subscribers who had a free trial
* Improved layout of Gravity Forms feed settings
* Improved styling of EDD order status metabox with EDD 3.0
* Fixed Apply Tags on View functionality not working when "Restrict Content" was disabled in the General settings
* Fixed WP Fusion's access rules sometimes running on content in Elementor's edit mode for non-admin editors
* Fixed connection settings getting overwritten when calling `wp_fusion()->settings->set()` after having switched to another multisite blog
* Fixed Async Checkout sometimes running on pending orders on the Order Confirmed page with WooFunnels
* Fixed fatal error trying to delete import groups that contained `WP_Error`s
* Fixed EDD order status metabox showing incorrect information since EDD 3.0
* Fixed fatal error auto-enrolling users into wpForo usergroups since wpForo 2.0
* Fixed disabling the API queue also disabling staging mode

= 3.40.21 - 8/23/2022 =
* Added Membership Plan Name field for sync with WooCommerce Memberships
* Added Status field for sync with JetPack CRM
* Improved - Moved WPBakery controls to their own settings tab
* Improved method of hiding content with WPBakery
* Improved Return After Login feature, will also run on the `login_redirect` filter for cases where another plugin takes priority over the login redirect on `wp_login`
* Updated to support Paymattic (used to be WPPayForm Pro), and fixed form feeds not saving
* Fixed creating a new BuddyBoss App Access Group based on a tag processing indefinitely
* Fixed JavaScript lead source tracking not working on some hosts (cookie components were being URI-encoded)
* Fixed PHP warning in Memberoni integration
* Fixed Lesson Locked text not showing with LearnDash 4.3.0+
* Fixed wpForo settings page missing since wpForo 2.0
* Fixed WPBakery tag search returning all tags in the UI

= 3.40.20 - 8/17/2022 =
* Added [Holler Box integration](https://wpfusion.com/documentation/other/holler-box/)
* Added Recruiter ID field for sync with NationBuilder
* Addded Avatar URL for sync with FluentCRM (can update the contact's photo by syncing a URL to an image)
* Added error handling for the Sendinblue Sales CRM API
* Improved - Asynchronous Checkout with WooCommerce will set a cron task for one minute in the future to confirm that the order was synced, for cases where the normal async process fails
* Fixed "Converted" tags not being applied when running the EDD Recurring Payments statuses batch operation
* Fixed Required Tags (Not) setting not working with WPBakery
* Fixed tags displaying as IDs after saving a WPBakery element

= 3.40.19 - 8/8/2022 =
* Added Availability, Support Level, Inferred Support Level, Priority Level, Do Not Call, Mobile Opt-In, and Do Not Contact fields for sync with NationBuilder
* Improved - If an EDD Software Licensing license is re-activated, the Expired tags will be removed
* Improved support for syncing user capabilities when using a custom table prefix
* Improved the UI for activating and deactivating the license on the Setup tab

= 3.40.18 - 8/1/2022 =
* Added Voting District fields for sync with NationBuilder
* Improved - MemberPress active tags will now also be applied on the `mepr-event-non-recurring-transaction-completed` hook
* Fixed a bug with automatic discounts and WooCommerce, where if the user's tags made them eligible for multiple discounts, navigating to the checkout page would apply an additional discount even when the cart total was already 0
* Fixed If-So integration not working with CRMs that use tag IDs
* Fixed tags not being applied properly when a LearnDash quiz with essay responses was graded in the admin
* Fixed Clean Login integration not syncing user_login and user_pass
* Fixed fatal error on the Contact Fields tab with WooCommerce Stripe Gateway 6.5.0
* Fixed removing a user role syncing the user's role as the name of the role that was just removed
* Developers: Fixed `wpf_get_setting_{$id}` filter not updating the option inputs on the settings page

= 3.40.17 - 7/25/2022 =
* Added an integration with the BuddyBoss App's [new Access Controls component](https://wpfusion.com/documentation/membership/buddyboss/#access-controls)
* Added support for Filter Queries on the Jet Engine Listing Grid widget
* Added County fields for sync with NationBuilder
* Improved performance when auto-enrolling users into BuddyPress profile types based on tags
* Fixed WP Event Manager integration not syncing registrations when transitioning an attendee from Waiting to Confirmed
* Fixed HubSpot lists showing as "Array" in If-So's Select A Condition dropdown
* Fixed fatal error saving LifterLMS membership plans on PHP 8+, since 3.40.15

= 3.40.16 - 7/19/2022 =
* Fixed error with Jetpack CRM "Jetpack CRM plugin not active" since 3.40.15
* Fixed staging mode not automatically activating when copying to a staging site on WP Engine and Cloudways
* Fixed tags not loading with MooSend
* Fixed PHP warnings in MooSend integration

= 3.40.15 - 7/18/2022 =
* Added support for [WP Event Manager's Sell Tickets Addon](https://wpfusion.com/documentation/integrations/wp-event-manager/)
* Improved - If the logs are disabled, the logs database table will be dropped
* Improved - Groundhogg integration will now log an error when updating a contact's email to an address that is already in use by another contact
* Fixed WP Event Manager integration not syncing registrations added via the admin
* Fixed GiveWP integration not syncing guest donors
* Fixed tags configured on LearnDash groups not being applied when users were self-enrolled in groups via the Uncanny Toolkit Pro Group Sign Up module
* Fixed tags with quotes in them not saving fully on LifterLMS course and membership settings
* Fixed the `read only` HTML flag showing with HubSpot active lists in the Oxygen conditions builder dropdown
* Fixed PHP warning in Contact Form 7 integration when editing the WP Fusion settings and no fields had been added to the form
* Fixed guest registrations not being synced with WP Event Manager 3.1.30+
* Fixed fatal error on WP Fusion settings page when connected to Jetpack CRM and the Jetpack CRM plugin was deactivated
* Developers - Improved: The `validate_field_` filters in the settings will now only run when an option value has changed instead of on every save

= 3.40.14 - 7/12/2022 =
* Improved - Fluent Forms global settings page will now be hidden from the menu since it doesn't do anything
* Fixed tags not being applied to recipient with Gifting for WooCommerce Subscriptions when user_email and billing_email were mapped to separate fields
* Fixed fatal conflict with older WPBakery versions (Uncaught ArgumentCountError)
* Fixed PHP notice in Advanced Ads integration on PHP 8

= 3.40.13 - 7/7/2022 =
* Fixed WooCommerce Subscriptions integration disabled since 3.40.12
* Fixed Mautic ignoring empty fields
* Fixed fatal error loading the WP Fusion PeepSo Groups settings subpage when no groups had been configured

= 3.40.12 - 7/5/2022 =
* Added an integration with [WPBakery Page Builder](https://wpfusion.com/documentation/page-builders/wpbakery-page-builder/)
* Added an integration with [WooCommerce Payments](https://wpfusion.com/documentation/ecommerce/woocommerce-payments/)
* Added Last Topic Completed field for sync with LearnDash
* Improved - If a Gravity Forms User Registration Update feed runs, only the submitted usermeta fields will be synced to the CRM (instead of all fields in the database)
* Improved - The MemberPress Memberships Statuses batch operation will now apply any tags configured via the Corporate Accounts addon when the user is a member of a sub-account
* Fixed Return After Login feature not working since WordPress 6.0
* Fixed the "Require Admin Permissions" setting (Advanced settings tab) not working
* Fixed conflict with "WooCommerce Fattureincloud Premium" when loading the available WooCommerce checkout fields in the admin
* Developers - Changed the EDD update check from a POST to a GET for improved performance

= 3.40.11 - 6/28/2022 =
* Added [Bricks builder integration](https://wpfusion.com/documentation/page-builders/bricks/)
* Improved - If a user registers and has an existing Lead record in Gist, the Lead will be converted to a User
* Fixed custom attendee fields not syncing with Event Tickets Plus v5.5.0+
* Fixed Advanced Custom Fields multi-checkbox fields not syncing when MemberPress was active
* Fixed MemberPress pending tags not being applied for pending transactions
* Fixed Transaction Expired tags not being removed when running a MemberPress Memberships Statuses batch operation
* Fixed MemberPress Transaction Expired tags not being removed when a new transaction was placed for a membership product that was previously expired
* Fixed some HTML and escaping glitches on the WooCommerce product panel upgrade nag with WP Fusion Lite

= 3.40.10 - 6/20/2022 =
* Added support for WP Global Cart (products configured on Site A will now have their tags applied when purchased on Site B)
* Addded an experimental method for setting the lead source tracking cookies on sites like WP Engine and Flywheel which sanitize UTM parameters out of request URIs
* Fixed calls to `wpf_user_can_access()` failing during a webhook (because the user was not logged in, it was assumed they did not have any tags)
* Fixed fatal error adding a new Solid Affiliate affiliate when the `saff_referral_count` field was enabled for sync
* Fixed special characters in Gravity Forms multiselect options appearing UTF-8 encoded when using the Create Tag(s) from Value option

= 3.40.9 - 6/13/2022 =
* Improved - When searching in the Redirect if Access is Denied dropdown in the main WP Fusion meta box, results will by grouped by post type
* Fixed [WooCommerce automatic discounts](https://wpfusion.com/documentation/ecommerce/woocommerce/#auto-applying-discounts) not applying when logging in using the checkout login form
* Fixed MemberPress membership statuses batch operation not applying tags for expired transactions
* Fixed Gist webhooks not working wih webhooks configured via automation rules
* Fixed CartFlows optin fields not being automatically detected if there was an existing custom checkout field with the same field key
* Fixed incorrect format when syncing dates to Bento

= 3.40.8 - 6/6/2022 =
* Added fields Marital Status and External ID for sync with NationBuilder
* Improved - With the `update` and `update_tags` webhook with Drip, the tags will now be read out of the webhook payload, improving performance and saving an API call
* Improved - If ActiveCampaign Deep Data responds with an error indicating the connection has been deleted, the saved connection ID will also be cleared out in WP Fusion
* Improved - If a timestamp being synced to HubSpot is already a whole date (midnight UTC), it won't be recalculated using the site's timezone offset
* Improved logging with WP Remote User Sync - The remote site that triggered the action will now be added to the log's source trace rather than a separate log entry
* Fixed WP Fusion's LearnDash course settings getting reset when quick editing a LearnDash course in the post list table
* Fixed conflict (`Uncaught ArgumentCountError`) with the auto-register functionality in FluentCRM v2.5.9
* Fixed upsell tags not being applied with CartFlows when Asynchronous Checkout was enabled
* Fixed staging mode activating if the `WPF_STAGING_MODE` constant was defined as `false` since 3.39.5
* Fixed slashes in Mautic API passwords not getting unslashed before saving, and breaking the API connection
* Fixed Ultimate Member integration syncing data back to the CRM after a new user was imported, when Push All was enabled
* Fixed typo in Constant Contact class name

= 3.40.7 - 5/30/2022 =
* Added [`the_excerpt` shortcode for use in the restricted content message](https://wpfusion.com/documentation/getting-started/access-control/#restricted-content-excerpts)
* Added Current Page pseudo-field to [lead source tracking fields](https://wpfusion.com/documentation/tutorials/lead-source-tracking/)
* Improved - If a <!--more--> tag is set for a post (or the More block is used), and the Restricted Content Message is being displayed, the post excerpt (above the <!--more--> tag) will be displayed
* Improved - A notice will be logged if a ThriveCart success URL is detected but the ThriveCart Auto Login setting is disabled
* Improved - If a WooCommerce subscription status is changed to Pending Cancel, the Next Payment Date field will be erased in the CRM
* Improved - If an auto login link is visited, the Return After Login process will be triggered (if enabled)
* Improved - User role won't be synced back to the CRM if it was changed by a webhook (improves performance)
* Improved - If a form submission is triggering a new user registration (via Gravity Forms User Registration, WPForms User Registration, or similar), the tags applied by the form submission will be passed directly to the new user account (this fixes an issue where the tags may not have been fully saved in the CRM by the time the user is logged in)
* Improved - Simplified the language and tooltips in the WP Fusion status meta box on single WooCommerce subscriptions
* Updated to support WPForms User Registration addon v2.0.0+
* Fixed bulk editing access rules not working since WordPress 6.0
* Fixed date fields not syncing to NationBuilder
* Fixed special characters in LearnDash course/lesson/topic titles getting synced to the CRM ASCII-encoded
* Fixed WooCommerce auto-applied discounts not respecting the usage limit per user setting during an auto-login session
* Fixed WPForms multi-select inputs not syncing correctly when set to Create Tag(s) from Value
* Fixed UI saying "Add Topics" instead of "Add Tags" when using a picklist field for tags with Salesforce
* Fixed PHP warning `Expected parameter 2 to be array, null given` when bulk editing WP Fusion access rules and the Merge Changes box was checked
* Developers: Added parameters `$event_id` and `$ticket_id` to the [`wpf_event_tickets_attendee_data` filter](https://wpfusion.com/documentation/filters/wpf_event_tickets_attendee_data/)
* Developers: Added filter `wpf_loaded_tags` when tags are loaded from the CRM for a user

= 3.40.6 - 5/23/2022 =
* Added option to "skip already processed" to the [Event Tickets attendees batch operation](https://wpfusion.com/documentation/integrations/the-events-calendar-event-tickets/#exporting-attendees)
* Fixed `billing_email` getting synced as a the user's `user_email` when a registered user checked out with WooCommerce
* Fixed fatal error `Class 'WPF_Staging' not found` when trying to sync data to the CRM on a multisite install after calling `switch_to_blog()`
* Fixed the Drip integration not loading custom fields with capital letters in the field keys
* Developers: Improved - WP Fusion will not save the main settings if you are currently switched to another blog on a multisite install. This prevents settings from the original site overwriting the site you've switched to.

= 3.40.5 - 5/16/2022 =
* Added [WP Booking System integration](https://wpfusion.com/documentation/events/wp-booking-system/)
* Added support for [Sendinblue event tracking](https://wpfusion.com/documentation/event-tracking/sendinblue-event-tracking/)
* Added support for [Sendinblue site tracking](https://wpfusion.com/documentation/tutorials/site-tracking-scripts/#sendinblue)
* Added the ability to [restrict the purchase of LifterLMS access plans using tags](https://wpfusion.com/documentation/learning-management/lifterlms/#access-plans)
* Improved - Mobile phone numbers synced to NationBuilder will be set to opted in for SMS by default
* Fixed expiration date not syncing and tags not being applied for the pending cancellation status with the Paid Memberships Pro - Cancel on Next Payment Date addon v0.4
* Fixed PHP warning in EventON integration
* Fixed PHP warning (undefined array key) in Restrict Content Pro integration
* Fixed PHP warning (undefined array key) in Uncanny LearnDash Groups integration

= 3.40.4 - 5/8/2022 =
* Added Add Attendees option to [EventON integration](https://wpfusion.com/documentation/events/eventon/)
* Added option to apply tags when an attendee is checked in to an EventON event
* Improved - When a Paid Memberships Pro membership level is cancelled the `pmpro_expiration_date` field will be erased
* Improved - When a Paid Memberships Pro membership is cancelled and the Cancel on Next Payment Date addon is active, the next payment date will be synced to the `pmpro_expiration_date` field
* Fixed missing AccessAlly settings submenu page with latest AccessAlly versions
* Fixed CartFlows optin step settings not saving since 3.39.0
* Fixed conflict with Premmerce Permalink Manager for WooCommerce (WP Fusion settings page not saving)
* Fixed logs not properly displaying the results of a value modified by the `wpf_format_field_value` when the input variable was empty
* Fixed custom fields not syncing with Constant Contact
* Fixed phone numbers and addresses not syncing with Constant Contact
* Fixed dates not syncing with Constant Contact
* Fixed empty dates getting synced to Mailchimp as Jan 1st 1970
* Developers: added action `wpf_crm_loaded`

= 3.40.3 - 4/25/2022 =
* Fixed all content restricted for logged in users since 3.40.2

= 3.40.2 - 4/25/2022 =
* Added [EMPTY and NOT EMPTY comparisons](https://wpfusion.com/documentation/getting-started/shortcodes/#empty-and-not-empty) to the `user_meta_if` shortcode
* Added Status field for sync with FluentCRM (same site)
* Improved - If a contact has been deleted or merged in ActiveCampaign and a "not found" error is triggered, WP Fusion will try to look up the contact again by email address and retry the API call
* Improved - If an invalid timestamp is being synced to HubSpot (+/- 1000 years from today) it will be removed from the payload to avoid an API error
* Fixed fatal error applying tags to event attendees with FooEvents when the initial contact record creation failed due to an API error
* Fixed "Apply tags when refunded" tags not being applied when a WooCommerce renewal order was refunded but the subscription was still active
* Developers: Fixed the `wp_fusion_init_crm` action running too early for code added to functions.php (moved from `plugins_loaded` to `init`)

= 3.40.1 - 4/21/2022 =
* Fixed `wp_fusion()->crm_base` variable not being initialized since 3.40.0
* Fixed some weirdness with Staging Mode since 3.40.0: tags and contact IDs were getting lost when resyncing
* Fixed bbPress archive restriction running when a redirect was saved in the settings but Restrict Archives not checked
* Fixed "Apply tags when purchased" setting missing on WooCommerce variations when Restrict Content was disabled in the General settings
* Fixed conflict with WooCommerce Anti Fraud (edits to users in the admin were syncing the user's email address to the admin's contact record)
* Fixed PHP warning in `WPF_CRM_Base` when viewing an admin user profile before WP Fusion had been set up
* Fixed error in WeGlot integration since 3.40.0

= 3.40.0 - 4/18/2022 =

** Heads up! ** This update cleans up a lot of old and redundant code in the CRM integration classes. It should be safe for regular users, but if you have any custom code or have created custom CRM modules, please test on a staging site before updating.

* Refactored and simplified CRM class structure: removed calls to wp_fusion()->crm_base, removed class `WPF_CRM_Queue`, removed redundancies in calling `WPF_Staging` CRM
* Refactored and simplified [lead source tracking](https://wpfusion.com/documentation/tutorials/lead-source-tracking)
* Removed parameter `$map_meta_fields` in CRM classes. Field mapping is now handled in `__call()` magic method in `WPF_CRM_Base` (i.e. `wp_fusion()->crm`)

* Added [Constant Contact integration](https://wpfusion.com/documentation/installation-guides/how-to-connect-constant-contact-to-wordpress/)
* Improved - WPML, WeGlot, TranslatePress, and GTranslate integrations will now sync the current language preference whenever a contact is created or updated in the CRM (including for guests)
* Improved - When processing actions again for WooCommerce, Woo Subscriptions, GiveWP, EDD, and Gravity Forms, any tags will be applied regardless of the cache in WordPress (bypasses the Prevent Reapplying Tags option)
* Fixed Events Manager integration not detecting cancelled bookings when the plugin language was non-English
* Fixed admin users list showing No Contact ID for users who had a contact ID but no tags
* Fixed WooCommerce Memberships for Teams team meta batch operation crashing when trying to access deleted users
* Fixed updates to existing leads not working with Intercom and Gist

= 3.39.5 - 4/13/2022 =
* Fixed WooCommerce auto-applied coupons not working in AJAX requests since 3.39.3
* Improved - MemberPress emails and receipts will no longer be sent when a user is auto-enrolled into a membership via a linked tag
* Developers - Added function `wpf_is_staging_mode()`

= 3.39.4 - 4/11/2022 =
* Added [WPPayForm integration](https://wpfusion.com/documentation/ecommerce/wppayform/)
* Added option to [use a custom picklist field for tags with Salesforce](https://wpfusion.com/documentation/crm-specific-docs/salesforce-tags/)
* Added option to configure form auto-login per form feed with Gravity Forms
* Added link to view the donor's record in the CRM to the GiveWP / WP Fusion payment meta box
* Improved - Auto login sessions will now be ended on the `set_logged_in_cookie` action instead of `wp_login` and `wp_authenticate` (fixes conflict with Gravity Perks Auto Login)
* Improved - Updated NationBuilder add contact API endpoint to `/people/push` instead of `/people` to better handle merging duplicate records
* Improved logging when syncing dates with invalid formats
* Improved handling of European date formats with Advanced Custom Fields
* Improved ActiveCampaign error handling
* Improved - Updated Mailchimp `add_contact()` API call to `PUT` instead of `POST` to better handle duplicates
* Fixed orders failing to sync when using Asynchronous Checkout and the WooCommerce PayPal Payments gateway, when payment capture was delayed by PayPal
* Fixed PHP warning trying to apply tags via AJAX when an invalid tag name was provided
* Fixed Gist integration not loading more than 50 available tags
* Fixed user passwords getting recorded in the logs when registering a new user during an active auto-login session
* Fixed date fields on ACF Frontend forms syncing the previous value
* Fixed WooCommerce Subscriptions renewal payments getting processed by Asynchronous Checkout (if enabled)
* Fixed PHP warning in Advanced Custom Fields integration when syncing repeaters
* Fixed fatal error calling `wpf_get_current_user()` before the API was initialized
* Fixed `contact data was not found or in an invalid format` error when receiving webhooks from FluentCRM on the same site
* Fixed broken "Reauthorize with NationBuilder" link on the setup panel
* Fixed (Lite) - Integrations settings tab will now be hidden in WP Fusion Lite

= 3.39.3 - 4/4/2022 =
* Added ACF Frontend integration
* Addded support for tag-based visibility controls with new Elementor Container widget
* Improved Elementor visibility controls: protected sections and columns will now be completely removed from the page instead of hidden via CSS
* Improved - WooCommerce automatic discounts will not be applied if the cart total has already been discounted to 0
* Fixed Give email optin checkbox click also selecting the anonymous donation checkbox on donation forms that allow anonymous donations
* Fixed WP Fusion's WooCommerce Subscriptions settings fields showing up on regular (non-subscription) variable products since WooCommerce Subscriptions v4.0.0
* Fixed Capsule integration not returning the contact ID of newly created contacts
* Fixed filtering in the logs not working if headers were already sent by another plugin
* Fixed 401 / unauthorized errors not being correctly handled with Bento
* Fixed dismissing notices on the WPF settings page not being remembered
* Fixed HubSpot integration starting a site tracking session when batch exporting WooCommerce guest orders
* Fixed adding a new list in HubSpot via WP Fusion causing the existing Select A List dropdown to only show `(array)` for each list option
* Developers: Added filter `wpf_woocommerce_order_statuses_for_payment_complete`

= 3.39.2 - 3/28/2022 =
* Fixed "contact not found" being treated as an irrecoverable API error with HubSpot, since v3.39.1
* Fixed missing second parameter `$force` in [wpf_get_tags() function](https://wpfusion.com/documentation/functions/get_tags/)
* Fixed WooCommerce Subscriptions psuedo fields not being declared as pseudo fields (would try to load subscription data from the CRM and save it in usermeta)
* Fixed unhandled exception when updating a contact's email address to an email address already in use by another contact, with FluentCRM (same site)

= 3.39.1 - 3/28/2022 =
* Added License ID and License Key fields for sync with EDD Software Licensing integration
* Added "Skip Already Processed" checkbox option to Ninja Forms entry export batch operation
* Improved - The tooltip for restricted content in the admin post list table will now show if a redirect has been configured on the post
* Improved HubSpot error handling
* Fixed MemberMouse integration not applying tags for new purchases since v3.37.12
* Fixed webhooks with multiple contact records not being successfully processed with Salesforce and MailerLite since 3.38.31
* Fixed import by tag not working with MailChimp and numeric tag IDs
* Fixed Meta Box fields being registered as pseudo fields (only one-way sync)
* Fixed Sync Tags on Login and Sync Meta on Login running at the start of a ThriveCart auto-login and sometimes erasing the user's cached tags
* Fixed "The link you followed has expired" error when bulk deleting users and the Members plugin is active
* Fixed unclosed <table> tag on the Setup tab when connected to NationBuilder
* Fixed fatal error submitting Elementor Forms when a multiselect or multi-checkbox type field was enabled for sync
* Fixed PHP notice in WPF_Integrations_Base::guest_registration()
* Fixed PHP notice in WPF_Simply_Schedule_Appointments::create_update_customer()

= 3.39.0 - 3/21/2021 =
* Added [option to completely disable the access control system](https://wpfusion.com/documentation/getting-started/general-settings/#restrict-content)
* Added [a WP Fusion status metabox](https://wpfusion.com/documentation/ecommerce/woocommerce-subscriptions/#subscription-management) when editing a single WooCommerce subscription
* Added support for syncing custom fields added to a CartFlows optin step
* Improved Sendinblue error handling for failed contact record creation
* Fixed first and last name fields not syncing to Bento
* Fixed SliceWP integration syncing the user's last name as the email address
* Fixed SliceWP integration syncing currency symbol with total earnings
* Fixed Gamipress default ranks not being synced during new user registrations
* Fixed Gravity Forms feeds not processing when set to "Process only if payment is successful", and the initial payment was a subscription payment
* Fixed CartFlows upsell settings missing since CartFlows v1.9.0
* Fixed fatal error "Class name must be a valid object or string" when syncing tags to the remote site with WP Remote Users Sync
* Fixed capabilities being saved to the database with `wp_` as the prefix instead of the current blog prefix
* Fixed some unclosed HTML tags in the single taxonomy term settings table

= 3.38.46 - 3/14/2022 =
* Added setting Remove Tags - Cancelled to [Teams for WooCommerce Memberships integration](https://wpfusion.com/documentation/membership/teams-for-woocommerce-memberships/#tagging-team-members)
* Added Meta Box integration (custom user fields will now be auto-detected and listed for sync)
* Improved - "Automatic tags" (i.e. dynamic tags) with WooCommerce will no longer be removed when an order is refunded (never worked properly and is inconsistent with the other general tag settings)
* Improved error handling with ActiveCampaign (403 errors are now properly handled)
* Fixed SliceWP integration only syncing Paid commissions, not Unpaid
* Fixed Push User Meta action not syncing SliceWP fields
* Fixed Subscription in Trial tags not applying with new EDD subscriptions
* Fixed the AffiliteWP Referral Count field counting pending and rejected referrals
* Fixed un-checked checkboxes not syncing with Sendinblue boolean fields
* Fixed FluentCRM (same site) custom fields not being erased when a null value was synced
* Fixed BuddyBoss Profile Complete tags not applying since 3.38.44
* Fixed BuddyBoss Profile Complete tags applying on every other widget view
* Fixed logs not indicating a value was modified by the `wpf_format_field_value` filter when only the type had changed (fixed `!=` to `!==`)

= 3.38.45 - 3/7/2022 =
* Improved - The Restrict Forums setting with bbPress will now also apply to forums that are displayed within a BuddyBoss / BuddyPress group's discussion tab
* Improved - Bento event tracking can now accept an array for `$event_data`
* Improved - Shortened the URL length when filtering data in the activity logs
* Fixed WooCommerce coupon restriction by tags not working when the WooCommerce PDF Vouchers plugin is active
* Fixed dashes in LearnDash course or lesson titles getting synced to the CRM as HTML characters with the Last Lesson Completed, Last Course Completed, and Last Course Progressed fields
* Fixed dates syncing to HubSpot in UTC not local time
* Fixed Give Donations batch operation not working since 3.38.37
* Fixed custom fields not syncing to Autonami
* Fixed fatal error checking if WooCommerce Subscriptions was running on a duplicate site with Woo Subscriptions versions less than 4.0, since 3.38.44
* Fixed Uncanny Groups integration settings hidden on subscription products
* Fixed fatal error clicking Process WP Fusion Actions Again on a Gravity Forms entry from a deleted form
* Fixed Import Tool not working with Mailchimp since 3.38.35
* Fixed Select a CRM Field dropdown hidden on WPForms feeds (z-index was too low)
* Fixed date filter in the logs not working

= 3.38.44 - 2/28/2022 =
* Added Phone 1 Extension, Phone 1 Type, Phone 2, Phone 2 Extension, Phone 2 Type fields for sync with Infusionsoft
* Added Reauthorize with Dynamics 365 link to the Setup tab when connected to MS Dynamics 365
* Improved Zoho error handling, and made error messages clearer
* Improved - WP Fusion will now track a user's BuddyBoss / BuddyPress profile completion in the database to avoid the complete tags being reapplied every time the widget is loaded
* Fixed gift recipient's contact record getting merged with the gift purchaser with WooCommerce Subscriptions Gifting when the billing_email was enabled for sync
* Fixed update contact method not working with FluentCRM (REST API)
* Fixed contact updates in FluentCRM (same site) triggering data to be loaded back into WP Fusion right away
* Fixed Approved tags not being applied after a successful Stripe payment with Events Manager
* Fixed Cancelled tags not being applied when a booking was rejected or deleted with Events Manager
* Fixed PHP warning when using LearnDash wtih Filter Course Steps on a course that doesn't have any sections, since 3.38.43
* Fixed Contact Form 7 applying "Submit" as a tag when no other tags were specified
* Fixed use of deprecated function WC_Subscriptions::is_duplicate_site() with WooCommerce Subscriptions 4.0
* Fixed `PHP Notice: register_rest_route was called incorrectly` with Beaver Themer integration
* Fixed AffiliateWP affiliate details not syncing when an affiliate was edited in the admin
* Fixed checkboxes in the addon plugins that should be checked by default not being checked by default
* Fixed MemberPress checkbox fields syncing as "on" instead of `true`
* Fixed MemberPress multiselect fields not syncing
* Developers - Added [wpf_admin_override filter](https://wpfusion.com/documentation/filters/wpf_admin_override/)

= 3.38.43 - 2/21/2022 =
* Added Microsoft Dynamics 365 CRM integration
* Added [SliceWP integration](https://wpfusion.com/documentation/affiliates/slicewp/)
* Added Remove Tags and Apply Tags - Cancelled settings [to Events Manager integration](https://wpfusion.com/documentation/events/events-manager/#tagging-attendees)
* Improved (event tracking) - If an event value is sent to Bento [as a valid JSON string](https://wpfusion.com/documentation/event-tracking/bento-event-tracking/#advanced-usage) then that will be used in place of the default `name` and `val` properties in the `details` of the event payload
* Improved - Bento Event Tracking will now send events to `event.details.name` and `event.details.val`
* Fixed Gravity Forms feeds not processing if they were set to only run on a successful Stripe payment, and the Payment Collection Method was set to Stripe Credit Card Field in the Gravity Forms settings
* Fixed tags getting removed during a failed WooCommerce Subscriptions renewal payment, if Remove Tags was checked even though the user still has a separate active subscription to the same product, since 3.38.41
* Fixed restricted content message appearing by default on bbPress search results page
* Fixed checkbox fields not syncing with HubSpot
* Fixed tags not applying using Process WP Fusion Actions Again on a WooCommerce order since 3.38.42
* Fixed Import Users tool not working with FluentCRM (same site)
* Fixed Filter Course Steps with LearnDash not correctly calculating the position of sections after lessons had been removed from those sections (maybe not 100% fixed yet)
* Fixed fatal error approving new users in BuddyPress who were registered via Gravity Forms User Registration, while the Defer Until Activation setting was enabled in WP Fusion

= 3.38.42 - 2/14/2022 =
* Added MooSend integration
* Added [order sync status column](https://wpfusion.com/documentation/ecommerce/woocommerce/#order-status-column) to WooCommerce orders list table
* Added - With HubSpot, Infusionsoft, FluentCRM, and Groundhogg, you can now type new tag names into the Select Tag(s) dropdown, and if the tag doesn't exist WP Fusion can send an API call to create the new tag
* Added links to the logs to go directly to the CRM contact record for each user
* Added Billing First Name and Billing Last Name fields for sync to Paid Memberships Pro integration
* Improved - WooCommerce + WP Fusion order status metabox will now require the `manage_woocommerce` permission (i.e. Shop Manager)
* Improved - Logs will now show "user-login" as the source when data was synced due to Login Meta Sync or Login Tags Sync
* Improved - The load_contact() method with ActiveCampaign will now use the v1 API for improved performance
* Fixed new Events Manager bookings with the Approved status not applying the Approved tags
* Fixed - Running Process WP Fusion Actions again on a WooCommerce order will now remove the `order_action=wpf_process` query parameter after it's finished so orders aren't accidentally exported twice

= 3.38.41 - 2/8/2022 =
* Fixed syntax error with PHP <= 7.2 since 3.38.40
* Fixed redundant WooCommerce integration and WooCommerce Subscriptions integration both applying the same tags for each renewal order
* Fixed - Outgoing API calls to remove and apply the same tag in the same request will be ignored (fixes issue of tag changes getting processed out of order in the CRM)
* Fixed product-specific WooCommerce Subscriptions fields getting orphaned on the main Contact Fields list even after being disabled
* Fixed 422 error with Drip when trying to sync data into a field that had a dash or space in the field ID
* Fixed Defer Until Activation setting with BuddyPress not working when using the Limit User Roles option in the WP Fusion settings
* Fixed error loading Infusionsoft / Keap social media fields
* Fixed failed user_register actions logging the full user POST data to the logs (including plaintext passwords)
* Extended default ActiveCampaign HTTP timeout to 20 seconds (instead of 15)

= 3.38.40 - 2/7/2022 =
* Added [Solid Affiliate integration](https://wpfusion.com/documentation/affiliates/solid-affiliate/)
* Added IP Address field to Contact Fields list
* Added ability to create new tags via the Select Tag(s) dropdown with Ontraport
* Improved - [Staging site detection](https://wpfusion.com/documentation/tutorials/staging-sites/) will now prompt you whether to recognize the new site URL as the main site vs. staying in staging mode (similar to WooCommerce Subscriptions)
* Improved - When creating a new lead in Kartra, the user's IP address will be sent by default
* Fixed updated Next Payment Date not syncing when editing a WooCommerce subscription in the admin (was syncing the previous value)
* Fixed some product-specific fields (Name, SKU, Start Date, End Date) not syncing with WooCommerce Subscriptions
* Fixed deleted tags in Mailchimp not being removed from the dropdowns in WP Fusion
* Fixed LearnDash course sections displaying in the wrong positions when using Filter Course Steps.
* Fixed "contact data not found" error receiving Groundhogg REST webhooks
* Fixed Bento event tracking not working when specifying a value
* Fixed Paid Memberships Pro expiration date not syncing when manually edited on the user's profile
* Fixed error `Call to a member function get_title() on bool` when processing a WooCommerce Subscriptions renewal payment for a deleted product

= 3.38.39 - 2/2/2022 =
* Hopefully fixed the issues with Staging Mode auto-activating since 3.38.35: changes to the home_url() as well as changes between http:// and https:// will no longer activate staging mode
* Improved - Filter Queries will now be bypassed when DOING_CRON is set to true
* Fixed WP Fusion subscription settings hidden on WooCommerce Subscriptions products with Subscriptions 4.0+
* Fixed event value not syncing with Bento event tracking
* Fixed PHP warning calling wp_fusion()->user->get_user_meta() when not logged in
* Fixed bug in the logs where link to edit a WooCommerce order would be replaced by link to edit the contact in the CRM

= 3.38.38 - 1/31/2022 =
* Additional fixes for staging mode auto-activating when the admin language was changed with WPML and TranslatePress, since 3.38.35
* Tested for WordPress 5.9

= 3.38.37 - 1/31/2022 =
* Added [ARMember integration](https://wpfusion.com/documentation/membership/armember/)
* Added [Apply Tags - Check-in setting](https://wpfusion.com/documentation/integrations/the-events-calendar-event-tickets/#event-tickets) to Event Tickets integration
* Added support for syncing to Leads with Intercom (enable from the Integrations tab)
* Added Subscription Product SKU field for sync with WooCommerce Subscriptions
* Improved - Updating subscribers with Bento will now use the UUID as an identifier, not the email address, which fixes issues arising from users changing their email address and getting disconnected from their subscriber record
* Improved - ACF User fields with the Multiple option enabled will now sync an array of full user names (not IDs)
* Improved Autonami error handling
* Fixed query filtering running twice on WooCommerce products when Filter Queries was enabled at the same time as Hide Restricted Products
* Fixed private BuddyPress XProfile fields not being exported with the Push User Meta batch operation
* Fixed staging mode auto-activating when the admin language was changed with WPML and TranslatePress, since 3.38.35

= 3.38.36 - 1/25/2022 =
* Fixed Gravity Forms feed settings menu item not showing since 3.38.35

= 3.38.35 - 1/24/2022 =
* Added social fields for sync with Infusionsoft / Keap (click Refresh Available Fields to load them)
* Added dynamic tagging support to Mailchimp integration (requires resetting the settings for existing installs)
* Improved - WP Fusion will now run before any Gravity Forms User Registration feeds. This allows for a subscriber to update their email address in the CRM via an auto-login link before registering a new account.
* Improved Mailchimp API performance for applying and removing tags
* Improved Mailchimp contact ID lookup — will now only return exact matches (fixes an issue where tags would be applied to the wrong contact when using sub-inboxes with Gmail)
* Improved - The new standalone api.php endpoint will now use wp_cache_set() to improve performance when looking up user IDs from contact ID
* Improved - If the site URL changes, WP Fusion will automatically enable Staging Mode
* Fixed "Apply Lists" option appearing on Gravity Forms feeds when connected to FluentCRM
* Fixed 5 second timeout loading a contact with Maropost (increased to 20s)
* Fixed fatal error with HTTP API Logging when the HTTP response was a WP_Error object, since 3.38.34
* Fixed calls to deprecated function GetOption() in WishListMember integration

= 3.38.34 - 1/18/2022 =
* Added Easy Digital Downloads Checkout Fields Manager integration
* Added support for syncing user profile data from [Advanced Custom Fields flexible content fields](https://wpfusion.com/documentation/other/advanced-custom-fields/#repeaters-and-flexible-content)
* Added [Apply Tags - Pending option](https://wpfusion.com/documentation/membership/memberpress/#tagging) to MemberPress integration
* Added [Link with Tag functionality](https://wpfusion.com/documentation/learning-management/learnpress/) to LearnPress integration
* Added View In CRM link to the user action links on the All Users list in the admin
* Improved - Auto login will set a cookie `wordpress_logged_in_wpfusioncachebuster` which should bypass caching on most configurations
* Improved - If the full WP Fusion is activated, the WP Fusion Lite plugin will be automatically deactivated
* Improved - Removed some redundant data in the logs when HTTP API logging is enabled
* Improved - HTTP API logging will now show JSON-decoded request and response bodies where applicable
* Improved error handling with ActiveCampaign Deep Data
* Improved HubSpot error handling
* Fixed Active lists not showing up as options in the Select A List dropdown for the Import Users tool with HubSpot
* Fixed If-Menu integration not working with CRMs that use tag categories
* Fixed Required Tags (all) and Required Tags (not) settings displaying on admin menu editor even when User Menus was inactive
* Fixed parse error in ActiveCampaign integration with PHP 7.2

= 3.38.33 - 1/10/2022 =
* Added [User Menus integration](https://wpfusion.com/documentation/tutorials/menu-item-visibility/#advanced-usage)
* Added Status section to WP Fusion settings with status and debug information about the plugin
* Added support for Group and [Repeater fields with Advanced Custom Fields](https://wpfusion.com/documentation/other/advanced-custom-fields/#repeaters)
* Added notice to HubSpot integration about enabling marketing contacts for the WP Fusion app
* Improved - ActiveCampaign integration now fully uses the WordPress HTTP API instead of the ActiveCampaign PHP SDK
* Improved - HTML tags will be removed when exporting the activity logs to .csv
* Improved - When resetting the main settings page, the cached contact IDs and tags will be deleted for all users (this fixes "Invalid contact ID" errors when switching between CRM accounts)
* Fixed Quiz Failed tags not being applied since LearnDash 3.6.0
* Fixed taxonomy term protections not working since 3.38.32
* Fixed fatal error loading the `role` field with an array value (now the first array value will be used as the role)
* Fixed ActiveCampaign multiselect fields loaded as text being prepended/appended by ||
* Fixed missing scope `crm.lists.write` with HubSpot (prevented adding contacts to static lists for OAuth apps connected after December 15th 2021)

= 3.38.32 - 1/3/2022 =
* Added support for [Fluent Forms User Registration](https://wpfusion.com/documentation/lead-generation/fluent-forms/#user-registration)
* Added option to [sync WooCommerce Subscriptions details to separate custom fields](https://wpfusion.com/documentation/ecommerce/woocommerce-subscriptions/#syncing-subscription-fields) in the CRM for each subscription product
* Improved logging of authentication errors with Salesforce
* Improved - Stopped ending auto-login sessions on registration (`user_register` hook)
* Updated ActiveCampaign integration to use v3 API for loading contacts
* Fixed the Prevent Reapplying Tags setting not working if at least one of the tags to be applied was new
* Fixed updater license check returning "invalid item ID" message and deactivating license
* Fixed PHP notice "wpdb::prepare was called incorrectly" when checking taxonomy term access rules on posts
* Fixed PHP warning during MemberMouse registration when Advanced Custom Fields is active
* Fixed issue syncing tags with Emercury for subscribers that had upper case letters in their email address

= 3.38.31 - 12/27/2021 =
* Added a new api.php endpoint that [can be used for super fast async webhook processing](https://wpfusion.com/documentation/other-common-issues/webhooks-not-being-received-by-wp-fusion/#the-async-endpoint)
* Removed old wpf_post.php file and API endpoint
* Added an option to process asynchronous webhooks using a cron job instead of trying to start the background worker with each webhook
* Added link to admin user profile to view the logs for that user
* Improved performance when using async=true webhooks. The background worker will no longer attempt to start if it is already running.
* Improved - The process lock time for the background worker will now respect the site's PHP `max_execution_time`. The lock time will be the max time + 30 seconds.
* Improved ActiveCampaign error handling so that it now looks at the response code instead of message (some errors were not being caught properly with non-English accounts)
* Fixed Resync Tags for Every User operation not triggering automated course enrollments
* Fixed PHP warning trying to lookup ActiveCampaign Deep Data customer ID when an existing contact was not yet registered as a Deep Data customer
* Developers: Added filter `wpf_query_filter_cache_time`

= 3.38.30 - 12/20/2021 =
* Added support for [custom objects with HubSpot](https://wpfusion.com/documentation/crm-specific-docs/custom-objects-with-hubspot/)
* Fixed new event tracking integration disabled with Gist and Intercom
* Fixed layout glitch on EDD admin customer profile with CRMs with long names
* Fixed fatal error in Woo Memberships for Teams integration when adding an invalid user ID to a team
* Fixed infinite redirect with LearnDash when using Shared Course Steps + Filter Course Steps, and trying to access a topic via permalink

= 3.38.29 - 12/13/2021 =
* Fixed parse error in Event Tickets integration since 3.38.29
* Fixed missing event value with Gist event tracking

= 3.38.28 - 12/13/2021 =
* Added [If Menu integration](https://wpfusion.com/documentation/other/if-menu/)
* Added event tracking support for Gist
* Added event tracking support for Intercom
* Improved - Invalid characters will now automatically be removed from the event name with ActiveCampaign event tracking
* Improved - If HTTP API logging is enabled, event tracking API calls will be sent `'blocking' => true` so that the responses are logged
* Improved - Select Tags boxes in Appearance >> Menus editor will now lazy load their tags, for improved menu editing performance
* Improved - Stopped syncing user ID when updating Gist subscribers (should cause records to get merged less often)
* Improved - Moved LearnDash course settings to standalone settings tab
* Fixed Gravity Forms not pre-filling during an auto-login session when the form was added via the Elementor "form" widget
* Fixed PHP warning in Event Tickets integration

= 3.38.27 - 12/6/2021 =
* Added user search field to the logs table
* Added lock indicator on locked LearnDash topics when Lock Lessons is enabled
* Added setting to the batch operations to re-process locked records for WooCommerce orders, Easy Digital Downloads payments, GiveWP donations, and Gravity Forms entries
* Added view in CRM link to Easy Digital Downloads customer profile
* Added view in CRM links to Mailchimp integration
* Added view in CRM links to Bento integration
* Improved support for using Create Tag(s) from Value with multi-checkbox inputs on forms
* Improved - If an `email` parameter is provided in a webhook request, WP Fusion will attempt to detect when a contact ID associated with a user may have changed due to a merge
* Improved - If a field type is set to "raw" an empty value loaded over the CRM will erase the value saved in WordPress
* Fixed has_access() check always failing in latest BuddyBoss App versions
* Fixed "The tags must be an array." error message with HighLevel when using Create Tag(s) from Value
* Fixed attendee phone number and company not syncing with FooEvents when the attendee's email is the same as the customer email
* Fixed wpf_infusionsoft_safe_tags filter not stripping invalid characters out of tag category names
* Developers: In cases where posts (i.e. orders) were marked with `wpf_complete` set to `true`, `wpf_complete` will now be set to the time (`current_time( 'Y-m-d H:i:s' )`)

= 3.38.26 - 11/30/2021 =
* Fixed CartFlows settings panel not clickable in CartFlows Pro v1.7.2
* Fixed fatal error in MemberPress Memberships Statuses batch operation when trying to apply Cancelled tags based on transaction status

= 3.38.25 - 11/22/2021 =
* Added option to prefix usermeta keys with the current blog prefix to avoid sharing contact IDs and tags across sub-sites on multisite installs (can be enabled from the Advanced settings tab)
* Added warning to the settings about applying tags for pending WooCommerce orders
* Added [`timezone-offset` attribute](https://wpfusion.com/documentation/getting-started/shortcodes/#user-meta-formatting-timezone-offset) to `user_meta` shortcode
* Added logging for when a date failed to sync to the CRM because the input date format couldn't be converted to a timestamp
* Added error logging for failed Salesforce access token refreshes
* Fixed Join Date fields not syncing with Restrict Content Pro
* Fixed Notes field not syncing with Restrict Content Pro
* Fixed EDD Orders exporter exporting unpaid orders
* Fixed PHP warning tracking events with HubSpot
* Fixed request to refresh Salesforce access token not being recorded by HTTP API Logging
* Fixed fatal error updating a Bento subscriber without an email
* Developers: Added filter `wpf_restricted_terms_for_user`
* Developers: Added filter `wpf_taxonomy_rules`
* Developers: Added constants `WPF_CONTACT_ID_META_KEY` and `WPF_TAGS_META_KEY`

= 3.38.24 - 11/15/2021 =
* Added note to Salesforce setup panel regarding completing the installation of the OAuth app
* Improved - Applying tags with Bento will now trigger events using the `add_tag_via_event` command (thanks @jessehanley)
* Fixed EDD Email Optin tags getting applied regardless of email optin consent checkbox being checked
* Fixed PHP warning when using Uncanny Toolkit Pro and FluentCRM or Groundhogg
* Developers - The active CRM object is now passed by reference via the `wp_fusion_init_crm` action and [can be operated on](https://wpfusion.com/documentation/advanced-developer-tutorials/how-to-use-a-custom-client-id-for-authentication/#using-a-custom-client-id-and-authorization-url)

= 3.38.23 - 11/8/2021 =
* Added `IN` and `NOT IN` comparisons [to the `user_meta_if` shortcode](https://wpfusion.com/documentation/getting-started/shortcodes/#in-and-not-in)
* Added Apply Tags - Trialling and Apply Tags - Converted to EDD Recurring Payments integration
* Added Export to CSV button to Activity Logs
* Improved - Mailchimp Audience select box is moved to the Setup tab and fields and tags can be loaded for a new audience without having to save the settings first
* Improved - Mailchimp setup will now show a warning if you try to connect and there are no audiences in your account
* Improved - Added a notice to the logs when a new ConvertKit subscriber is being created with a random tag due to no default tag being set
* Fixed WooCommerce order status changes in the admin list table not applying tags when Asynchronous Checkout was enabled
* Fixed LearnDash course progress tags not being applied when the Autocomplete Lessons & Topics Pro module was enabled in Uncanny Toolkit Pro for LearnDash
* Fixed MemberPress Memberships Statuses batch operation not applying tags for cancelled, trial, and expired subscription statuses
* Fixed Subscription Cancelled tags not be applied with MemberPress when a subscription is cancelled after its expiration date
* Fixed new users registered via Gravity Forms User Registration not being synced during an auto-login session
* Fixed Intercom rejecting new subscribers without a last name
* Fixed `unknown class FrmRegEntryHelper` error when registering new users on older versions of Formidable Forms
* Fixed PHP warning loading subscriber with no tags from Intercom
* Fixed upgrade to 3.38.22 not setting autoload = yes on `wpf_taxonomy_rules`, which made content protected by taxonomy rules un-protected until saved again
* Developers - Added `wpf_woocommerce_subscription_sync_fields` filter
* Developers - Added function `wpf_get_current_user_email()`

= 3.38.22 - 11/1/2021 =
* Improved performance with checking post access against taxonomy term restrictions
* Improved - If a field type is set to multiselect and it is stored as a comma-separated text value, the value will be synced as an array with supported CRMs
* Improved - If a page using an auto-login query string (?cid=) is refreshed, for example due to a form submission, this will no longer force reload the contact's tags from the CRM
* Improved Zoho error handling
* Fixed tags linked to BuddyBoss profile types not being assigned during registration when new user accounts are auto-activated
* Fixed restricted LearnDash lessons not being hidden by Filter Course Steps in Focus Mode with the BuddyBoss theme
* Fixed Lock Lessons with LearnDash outputting lock icon on lessons that were already locked by LearnDash core

= 3.38.21 - 10/26/2021 =
* Fixed all content being protected when no term taxonomy rules were set since 3.38.20

= 3.38.20 - 10/26/2021 =
* Fixed SQL warning checking term access restrictions since 3.38.17
* Fixed `wpf_salesforce_auth_url` filter (for connecting to sandboxes) not working with new OAuth integration from 3.38.17
* Fixed WP Affiliate Manager integration not applying Approved tags when affiliates are auto-approved at registration

= 3.38.19 - 10/25/2021 =
* Fixed error with WP Remote Users Sync `Cannot redeclare WPF_WP_Remote_Users_Sync::$slug`

= 3.38.18 - 10/25/2021 =
* Fixed error with Advanced Ads `Cannot redeclare WPF_Advanced_Ads::$slug`
* Fixed - Infusionsoft integration will force all numeric values to sync as text to get around "java.lang.Integer cannot be cast to java.lang.String" errors

= 3.38.17 - 10/25/2021 =
* **Added Salesforce OAuth integration - Salesforce users will need to go to the WP Fusion settings page one time and grant OAuth permissions to use the new API**
* Added setting to apply tags when a review is left on a WooCommerce product
* Added option to sync total points earned on a LearnDash quiz to a custom field in the CRM
* Improved - When using Filter Queries - Advanced, posts protected by taxonomy terms will be properly excluded
* Improved performance for Filter Queries with Elementor posts lists
* Improved - If "Create contacts for new users" is disabled, a WooCommerce checkout by a registered user will now correctly apply the product tags directly to the contact record in the CRM
* Improved - Removed "old" WooCommerce asynchronous checkout processor via WP Background Processing in favor of an AJAX request bound to the successful payment response from the gateway
* Improved - If the LearnDash - WooCommerce plugin triggers an enrollment into a course or group which results in tags being applied, this will be indicated in the logs
* Improved - Slowed down batch exporter with Bento to get around API throttling
* Improved - When bulk editing more than 20 WooCommerce orders in the admin, WP Fusion will bypass applying any tags to avoid a timeout
* Fixed fatal error `undefined method FacetWP_Settings::get_field_html()` in FacetWP 3.9
* Fixed read only lists not showing on admin user profile with HubSpot since 3.38.16
* Fixed Infusionsoft not loading more than 1000 available tags per category
* Fixed custom fields not syncing when creating a new Bento contact
* Fixed 429 / "API limits exceeded" errors not being logged with Bento
* Fixed Salesforce automatic access token refresh failing when the password contains an ampersand
* Developers — Added `track_event()` method to supported CRMs in advance of the new Event Tracking addon

= 3.38.16 - 10/18/2021 =
* Added support for syncing to Date/Time fields with Keap and Infusionsoft
* Added option to sync LearnDash course progress percentage with a custom field in the CRM
* Added JetEngine integration
* Improved - Read-only tags and lists will no longer show up in Apply Tags dropdowns (only Required Tags dropdowns)
* Improved - If a user is auto-enrolled into a course via a linked tag, the tags in the Apply Tags - Enrolled setting will now be applied. This can be used in an automation to confirm that the auto-enrollment was successful
* Improved - Dates displayed with the [[user_meta]] shortcode will now use the site's current timezone
* Improved - WP Remote Users Sync integration will no longer sync tag changes to a remote site when they've just been loaded from a remote site (safeguard against infinite loops)
* Improved - WP Remote Users Sync integration will not send updated tags to remote sites more than once per pageload
* Improved - A successful API response from Drip for a subscriber will remove the Inactive badge in the admin
* Fixed not being able to de-select a selected pipeline and stage for ecommerce deals in the WooCommerce Order Status Stages section of the WP Fusion settings
* Fixed automatic WooCommerce Subscriptions duplicate site detection not working
* Fixed Prevent Reapplying Tags setting not being respected
* Fixed an empty API response from Drip marking users as Inactive
* Fixed fatal error "Too few arguments to function" when applying BuddyBoss profile type tags since 3.38.14
* Fixed error syncing array values with Sendinblue
* Fixed Sendinblue error "attributes should be an object" when syncing data without any custom fields
* Fixed PHP notice "Trying to access array offset on value of type null" in Uncanny LearnDash Groups integration during group member enrollment

= 3.38.15 -10/11/2021 =
* Added Emercury site tracking
* Added safety checks against infinite loops when using LearnDash and BuddyBoss auto-enrollments in conjunction with the Group Sync feature
* Fixed bug since 3.38.14 that could cause content to become restricted if it was associated with a deleted taxonomy term
* Fixed HTML not saving in the Default Restricted Content Message since 3.38.0
* Fixed empty date fields being synced as 0 which could evaluate to January 1st 1970 in some CRMs
* Fixed WooCommerce Product Addons integration not syncing Quantity type fields
* Fixed WooCommerce Product Addons integration not syncing Text type fields
* Fixed Async Checkout (New) for WooCommerce applying tags for On Hold orders (i.e. BACS)
* Fixed dynamic tags with a text prefix not getting automatically removed when a WooCommerce order is refunded
* Fixed WPF trying (and failing) to unenroll BuddyPress group moderators from groups when they were missing the group member linked tag
* Fixed WPF settings not saving in CPT-UI since CPT-UI v1.10.0
* Developers - Added function `wpf_clean_tags()` (same as `wpf_clean()` but allows special characters)

= 3.38.14 - 10/5/2021 =
* Added panel in the WP Fusion settings showing the loaded integrations, with links to the documentation for each
* Improved Mailchimp API performance when loading available tags
* Fixed error `Uncaught Error: Class 'WPF_Plugin_Updater' not found` conflict with WPMU Dev Dashboard v4.11.4
* Fixed "Failed to apply tags - no contact ID" message when a registered user without a contact record filled out a form
* Fixed special characters getting synced to the CRM HTML encoded since 3.38.0
* Fixed Filter Course Steps with LearnDash not working when Shared Course Steps was off
* Fixed category-based tag access rules not working
* Fixed BuddyPress XProfile updates not syncing since BuddyPress v9.1.0
* Fixed linked tags not being removed from the previous profile type when switching a user's profile types in BuddyBoss
* Fixed form submissions during an auto-login session not updating the correct contact record when there was no email address on the form
* Fixed error with Gravity Forms when using "Create tag(s) from value" on a form field and no tags had been configured generally for the feed
* Fixed custom fields not syncing with FooEvents when the customer who purchased the ticket is also an attendee
* Fixed Salesforce integration not accepting a new security token until Refresh Topics and Fields was pressed
* Fixed import tool with Drip not importing unsubscribed subscribers
* Fixed import tool with Drip not importing more than 1000 subscribers
* Fixed countries with e-acute symbol in their name not syncing to the Country field with Infusionsoft
* Fixed date values before 1970 not being synced correctly
* Fixed PHP notice Undefined index: step_display in LearnDash integration

= 3.38.13 - 9/22/2021 =
* Fixed Divi modules not respecting tag access rules with Divi Builder 4.10.8+

= 3.38.12 - 9/21/2021 =
* Improved WP Remote Users sync integration (can now detect tag changes that aren't part of a profile update)
* Fixed updated tags not loading from CRM, since 3.38.11

= 3.38.11 - 9/20/2021 =
* Added [WooCommerce Payment Plans integration](https://wpfusion.com/documentation/ecommerce/woocommerce-payment-plans/)
* Improved - Filter Course Steps for LearnDash should now be a lot more reliable in terms of course step counts and progress tracking
* Improved - If a WooCommerce Memberships membership plan is transferred to another user, the tags will be updated for both the previous and new owners
* Added import tool support for Groundhogg (REST API)
* Added support for loading multiselect data from Copper
* Removed "Enable Notifications" setting from ConvertKit integration, in favor of the global "Send Welcome Email" setting
* Maropost bugfixes
* Updated Copper API URL
* Fixed access checks sometimes failing when using tag names with HTML special characters in them
* Fixed a bug whereby LearnDash lessons could become detached from a course if LearnDash tried to rebuild the course steps cache while the Restricted Content Message was being displayed in place of the course content
* Fixed custom fields not syncing with Bento
* Fixed multiselect data not syncing to Copper
* Fixed checkbox data not syncing to Copper
* Fixed PHP warning in Emercury integration

= 3.38.10 - 9/13/2021 =
* Added Groundhogg (REST API) CRM integration
* Added [Simply Schedule Appointments integration](https://wpfusion.com/documentation/events/simply-schedule-appointments/)
* Added option to disable the sync of guest bookings with Events Manager
* Improved - Events Manager dates and times will now be synced in the timezone of the event, not UTC
* Fixed initial REST authentication (Groundhogg, FluentCRM, Autonami) sometimes breaking if there was a trailing slash at the end of the REST URL
* Fixed lookups for ActiveCampaign Deep Data customer IDs sometimes failing (email address in URL wasn't URL encoded)
* Fixed import by tag with ActiveCampaign sometimes importing contacts with the wrong tag ID when the search string matched multiple tags
* Fixed WP Fusion blocking Events Manager registrations when there was an API error creating the attendee contact record
* Fixed ACF return formats not being respected for dates when using a Push User Meta operation
* Fixed - Salesforce dates will now be formatted using gmdate() instead of date() (fixes some time zone issues)
* Fixed - Updated Maropost API calls to use SSL API endpoint
* Fixed admin override not working correctly in wpf_user_can_access() when checking the access for a different user (since 3.38.5)

= 3.38.9 - 9/7/2021 =
* Added [Download Monitor integration](https://wpfusion.com/documentation/other/download-monitor/)
* Added [BuddyBoss group organizer linked tag option](https://wpfusion.com/documentation/membership/buddypress/#group-organizer-auto-assignment)
* Improved - Clicking Process WP Fusion Order Actions Again on a WooCommerce order which contains a subscription renewal will also sync any enabled subscription fields
* Improved - HubSpot's site tracking script is now disabled on the WooCommerce My Account page, to prevent the script from trying to sync account edits with the CRM
* Fixed tags with > and < symbols getting loaded from the CRM HTML-encoded
* Fixed PHP warning in class WPF_User when registering a new user with no first or last name
* Fixed Maropost webhooks not working since 3.38.0

= 3.38.8 - 9/1/2021 =
* Fixed parse error in LearnDash integration on some PHP versions since 3.38.5
* Fixed form integrations not applying tags if no fields were enabled for sync since 3.38.5
* Fixed Incomplete Address error with Mailchimp when syncing United States of America as the country, but not specifying a state
* Updated EDD updater to v1.9

= 3.38.7 - 8/31/2021 =
* Fixed apply tags via AJAX endpoints resulting in a 403 error since 3.38.0, with Media Tools and other addons
* Improved logging with Drip, when an email address is changed to an address that already has a subscriber record
* Fixed PHP warning in the admin when editing a page that has child pages

= 3.38.6 - 8/30/2021 =
* Fixed PHP notice in LearnDash integration since 3.38.5

= 3.38.5 - 8/30/2021 =
* Added [EventON integration](https://wpfusion.com/documentation/events/eventon/)
* Added support for [Bento webhooks](https://wpfusion.com/documentation/webhooks/bento-webhooks/)
* Added [Pay Per Post tagging with WishList Member](https://wpfusion.com/documentation/membership/wishlist-member/#pay-per-post-tagging)
* Added Login With AJAX integration (login redirects will now work with the Return After Login setting)
* Improved - When a contact ID is recorded in the logs, it will include a link to edit that contact in the CRM
* Improved - It's no longer necessary to enable Set Current User to pre-fill Gravity Forms fields with auto-login user data
* Improved LearnDash course settings admin layout
* Fixed - Removed `wp_kses_post()` on restricted content message (was breaking login forms)
* Fixed `http_request_failed` errors from the WordPress HTTP API not being logged as errors
* Fixed PHP warning loading custom fields from Bento
* Fixed PHP warning in wpForo integration
* Fixed fatal error syncing avatars to the CRM from the BuddyBoss app
* Fixed Users Insights search only running on first page of results
* Fixed FooEvents Zoom URL not syncing
* Fixed fatal error in HubSpot integration when using site tracking and an API error was encountered trying to get the tracking ID
* Fixed `Fatal error: Cannot declare class AC_Connector` since 3.38.0
* Fixed memory leak with WPML, post category archives, and the Exclude Administrators setting
* Fixed Ontraport integration not creating new contacts with missing emails (even though Ontraport allows contacts to not have an email address)
* Developers: Added filter `wpf_wp_kses_allowed_html`
* Developers: Data loaded from the CRM will now be passed through `wp_kses_post()` instead of `sanitize_text_field()` (since 3.38), to permit syncing HTML inside of custom fields
* Fixed missing second argument `$force_update` in `wpf_get_contact_id()`

= 3.38.4 - 8/23/2021 =
* Added [Bento marketing automation](https://wpfusion.com/go/bento/) integration
* Fixed updates to existing contacts not working with Klaviyo
* Fixed Bulk Edit box not appearing on LifterLMS lessons
* Fixed JavaScript error with Resync Tags button on admin user profile
* Fixed serialized data not being unserialized during a Push User Meta operation
* Fixed parse error in MemberPress integration on some PHP versions
* Developers: Fixed `wpf_get_contact_id()` sometimes returning an empty string instead of `false` when a contact record wasn't found

= 3.38.3 - 8/19/2021 =
* Improved - Stopped setting 'unknown' for missing Address 2, Country and State fields with Mailchimp
* Fixed webhooks not working with Salesforce since 3.38.0
* Fixed links not displaying in the activity logs since 3.38.0
* Fixed syntax error with some PHP configurations since 3.38.0
* Fixed PHP warning in Infusionsoft integration

= 3.38.2 - 8/18/2021 =
* Fixed error `Call to undefined function get_current_screen()` since 3.38.0 when performing some admin actions
* Fixed warning about missing redirect showing on LearnDash lessons where the redirect was configured on the parent course

= 3.38.1 - 8/17/2021 =
* Fixed auto-login links not working since 3.38.0
* Fixed custom fields not syncing during MemberPress registration since 3.38.0
* Fixed Defer Until Activation setting not working with signups from the BuddyBoss app
* Developers: Removed WPF_* prefix from 3rd party CRM SDK classes (to comply with wordpress.org plugin guidelines)

= 3.38.0 - 8/16/2021 =

** Heads up! ** This update includes a significant refactor of WP Fusion's admin settings, interfaces, and database storage. We've tested the update extensively, but with 3,500+ changes across 200+ files, there are potentially bugs we've missed. If that sounds scary, you may want to wait until v3.38.1 is released before updating.

If there are bugs, they will most likely affect saving WP Fusion settings in the admin (general settings, access rules, product configurations, etc.) and not affect the existing access rules or sync configuration on your site.

* Big cleanup and refactoring with improvements for security, internationalization, and documentation
* Added [If-So Dynamic Content integration](https://wpfusion.com/documentation/other/if-so/)
* Added support for syncing the [Zoom meeting ID and join URL with FooEvents](https://wpfusion.com/documentation/events/fooevents/#zoom)
* Added View in CRM URL for Jetpack CRM
* Added GDPR Consent Date, Agreed to Terms Date, and Marketing Consent Date fields for sync with Groundhogg
* Improved - Guest registrations will log whether a contact is being created or updated
* Fixed XProfile fields First Name and Last Name not syncing during a new BuddyBoss user registration
* Fixed filtering by CRM tag not working in Users Insights
* Fixed user profile updates overwriting Jetpack CRM contacts
* Fixed initial default field mapping not being stored after setup until the settings were saved the first time
* Fixed logs getting flushed when hitting Enter in the pagination box
* Fixed expiration date not being synced after a Restrict Content Pro renewal
* Fixed bbPress forum archive not being protected when Filter Queries was on
* Deleted unused XMLRPC modules in the Infusionsoft iSDK
* Developers: Added function `wpf_get_option()` (alternative for `wp_fusion()->settings->get()`)
* Developers: Added sanitization functionn `wpf_clean()`
* Developers: Deprecated `wp_fusion()->settings->get_all()`
* Developers: Changed `wp_fusion()->settings->set_all( $options )` to `wp_fusion()->settings->set_multiple( $options )`

= 3.37.31 - 8/9/2021 =
* Added [RestroPress integration](https://wpfusion.com/documentation/ecommerce/restropress/)
* Added [Import Trigger tag option for Jetpack CRM ](https://wpfusion.com/documentation/webhooks/jetpack-crm-automatic-imports/)
* Added option to [sync LearnDash quiz scores to a custom field in the CRM](https://wpfusion.com/documentation/learning-management/learndash/#quizzes)
* Added support for WPForms User Registration addon
* Added Picture URL field for sync with CapsuleCRM
* Added nonce verification to Flush All Logs button (improved security)
* Improved - Logs will contain a link to edit the contact record in the CRM after a form submission
* Improved - If Add Attendees is enabled for a Tribe Tickets RSVP ticket, and a registered user RSVPs with a different email address, a new contact record will be created (rather than updating their existing contact record)
* Fixed Ultimate Member `role_select` and `role_radio` fields not syncing during registration
* Fixed Gravity Forms Nested Feeds processing not respecting feed conditions
* Fixed custom fields not syncing with Maropost
* Fixed PHP warning updating contacts with Intercom
* Fixed LearnPress course enrollment tags not being applied when there were multiple course products in an order
* Fixed console errors in the Widgets editor since WP 5.8
* Fixed search input not being auto-focused in CRM field select dropdowns with jQuery 3.6.0
* Developers: Added helper function `WPF_Admin_Interfaces::sanitize_tags_settings( $settings ); for sanitizing tag multiselect data in metaboxes before saving to postmeta
* Developers: Improved sanitization of meta box data in admin

= 3.37.30 - 8/2/2021 =
* Added View In CRM links (direct link to the contact record) for all CRMs that support it
* Added [email optin checkbox and optin tagging for Easy Digital Downloads](https://wpfusion.com/documentation/ecommerce/easy-digital-downloads/#email-optins)
* Added support for [FluentCRM webhooks](https://wpfusion.com/documentation/webhooks/fluentcrm-webhooks/)
* Added [email optin setting to GiveWP integration](https://wpfusion.com/documentation/ecommerce/give/#email-optins)
* Added Job Title field for sync with Capsule CRM
* Improved - Added notice to the setup screen with information on how to connect to Autonami on the same site
* Improved - Added warning for admins when viewing a post that has access rules enabled, but no redirect specified
* Fixed Capsule CRM not loading more than 100 tags
* Fixed Events Manager bookings batch operation not detecting past bookings
* Fixed Events Manager bookings batch operation not exporting more than 20 bookings
* Fixed Events Manager not syncing guest bookings
* Fixed Elementor Forms integration treating some country names as dates
* Fixed undefined index PHP warning loading data from ActiveCampaign
* Fixed "Invalid email address" error with Mailerlite when Email Changes setting was set to Duplicate
* Fixed course enrollment tags not being applied when a LearnPress course was purchased using the WooCommerce Payment Methods Integration extension

= 3.37.29 - 7/26/2021 =
* Added Appointment Time field for sync with WooCommerce Appointments
* Added [event category tagging for Events Manager events](https://wpfusion.com/documentation/events/events-manager/#event-category-tagging)
* Added additional YITH WooCommerce Vendors fields for sync
* Improved - Wildcards * can now be used in the Allowed URLs setting for Site Lockout
* Improved - If a Gravity Forms email field is mapped to the primary CRM email address field, this will take priority over other email fields on the form
* Fixed "Hide if access is denied" setting not working with wpForo categories and some forum layouts
* Fixed GP Nested Forms feeds not running when there was no feed configured on the parent form
* Fixed email address changes for existing contacts not working with Autonami
* Fixed error syncing array formatted data to Intercom
* Fixed PHP warnings in the MemberMouse integration
* Fixed custom fields not syncing during a WP Ultimo registration
* Developers: Added `wpf_get_lookup_field()` function for getting the API name of the CRM field used for contact lookups (usually Email)
* Developers: Added `wpf_infusionsoft_safe_fields` filter (strips out Asian characters loaded over the API in field names to prevent XML parse errors)
* Developers: Added `wpf_beaver_builder_access_meta` filter

= 3.37.28 - 7/19/2021 =
* Fixed new contacts created with Autonami not being opted in to receive emails
* Fixed fatal error with Klick-Tip when making API calls using expired credentials

= 3.37.27 - 7/19/2021 =
* Added Event Categories field for sync with Events Manager
* Improved - Comments forms will be pre-filled with the temporary user's details during an auto-login session
* Improved - Booking dates will be formatted using the sitewide datetime format (set Settings > General) with WooCommerce Bookings and WooCommerce Appointments, when the field format is set to `text`
* Improved - Form submissions will record the page URL of the form in the logs
* Improved - If a field type is set to `text` then arrays will be converted to comma-separated strings for syncing
* Fixed &send_notification=false in a webhook URL triggering the new user welcome email
* Fixed datetime fields being synced to ActiveCampaign in 12h format (fixed to 24h format)
* Fixed fatal error trying to sync multidimensional arrays to the CRM
* Developers - added `wpf_get_users_with_tag( $tag )` function
* Developers - added `wpf_get_datetime_format()` function and `wpf_datetime_format` filter

= 3.37.26 - 7/12/2021 =
* Added Autonami CRM integration
* Added [Upsell Plugin integration](https://wpfusion.com/documentation/ecommerce/upsell-plugin/)
* Added [WooCommerce Memberships for Teams team meta batch operation](https://wpfusion.com/documentation/membership/teams-for-woocommerce-memberships/#syncing-historical-data)
* Improved - Stopped "Unknown Lists" from being loaded from HubSpot
* Fixed CSS classes getting removed from LearnDash lessons in focus mode since v3.37.25
* Fixed profile updates in the BuddyBoss app not syncing to the CRM
* Fixed default fields not being enabled for sync in the settings after first setting up the plugin
* Fixed PHP notice on WooCommerce order received page
* Fixed post types created with Toolset Types bypassing access rules
* Developers - Added wpf_get_tags() function
* Developers - Added action wpf_meta_box_content_{$post->post_type}
* Developers - All Beaver Builder nodes will pass through the wpf_beaver_builder_can_access filter, regardless of if they're protected by WP Fusion or not
* Developers - Refactored user_can_access() function for better performance and readability

= 3.37.25 - 7/6/2021 =
* Added support for LifterLMS Custom Fields addon
* Added support for applying tags with the default (site title) course with WPComplete
* Added Events Manager Registrations batch operation
* Added lock icon on LearnDash lessons that are protected by the Filter Course Steps setting
* Improved - If a FooEvents order is refunded, any tags applied to event attendees will automatically be removed
* Improved - Custom leadsource tracking variables registered via the wpf_leadsource_vars filter will show up on the Contact Fields list automatically
* Fixed fields not being synced when a WooCommerce Subscriptions subscription was renewed early
* Fixed MemberMouse settings page only listing 10 membership levels
* Fixed unique_id not showing up for sync with Ontraport
* Fixed WPComplete integration not detecting courses on custom post types
* Fixed fatal error sending social group invites with BuddyBoss when the Platform Pro plugin wasn't active
* Developers: Removed third parameter $user_tags from wpf_beaver_builder_can_access filter (for consistency with other page builders)
* Developers: Added [wpf_disable_crm_field_select4 and wpf_disable_tag_select4 filters](https://wpfusion.com/documentation/faq/performance/#admin-performance)

= 3.37.24 - 6/28/2021 =
* Added [Event Tickets attendees batch operation](https://wpfusion.com/documentation/events/the-events-calendar-event-tickets/#exporting-attendees)
* Added indicator in the logs when a pseudo field or read only field (i.e. user_registered) has been loaded from the CRM
* Added unique_id field for sync with Ontraport
* Added support for syncing user data from Advanced Custom Fields: Extended frontend forms
* Added Owner ID field for sync with Intercom
* Added Google Analytics fields for sync with Intercom
* Added indicator for email optin status to WooCommerce order sidebar meta box
* Improved - Contact fields settings will default to suggested usermeta / CRM field pairings
* Improved site tracking with Mautic after guest form submission
* Fixed the default owner for new Zoho contacts overriding a custom owner
* Fixed Apply Tags - Assignment Uploaded setting not saving on LearnDash lessons
* Fixed fatal error in admin with WooFunnels 1.5.0
* Fixed fatal error since v3.37.23 with BuddyBoss and registering a new user via MemberPress, when the Limit User Roles setting was active
* Changed WooCommerce function order_has_contact() to get_contact_id_from_order()

= 3.37.23 - 6/21/2021 =
* Added notification badge on WP Fusion Logs admin menu item to indicate when there are unseen API errors in the logs
* Added logging when a site tracking session has been started for a guest, for ActiveCampaign, HubSpot, and EngageBay
* Added Designation field for sync with FooEvents
* Improved - If the Limit User Roles setting is in use, and a user without a CRM contact record has their role changed to a valid role, a new contact record will be created in the CRM
* Fixed linked tags from LifterLMS courses being applied when a student was added to a membership that contains that course
* Fixed custom fields not syncing with FooEvents v5.5+ (Note: you will need to re-map any custom attendee fields in the WP Fusion settings)
* Fixed WooCommerce Memberships integration not applying tags for membership status when user memberships were edited in the admin
* Fixed async=true in an `update` webhook not loading the user's tags
* Fixed PHP warning in the PulseTechnologyCRM integration
* Fixed fatal error loading WooFunnels custom checkout fields for the WP Fusion settings with WooFunnels 1.4.2
* Removed wp_fusion()->access->can_access_terms cache (was causing more trouble than it was worth)

= 3.37.22 - 6/14/2021 =
* Added support for [auto-applied discounts with Easy Digital Downloads](https://wpfusion.com/documentation/ecommerce/easy-digital-downloads/#auto-applied-discounts)
* Improved - Return after login cookies will now be set if access is denied and the restricted content message is shown (previously it only worked after a redirect)
* Fixed auto-login loading the user's tags on every page load
* Fixed settings fields not showing on Easy Digital Downloads discounts
* Fixed Gravity Forms feed setting "Add to Lists" not saving correctly since Gravity Forms 2.5
* Fixed Push User Meta and Pull User Meta batch operations not working since v3.37.21
* Fixed +1 as country code option with Elementor Forms being synced to the CRM as a checkbox
* Fixed fatal error when enabling ActiveCampaign site tracking while WP Fusion is in staging mode
* Fixed PHP warning syncing array values with HighLevel
* Fixed PHP notices in Groundhogg integration
* Added wpf_get_users_with_contact_ids() function

= 3.37.21 - 6/7/2021 =
* Added [Ninja Forms entries batch export tool](https://wpfusion.com/documentation/lead-generation/ninja-forms/#syncing-historical-entries)
* Added [PulseTechCRM integration](https://thepulsespot.com/)
* Added a Send Welcome Email option in the Imported Users settings
* Added WP Fusion icon to Gravity Forms settings menu
* Fixed Gravity Perks Nested Forms feeds not being processed when the main form feed was processed
* Fixed Members integration trying to apply linked tags during registration before the user had been synced to the CRM
* Fixed multi-checkbox fields not syncing from Event Tickets Plus attendee registrations
* Fixed fatal error with Drip SDK and PHP 8

= 3.37.20 - 5/31/2021 =
* Added subscription failed tagging to GiveWP integration
* Added Affiliate Rejected tagging option to AffiliateWP
* Fixed Last Course Completed Date and Last Lesson Completed Date not syncing correctly with LearnDash
* Fixed LearnDash tags not being applied with Uncanny Toolkit's Autocomplete Lessons feature
* Fixed being unable to deactivate the license key if the license had never been activated on the current site
* Developers: removed register_shutdown_function() in API queue in favor of the "shutdown" WordPress action

= 3.37.19 - 5/24/2021 =
* Added [WS Form integration](https://wpfusion.com/documentation/lead-generation/ws-form/)
* Added support for [WooFunnels custom checkout fields](https://wpfusion.com/documentation/ecommerce/woofunnels/#custom-checkout-fields)
* Added option to apply tags when an Event Tickets attendee is deleted from an event
* Added error message when connecting to FluentCRM (REST API) and pretty permalinks aren't enabled on the CRM site
* Added option with WooFunnels to run WP Fusion actions on the Primary Order Accepted status rather than waiting for completed
* Improved - If you have more than 1,000 tags, they will be loaded in the admin via AJAX when you focus on the dropdown (improves admin performance)
* Improved site tracking with EngageBay (logged-in users will now be identified to the tracking script by email address)
* Improved reliability of license activation and deactivation (changed requests to GET to get past CloudFlare's firewall)
* Fixed Event Tickets treating the first attendee email field as the attendee's email address, even if it wasn't enabled for sync
* Fixed WP Fusion settings not saving on new Event Tickets tickets
* Fixed Tickera integration syncing attendees for pending orders
* Fixed Tickera integration not syncing attendees if "Show E-mail for Option For Ticket Owners" was disabled
* Fixed conflict with YITH WooCommerce Frontend Manager trying to access WP Fusion product settings from the frontend
* Developers: wp_fusion_init action will now only fire if WP Fusion is connected to a CRM

= 3.37.18 - 5/17/2021 =
* Added [Tickera integration](https://wpfusion.com/documentation/events/tickera/)
* Added [Give Gift Aid integration](https://wpfusion.com/documentation/ecommerce/give/#gift-aid)
* Fixed error connecting to FluentCRM (REST API) when there were no tags created in FluentCRM
* Fixed PHP warning trying to apply tags on view for deleted taxonomy terms
* Added wp_fusion_hide_upgrade_nags filter

= 3.37.17 - 5/14/2021 =
* Continued bugfixes for Elementor Pro Forms v3.2.0 compatibility — entries from pre-Elementor-3.2 forms sync correctly again, but if you edit the form in Elementor you will still need to re-do the field mapping
* Improved upgrade nags with WP Fusion Lite
* Improved - Moved Lite-specific functionality into class WPF_Lite_Helper
* Fixed PHP warning in FluentCRM REST API integration

= 3.37.16 - 5/12/2021 =
* Fixed tags not applying with FluentCRM since v3.37.14
* Fixed PHP warning in The Events Calendar month view

= 3.37.15 - 5/11/2021 =
* Fixed fatal error with BuddyPress (not BuddyBoss) when updating profiles, from v3.37.14
* Fixed Elementor Forms field maps not saving on new forms
* EngageBay bugfixes

= 3.37.14 - 5/10/2021 =
* Added [FluentCRM (REST API) CRM integration](https://wpfusion.com/plugin-updates/introducing-fluentcrm-rest-api/)
* Added [WooFunnels integration](http://wpfusion.com/documentation/ecommerce/woofunnels/)
* Added support for syncing the WooCommerce Appointments appointment date when an appointment status is changed to Pending or Confirmed
* Added notice to the logs when Filter Queries is running on more than 200 posts of a post type in a single request
* Improved WP Simple Pay logging for subscriptions
* Fixed edits to custom fields in FluentCRM not being synced back to the user record automatically
* Fixed First Name and Last Name fields not syncing with BuddyPress frontend profile updates if the XProfile fields hadn't been enabled for sync
* Fixed Gifting for WooCommerce Subscriptions integration setting the name of the gift recipient to the name of the purchaser
* Fixed "Remove tags from customer" setting being treated as enabled by default in Gifting for WooCommerce Subscriptions integration
* Fixed error loading Elementor Pro editor on sites that hadn't yet updated to Elementor Pro v3.2.0+
* Fixed WooCommerce Memberships batch operation getting hung up on deleted memberships
* Fixed EngageBay add tag / remove tag API endpoints
* Fixed fatal error trying to apply tags to a deleted FluentCRM contact
* Added action [wp_fusion_init](https://wpfusion.com/documentation/actions/wp_fusion_init/)
* Added action wp_fusion_init_crm

= 3.37.13 - 5/3/2021 =
* Added Payment Failed and Subscription Cancelled tagging options to WP Simple Pay integration
* Added Subscription End Date field for sync with WooCommerce Subscriptions
* Improved - user_registered will now be synced back to the CRM after a user is imported via webhook (if enabled)
* Improved - Removed "read only" indicator from HubSpot list name and included it in a label in the select box instead
* Fixed unwanted user meta getting synced back to the CRM when importing users if Push All was enabled
* Fixed feed settings not saving with Gravity Forms 2.5+
* Fixed Next Payment Date not being synced after a successful WooCommerce Subscriptions renewal
* Fixed Elementor Forms integration broken since Elementor Pro v3.2.0 (removed implementation of Fields_Map::CONTROL_TYPE) thanks @techjewel
* Fixed BuddyBoss group invites not working when WP Fusion was in use for groups member access controls
* Added wpf_event_tickets_attendee_data filter

= 3.37.12 - 4/26/2021 =
* Added Auto Login debug mode
* Added support for syncing Gravity Forms meta fields (Embed URL, Entry URL, Form ID, etc) with the CRM
* Added LearnDash Groups Enrollment Statuses batch operation d
* Added LearnDash Course Progress batch operation
* Added WooCommerce Memberships meta batch operation
* Improved - If Return After Login is enabled, and a form submission starts an auto-login session, the redirect will be triggered (Elementor Forms and Gravity Forms)
* Fixed Paid Memberships Pro Approval status not syncing when edited on the admin user profile
* Fixed pmpro_approval field not being picked up by Push User Meta

= 3.37.11 - 4/19/2021 =
* Added support for syncing ACF image fields to the CRM as image URLs instead of attachment IDs
* Improved support for syncing phone numbers with HighLevel
* Reverted change from 3.37.7 - bbPress topics will now use the query filtering mode set in the settings, rather than defaulting to Advanced (for improved performance)
* Fixed Paid Memberships Pro approval_status field not syncing when a membership level was changed
* Fixed "The link you followed has expired" message when deleting users, with Members active

= 3.37.10 - 4/15/2021 =
* Fixed infinite loop when loading bbPress forums index with Filter Queries set to Advanced and Restrict Forum Archives enabled

= 3.37.9 - 4/15/2021 =
* Fixed tags loaded via webhook not triggering automated enrollments since v3.37.8
* Added WP Fusion status metabox to WooCommerce order sidebar
* Added Add Only option to Contact Form 7 integration
* Improved - user_email and user_pass will no longer be loaded from the CRM during login if Login Meta Sync is enabled
* Improved error handing with HubSpot
* Improved - Filter Queries / Advanced will now limit the post query to the first 200 posts of each post type (for improved performance)
* Improved - Filter Queries will be bypassed while WP Fusion is processing a webhook
* Updated EngageBay API URL
* Fixed an empty last_name field at registration defaulting to last_updated (with FluentCRM)
* Fixed fatal error trying to install addon plugins before setting up the CRM API connection

= 3.37.8 - 4/12/2021 =
* Added Emercury CRM integration
* Added support for Easy Digital Downloads 3.0-beta1
* Added a notice to the LearnDash course and group settings panels when the LearnDash - WooCommerce integration plugin is active
* Improved support for Advanced Custom Fields (ACF) date fields
* Improved - If a license key is defined in wp-config.php using WPF_LICENSE_KEY then the site will be auto-activated for updates
* Improved - User-entered fields on the Contact Fields list will now show under their own heading
* Fixed BuddyBoss member type field not syncing during a Push User Meta operation
* Fixed special characters in MemberPress membership level names being synced to the CRM as HTML entities
* Fixed Resync Tags batch operation getting hung up with Ontraport trying to load the tags from a deleted contact
* Fixed fatal error error handling error-level HTTP response code with NationBuilder
* Fixed Capsule not loading more than 50 tags

= 3.37.7 - 4/5/2021 =
* Added WISDM Group Registration for LearnDash integration
* Added support for syncing date-type fields with Elementor forms
* Added support for Filter Queries with The Events Calendar events
* Added support for Filter Queries - Advanced with bbPress topics
* Added WP Fusion logo to Gravity Forms entry note
* Improved Filter Queries performance
* Fixed Filter Queries - Standard not working on search results
* Fixed HTTP API logging not working with MailJet
* Fixed MailJet treating Contact Not Found errors as irrecoverable
* Fixed Email Optin tags not being applied with WooCommerce integration
* Fixed duplicate State field with HighLevel
* Fixed Give donations_count and total_donated fields not syncing accurately during the first donation

__Developers:__
* Re-added wp_fusion()->access->can_access_posts cache
* Added wpf_query_filter_get_posts_args filter
* Added wpf_is_post_type_eligible_for_query_filtering filter
* Added wpf_should_filter_query filter
* Improved - Third parameter ($post_id) to wpf_user_can_access filter will now be false if the item being checked is not a post
* Changed wpf_user_id filter to wpf_get_user_id
* Removed wpf_bypass_filter_queries filter (in favor of wpf_should_filter_query)
* Fixed PHP notices in class-access-control.php

= 3.37.6 - 4/1/2021 =
* Removed wp_fusion()->access->can_access_posts cache (was causing a lot of access problems, needs more testing)
* Fixed wpf_tags_applied and wpf_tags_removed hooks not running when a webhook was received, since 3.37.4

= 3.37.5 - 3/30/2021 =
* Fixed Filter Course Steps setting with LearnDash integration treating Filter Queries as on, on some hosts
* Fixed url_to_postid() causing problems with WPML when Hide From Menus was active

= 3.37.4 - 3/29/2021 =
* Added Piotnet Forms integration
* Added Lock Lessons option to LearnDash courses
* Added Apply Tags - Approved setting to Events Manager events
* Added warning during HubSpot setup if site isn't SSL secured
* Added additional context to the "Can not operate manually on a dynamic list." error with HubSpot
* Improved - Active HubSpot lists will now show as "read only" when selected
* Improved performance with taxonomy term access rules
* Fixed YITH WooCommerce Frontend Manager triggering an error trying to load the WP Fusion settings panel on the frontend
* Fixed Filter Course Steps in LearnDash not properly adjusting the course step count
* Fixed ONTRApages plugin taking redirect priority over WP Fusion
* Added wp_fusion()->access->can_access_posts cache
* Added wp_fusion()->access->can_access_terms cache
* Added filter wpf_user_id
* Added filter wpf_restricted_content_message

= 3.37.3 - 3/22/2021 =

* __Added / Improved:__ 
	* Added Members integration
	* Added logging for when a linked tag is removed due to a user leaving a BuddyPress group
	* Added View in CRM links to admin user profile for FluentCRM and Groundhogg
	* Added View in CRM links to Easy Digital Downloads payment sidebar
	* Added WP Fusion status metabox to Gravity Forms single entry sidebar
	* Improved - Contact records created by guest form submissions or checkouts will now be identified to the ActiveCampaign tracking script
	* Improved upgrade process from pre-3.37 (fixes CRM fields getting lost in admin)
	* Improved - WooCommerce Memberships integration will try to avoid modifying any tags during a successful subscription renewal
	* Improved - Edits to fields on contact records in FluentCRM will now be synced back to the user record automatically
	* Improved - Disabled the "API Queue" with FluentCRM and Groundhogg
	* Improved - If a user is already logged in when coming from a ThriveCart success URL, they won't be logged in again

* __Bugfixes:__
	* Fixed Export Users batch operation not respecting Limit User Roles setting
	* Fixed tag changes not being synced back properly from FluentCRM
	* Fixed Member Access Controls with BuddyBoss denying access to all members if no tags were specified
	* Fixed BuddyBoss app notification segment not working with more than one selected tag
	* Fixed SQL error when searching for The Events Calendar events that are protected by tags, when Filter Queries was set to Advanced mode
	* Fixed WooCommerce Subscriptions meta fields not syncing for subscriptions that have no products
	* Fixed being unable to disable First Name and Last Name fields from sync
	* Fixed On-Hold WooCommerce orders from Bank Transfer payment gateway not being synced despite On-Hold being registered as a valid status
	* Fixed MemberPress integration syncing the details from the expiring transaction when switching between two free lifetime memberships
	* Fixed automated unenrollments not working with MemberPress transactions created using the Manual gateway

* __Developer Updates:__ 
	* Added "wpf_filtering_query" property to WP_Query objects that are being affected by Filter Queries - Advanced
	* Added wpf_leadsource_cookie_name filter
	* Added wpf_referral_cookie_name filter
	* Added wpf_get_current_user() function
	* Fixed fatal error on frontend if you selected Mautic as the CRM in the initial setup and saved the settings without entering API credentials
	* Fixed fatal error when running "EDD Recurring Payments statuses" batch operation
	* Fixed PHP 'WPF_Lead_Source_Tracking' does not have a method 'prepare_meta_fields' warning saving the settings
	* Fixed "Warning: Illegal string offset 'crm_field'"


= 3.37.2 - 3/15/2021 =
* Fixed fatal error with dynamic tagging and Event Tickets in 3.37.0
* Added expiration to cached Filter Queries results (thanks @trainingbusinesspros!)
* Added user_nicename field for sync

= 3.37.1 - 3/15/2021 =
* Fixed fatal error "Call to undefined function bbapp_iap()" in 3.37.0 when BuddyBoss App was active with IAP disabled
* Added "Default Not Logged-In Redirect" setting
* Added logging when wp_capabilities have been modified by data loaded from the CRM
* Fixed roles or capabilities loaded from the CRM being able to remove roles and/or capabilities from administrators
* Fixed wp_capabilities field not saved in correct format when loaded from the CRM

= 3.37.0 - 3/15/2021 =

* __Added / Improved:__
	* Added support for Create Tag(s) from Value with WooCommerce guest checkouts
	* Added support for Create Tag(s) from Value with Tribe Events guest registrations
	* Improved - When an Event Tickets attendee is moved to another ticket, their custom fields will be synced
	* Improved - Updated to support the new CartFlows admin UI
	* Improved - Added a safety check to prevent you from selecting the same tag for both Apply Tags - Enrolled and Link With Tag, on courses
	* Improved - wpForo usergroups will not be linked to tag changes if the user is an administrator (manage_options capability)

* __BuddyPress / BuddyBoss / bbPress:__
	* Added In-App Purchases support with BuddyBoss app (beta)
	* Added integration with BuddyBoss segments for app push notifications (beta)
	* Improved - If the BuddyPress groups directory page is protected, the restricted content message will replace the groups list
	* Improved - Added notice to BuddyPress group meta box to indicate when main groups page is protected by a tag
	* Fixed restricted bbPress topics not being hidden by Filter Queries - Advanced
	* Fixed restricted content message not displaying on restricted BuddyPress groups

* __Performance:__
	* Improved - Available tags and available fields have been moved to their own wp_options keys for improved performance
	* Improved - The wpf_options options key is now set to autoload, for improved performance
	* Improved - AJAX'ified the page redirect select in the meta box for improved admin performance
	* Improved - Moved the license check from a transient to an option to get around transient caching
	* Improved - Removed "Copy to related topics" from LearnDash meta box, for improved performance
	* Removed meta box notice about inheriting permissions from taxonomy terms (for improved performance)

* __Filter Queries:__
	* Improved performance with Filter Queries - Advanced, query results for the same post type will now be cached with wp_cache_set()
	* Fixed bbPress public topics being hidden when Filter Queries was set to Advanced
	* Fixed some post types registered by other plugins not showing as options for Filter Queries - Post Types
	* Added notice when Filter Queries is enabled on The Events Calendar event post types, and the Events Month Cache is enabled

* __Bugfixes:__
	* Fixed fields after a checkbox field on a Ninja Forms form being synced as boolean values
	* Fixed Create Tag(s) from Value creating errors with NationBuilder
	* Fixed tags not being applied to current user during form submission from 3.36.16
	* Fixed ActiveCampaign integration not treating 429 status code as an error
	* Fixed standard fields not being loaded from Autopilot
	* Fixed Autopilot integration creating new contacts when email address wasn't specified in update data
	* Fixed automatic name detection feature from 3.36.12 treating username as first_name
	* Fixed errors not being logged correctly while creating / updating GiveWP donors in the CRM

* __Developer Updates:__
	* Removed masking of ?cid= parameter from auto login URL since 3.36.5
	* Added wpf_bypass_query_filtering filter
	* Added wpf_query_filtering_mode filter
	* Added wpf_configure_setting_{$setting_id} filter

= 3.36.16 - 3/8/2021 =
* Added Filter Course Steps setting with LearnDash 3.4.0+
* Added search filter to select boxes in the admin
* Improved - If Staging Mode is enabled, site tracking scripts will be turned off with supported CRMs
* Improved EngageBay error handling
* Improved support for Filter Queries on LearnDash lessons in LearnDash v3.4.0 beta
* Improved - Elementor Forms integration data upgrades will now only run when the Elementor editor is active
* Fixed individual bbPress topics not respecting global Restrict Forums setting
* Fixed BuddyBoss profile types not being properly set via linked tag when removing and assigning a type in the same action
* Fixed menu items being hidden when Filter Queries was used in Standard mode and limited to specific post types
* Fixed PHP warning in Salesforce integration
* Fixed PHP warning when force-ending an auto login session

= 3.36.15 - 3/2/2021 =
* Fixed admin metabox settings getting reset when editing pages in Elementor since v3.36.12
* Fixed LifterLMS groups settings page not saving

= 3.36.14 - 3/1/2021 =
* Added Read Only indicator on non-writeable Salesforce fields
* Added wpf_hubspot_auth_url filter
* Added wpf_zoho_auth_url filter
* Added support for Datetime fields with ActiveCampaign
* Added Ticket Name field for sync with Tribe Tickets
* Improved support for multiselect fields with EngageBay
* Improved - Data will no longer be synced to Salesforce for read-only fields
* Improved - Users imported via a ThriveCart success URL will use the firstname and lastname parameters from the URL, if available
* Improved - Empty tags will now be filtered out and not applied during a WooCommerce guest checkout
* Improved - Custom fields are now separated from standard fields with Drip
* Improved - Username format for imported users will be set to FirstnameLastname by default on install if BuddyPress or BuddyBoss is active
* Improved - If FirstnameLastname or Firstname12345 are selected for the user import username format, and a user already exists with that username, the username will be randomized further
* Fixed tags from previous (still active) MemberPress memberships being removed when a member purchased a new concurrent membership
* Fixed wpForo custom profile fields not saving when loaded from the CRM
* Fixed ThriveCart success URLs triggering welcome emails to new users
* Fixed Mautic not loading more than 30 tags on some sites
* Fixed Ninja Forms integration using the last email address on a form as the primary email, not the first
* Fixed date-format fields sometimes not syncing correctly to Kartra
* Fixed Add New Field on Contact Fields list not saving when no CRM field was selected

= 3.36.13 - 2/24/2021 =
* Tested for WooCommerce 5.0.0
* Added support for syncing date, checkbox, and and multiselect type fields with Ninja Forms
* Improved error handing with Zoho
* Improved - Admin notices from other plugins will be hidden on the WPF settings page
* Fixed "Create tags from value" not working with form submissions
* Tribe Events Tickets RSVP bugfixes

= 3.36.12 - 2/22/2021 =
* Added tagging based on event registration status with Event Espresso
* Added Ticket Name and Registration Status fields for sync with Event Espresso
* Added support for the Individual Attendee Collection module in Event Tickets Plus
* Added track_event function to ActiveCampaign integration
* Improved HubSpot error logging
* Improved automatic detection for first_name and last_name fields during registration
* Improved performance - wpf-settings postmeta key will now be deleted on post save if there are no WPF settings configured for the post
* Improved - get_customer_id() in ActiveCampaign integration will now read the customer_id from a previously created cart, if available
* Improved - Log messages will now use the correct custom object type (instead of "contact") when a custom object is being edited
* Fixed query filtering not working on queries that used post__in
* Fixed BuddyPress group visibility rules taking priority over menu item visibility
* Fixed conflict with Woo Credits
* Fixed missing email address with Tribe Tickets guest RSVPs

= 3.36.11 - 2/15/2021 =
* Fixed PHP warning during login when Login Tags Sync is enabled

= 3.36.10 - 2/15/2021 =
* Added BuddyBoss Member Access Controls integration
* Added Give Funds & Designations integration
* Added View in Infusionsoft link to admin user profile
* Added support for Users Insights custom fields
* Added Home Page and Login Page options to "Redirect when access is denied" dropdown
* Improved - Automated membership level changes in Restrict Content Pro will now be logged to the Customer notes
* Improved - Login Tags Sync and Login Meta Sync features will now give up after 5 seconds if the CRM API is offline
* Improved - Refactored and standardized ConvertKit integration
* Improved - ConvertKit API timeout is now extended to 15 seconds
* Improved - LearnDash topics will now inherit their parent lesson settings if no access rules have been specified
* Improved - Added second argument $user_meta to wpf_map_meta_fields filter
* Fixed post type rules taking priority over single post access rules for query filtering
* Fixed JavaScript error on settings page when connected to ConvertKit
* Fixed PHP notices in admin
* wpForo bugfixes

= 3.36.9 - 2/9/2021 =
* Fixed undefined index PHP notices in v3.36.8

= 3.36.8 - 2/8/2021 =
* Added linked tags for LearnDash group leaders (thanks @dlinstedt)
* Added WP Fusion sync status meta box to the GiveWP payment admin screen
* Improved - Passwords generated by the LearnDash - ThriveCart extension will now be synced to the CRM after a new user is created if Return Password is enabled
* Improved logging for the Gamipress requirements system	
* Improved API error logging with Mailchimp
* Fixed Prevent Reapplying Tags setting not saving when un-checked
* Fixed Gamipress Requirements not being triggered when tags were loaded via a webhook
* Fixed menu visibility controls sometimes getting output twice
* Fixed some admin-only interfaces getting loading on the bbPress frontend profile and causing errors
* Fixed some bbPress frontend profile updates not syncing
* Fixed bbPress email address changes not syncing
* Fixed Gutenberg block visibility not respecting auto login sessions
* Fixed fatal error running WooCommerce Subscription Statuses batch operation on deleted subscriptions
* Fixed PHP notice "Constant DOING_WPF_BATCH_TASK already defined"
* Fixed deprecated function notice "WC_Subscriptions_Manager::user_has_subscription()"
* Fixed LifterLMS engagement settings not saving

= 3.36.7 - 2/1/2021 =
* Added ability to restrict BuddyPress group visibility based on tags and specify a redirect if access is denied
* Added BuddyPress User Profile Tabs Creator integration for profile tabs visibility control
* Added add_object() update_object() and load_object() methods to Salesforce, HubSpot, and Zoho
* Improved - When a user is removed from a BuddyBoss profile type via a linked tag, they will be given the Default Profile Type if one is set
* Fixed WooCommerce Orders batch exporter not recognizing custom "paid" order statuses for export
* Fixed the logs getting flushed if the filter form was submitted using the enter key
* Fixed error viewing AccessAlly settings page when AccessAlly was connected to ActiveCampaign
* Fixed array values not syncing to AgileCRM since v3.36.5

= 3.36.6 - 1/26/2021 =
* Fixed Gravity Forms integration not loading since v3.36.5
* Fixed tooltips not working in the logs
* Fixed HTTP API logging not showing up for ConvertKit
* Fixed some PHP notices

= 3.36.5 - 1/25/2021 =
* Added [[user_meta_if]] shortcode (thanks @igorbenic!)
* Added View In CRM link to admin user profile (at the moment just for ActiveCampaign)
* Added option to set default format for usernames for imported users
* Added notice in the logs when a user's role was changed via loading a new role from the CRM
* Added support for custom order form fields with WPPizza
* Added note about .csv imports to Import settings tab for Salesforce
* Improved - Divi access controls will now work on all modules (not just Text, Column, and Section)
* Improved - Role slug or name can be loaded from the CRM and used to set a user's role
* Improved - wpf_format_field_value in WPF_CRM_Base will stop imploding arrays (fixes issue syncing picklists options with commas in them to Salesforce and HubSpot)
* Improved Zoho error handling
* Improved - The ?cid= parameter will now be removed from the URL in the browser when using an auto login link
* Improved - Test Connection / Refresh Available Tags errors will now be shown on the top of the settings page (instead of the Setup tab)
* Fixed tag changes in FluentCRM not being synced back to WP Fusion
* Fixed dates loaded from HubSpot being loaded as milliseconds not seconds since the epoch
* Fixed date formatting not running on Ultimate Member standard fields when data was loaded from the CRM
* Fixed Ultimate Member not respecting custom date format when loading data from the CRM
* Fixed Ultimate Member Profile Completeness tags getting applied on every profile page view
* Fixed imported users not respecting "role" field loaded in user meta
* Fixed Gravity Forms and Formidable Forms integrations not being available in the wp_fusion()->integrations array
* Auto login bugfixes

= 3.36.4 - 1/8/2021 =
* Added Webhook Base URL field to general settings tab as a reference
* Added "ignoreSendingWebhook" parameter to EngageBay API calls
* Added Flush All Logs button at top of logs page
* Added debugging message to logs page for when the site runs out of memory building the Users dropdown
* Added third parameter $searchfield to wpf_salesforce_query_args filter
* Added logging for when WP Remote Users Sync is syncing tags to another connected site
* Added warning when curly quotes are detected in shortcode parameters
* Improved - Parentheses can now be used in shortcode attributes to match tags with square brackets in the name
* Improved - Salesforce webhook handler will now properly send a WSDL <Ack>false</Ack> when a webhook fails to be processed
* Improved - Logs will now show when an entry was recorded as a part of a batch operation
* Improved admin style for consistency with the rest of WP
* Improved NationBuilder error handling
* Fixed Ultimate Member not properly loading Unix timestamps from the CRM into Date type fields
* Fixed linked tag enrollments with Restrict Content Pro triggering additional tag changes in the CRM
* Fixed "Subscription Confirmation" type transactions getting picked up by the MemberPress Transactions Meta batch exporter
* Fixed MemberPress corporate account tags not applying
* Fixed MemberPress Subscriptions Meta batch tool syncing incorrect expiration date
* Fixed On Hold tags getting applied and removed when a WooCommerce Subscription was renewed via early renewal
* Fixed fatal error loading admin user profile when WP Fusion was not connected to a CRM
* Fixed PHP notices in Mailjet integration

= 3.36.3 - 1/13/2021 =
* Added First Name and Last Name fields for sync with Intercom
* Added Restrict Content Pro Joined Date fields for sync
* Added support for loading picklist / multiselect fields from Salesforce
* Improved logging for incoming webhooks with missing data
* Fixed broken ThriveCart auto-login from 3.36.1
* Fixed "quick update tags" not working with Mautic and ActiveCa,paign since 3.36.2
* Fixed PHP warning trying to get tag ID from tag label when no tags exist in the CRM
* Fixed returning null from wpf_woocommerce_customer_data marking the order complete

= 3.36.2 - 1/11/2021
* Added Apply Tags - Pending Cancellation with the Paid Memberships Pro Cancel on Next Payment Date addon
* Added "Quick Update Tags" support for Mautic webhooks (improved performance)
* Added indicator in the main access control meta box showing if the post is also protected by a taxonomy term
* Added wpf_woocommerce_sync_customer_data filter
* Improved - Customer meta data will no longer be synced during a WooCommerce renewal order
* Improved performance of the "update" and "update_tags" webhook methods with ActiveCampaign and Mautic
* Fixed HubSpot not converting dates properly
* Fixed contact ID not being passed from WooCommerce to Enhanced Ecommerce addon for registered users (from 3.36.1)

= 3.36.1 - 1/7/2021 =
* Improved - Refactored and optimized WooCommerce integration
* Improved asynchronous checkout for WooCommerce (will now be bypassed during IPN notifications)
* Improved - Refactored class WPF_API / webhooks handler
* Improved - Incoming duplicate webhooks will now be blocked
* Improved - Deactivating a license key will now also remove the license key from the settings page
* Fixed Cancelled tags not being applied with Paid Memberships Pro since v3.35.20
* Fixed WooCommerce billing details taking priority over user details when adding a new user in the admin
* Fixed bug applying and removing tags with Growmatik
* Fixed Pending tags not being applied for WooCommerce orders
* Added wpf_get_contact_id() function

= 3.36 - 1/4/2021 =
* Added HighLevel CRM integration
* Added Growmatik CRM integration
* Added WP Fusion payment status metabox to Easy Digital Downloads payment sidebar
* Added wpf_woocommerce_order_statuses filter
* Added wpf_woocommerce_subscription_status_apply_tags filter
* Added wpf_woocommerce_subscription_status_remove_tags filter
* Improved Asynchronous Checkout performance with CartFlows
* wpf_forms_pre_submission_contact_id will now run before wpf_forms_pre_submission
* Fixed auto-login session setting user to logged in when the contact ID was invalid
* Fixed PHP warning loading available users from Zoho

= 3.35.20 - 12/28/2020 =
* Added YITH WooCommerce Multi Vendor integration
* Added LaunchFlows integration
* Added option to limit Filter Queries to specific post types
* Added support for the Paid Memberships Pro - Cancel on Next Payment Date addon
* Added wpf_gform_settings_after_field_select action
* Fixed last name getting saved to first name field during a WooCommerce checkout
* Fixed WP Fusion trying to handle API error responses from ontraport.com that originated with other plugins
* Fixed LifterLMS billing and phone meta field keys

= 3.35.19 - 12/22/2020 =
* Fixed "Role not enabled for contact creation" notice when users register (from 3.35.17)

= 3.35.18 - 12/22/2020 =
* Fixed error when trying to add an entry to the logs before the CRM connection was configured

= 3.35.17 - 12/21/2020 =
* Added Start Date and End Date filters to the activity logs
* Added logging for when an API call to apply a tag isn't sent because the user already has that tag
* Added 1 second sleep time to Quentn batch operations to get around API throttling
* Added logging when the Import Trigger tags are removed as a part of a ConvertKit webhook
* Added additional Standard Fields for sync with Autopilot
* Improved - the available CRMs are now loaded on plugins_loaded to better support custom CRMs modules in other plugins
* Improved - Elementor form field values of "true" and "false" will now be treated as boolean with supported CRMs
* Fixed apostrophes getting escaped with slashes before being synced
* Fixed gender pronoun prefix getting synced with BuddyPress Gender-type fields
* Fixed header resync button on settings page not resyncing CRM lists
* Fixed Organizer Email overriding Attendee Email with Event Tickets Plus
* Fixed the No Tags filter in the users list showing all users
* Fixed Create Tags from Value on user_role conflicting with Limit User Roles setting
* Fixed Add New Field not working since 3.35.16
* Fixed tags not being applied with EDD Free Downloads addon

= 3.35.16 - 12/14/2020 =
* Added Work Address fields for sync with NationBuilder
* Added admin notice when logs are set to Only Errors mode
* Added link back to main settings page from the logs page
* Added "Apply registration tags" batch operation
* Added wpf_api_preflight_check filter
* Added Referrer's Username field for sync with AffiliateWP
* Added Affiliate's Landing Page field for sync
* Improved - Significantly reduced the amount of memory required for the main settings storage
* Improved error handling with Groundhogg and FluentCRM when those plugins are deactivated
* Improved support for auto login sessions on custom WooCommerce checkout URLs
* Fixed ActiveCampaign not loading more than 100 lists
* Fixed changed link tag warning appearing multiple times
* Fixed the new async checkout for WooCommerce not working with PayPal
* Fixed typo in the tooltip with the new wpf_format_field_value logging

= 3.35.15 - 12/8/2020 =
* Fixed "Invalid argument" warning listing custom fields with some CRMs
* Fixed Required Tags (All) in Elementor integration

= 3.35.14 - 12/7/2020 =
* Tested and updated for WordPress 5.6
* Added additional logging to show when meta values have been modified by wpf_format_field_value before being sent to the CRM
* Added "Additional Actions" to admin user profile (Push User Meta, Pull User Meta, and Show User Meta) for debugging purposes
* Added AffiliateWP Groups integration
* Added Referral Count, Total Earnings, and Custom Slug fields for sync with AffiliateWP
* Added functions wpf_get_crm_field(), wpf_is_field_active(), and wpf_get_field_type()
* Improved - All forms integrations will now use the first email address on the form as the primary email for lookups
* Updated ZeroBS CRM to Jetpack CRM
* Fixed default group not getting assigned in wpForo when a tag linked to a usergroup was removed
* Fixed Tribe Tickets integration treating the event organizer email as an attendee email
* Fixed importer getting hung up on more than 100 contacts with HubSpot
* Fixed bug with mapping Elementor Form fields when WPML was active
* Fixed WooCommerce auto-applied coupons not respecting Allowed Emails usage restrictions
* Fixed PHP warning in LearnDash 3.3.0

= 3.35.13 - 11/30/2020 =
* Added new experimental Asynchronous Checkout option (should be more reliable)
* Added warning when user_pass field is enabled for sync
* Added wpf_set_user_meta filter
* Improved - if no billing name is specified at WooCommerce checkout, the name from the user record will be used instead
* Improved error handling with Drip
* Improved Mailjet error handling
* Improved - If a MemberPress membership level has Remove Tags checked, then the tags will be removed when the member changes to a different membership level
* Fixed ArgumentCountError in WPF_BuddyPress::set_member_type()
* Fixed BuddyPress groups not being auto-assigned during a webhook
* Fixed BuddyPress custom fields not being loaded during a new user import
* Fixed user_registered getting loaded from the CRM when a user was imported
* Fixed Mailjet integration not loading more than 10 custom fields
* Fixed dates not formatted correctly with Mailjet

= 3.35.12 - 11/23/2020 =
* Added support for syncing custom attendee fields from Events Manager Pro
* Added warning in the logs for chaining together multiple automated enrollments based on tag changes
* Added wpf_woocommerce_attendee_data filter
* Added wpf_pmpro_membership_status_apply_tags filter
* Added wpf_pmpro_membership_status_remove_tags filter
* Added Re-Authorize With Hubspot button to re-connect via OAuth
* Improved logging for MailerLite webhooks
* Improved - MailerLite webhooks will now be deleted when resetting the settings
* Fixed Elementor visibility controls not showing on columns
* Fixed usage restriction settings not getting copied when a WooCommerce Smart Coupon was generated from a template
* Fixed PHP warning loading contact data with ActiveCampaign

= 3.35.11 - 11/18/2020 =
* Fixed Elementor widget visibility for "Required Tags (not)" bug from v3.35.9
* Added wpf_auto_apply_coupon_for_user filter
* Improved (No Tags) and (No Contact ID) filters in the All Users list

= 3.35.10 - 11/17/2020 =
* Fixed Elementor widget visibility bug from v3.35.9
* Removed dynamic tagging support from Groundhogg
* Users Insights bugfixes
* Fixed GiveWP not syncing billing address during the initial payment

= 3.35.9 - 11/16/2020 =
* Added WP Remote Users Sync integration
* Added support for FooEvents Bookings
* Added Apply Tags - Enrolled setting for LearnDash groups
* Added Last Order Date field for syncing with Easy Digital Downloads
* Added BuddyPress Profile Type field for sync
* Added logging for WP Fusion plugin updates
* Added Email Optin Default (checked vs un-checked) option for WooCommerce
* Removed "(optional)" string from Email Option checkbox on WooCommerce checkout
* Improved error handling for MailPoet
* Improved Elementor visibility settings migration from pre v3.35.8
* Improved Ninja Forms field mapping interface
* Improved - Moved LearnDash groups settings to Settings panel
* Fixed some issues with Sendinblue and email addresses that had capital letters
* Fixed syncing empty multiselects to EngageBay not erasing the selected values in the CRM
* Fixed multiselect fields not loading from EngageBay
* Fixed wpForo custom fields not loading from the CRM if they didn't start with field_
* Fixed linked tags not being removed when a BuddyPress profile type was changed
* Fixed Apply Tags select on LifterLMS access plans not saving
* Bugfixes for Gravity Forms User Registration

= 3.35.8 - 11/9/2020 =
* Added Tag Applied and Tag Removed as triggers for Gamipress points, ranks, and achievements
* Added option to sync Gamipress rank names when ranks are earned
* Added Required Tags (All) option to Elementor integration
* Added Logged In vs Logged Out setting to Elementor visibility controls
* Added License Renewal URL field for sync with Easy Digital Downloads Software Licensing 
* Added paused subscription tagging with MemberPress
* Added upgraded subscription tagging with MemberPress
* Added downgraded subscription tagging with MemberPress
* Improved - Multiselect values loaded from ActiveCampaign will now be loaded as arrays instead of strings
* Improved - Easy Digital Downloads Recurring Payment tags will no longer be removed and reapplied during a subscription renewal
* Fixed GiveWP integration not syncing renewal orders
* Fixed PHP warning in EngageBay integration when loading a contact with no custom properties
* Fixed - Updated for compatibility with wpForo User Custom Fields addon v2.x
* Fixed Advanced Custom Fields integration converting dates fields from other integrations

= 3.35.7 - 11/2/2020 =
* Added Owner ID field for sync with Groundhogg
* Added support for syncing avatars with BuddyPress and BuddyBoss
* Added option to sync field labels instead of values with Gravity Forms
* Added billing address fields for sync with GiveWP
* Improved handling of simultaneous LearnDash Course and BuddyPress group auto-enrollments
* Improved asynchronous checkout feature with WooCommerce during simultaneous orders
* Improved - Tags will no longer be removed during a refund if a customer has an active subscription to the refunded product
* Improved - Cancelled and On Hold subscription statuses with WooCommerce will now be ignored if the customer still has another active subscription to that product
* Fixed users having to log in twice if they tried to log in during an auto-login session
* Fixed auto-enrollments not working correctly with WPML when tags were loaded while the site was on a different language than the linked course or membership
* Fixed AffiliateWP "Approved" tags not being applied during AffiliateWP batch operation
* Fixed taxonomy term settings not saving when trying to remove protection from a term
* Fixed cancelled tags getting applied when an Easy Digital Downloads subscription was upgraded

= 3.35.6 - 10/26/2020 =
* Added email optin consent checkbox for WooCommerce
* Added support for custom address fields with Mailchimp
* Added Avatar field for sync with Jetpack CRM
* Improved support for syncing First Name and Last Name with Gist
* Improved - Post type archives will now respect wpf_post_type_rules access rules
* Updated updater
* Fixed PHP warnings in EDD Recurring Payments integration
* Fixed post-order actions not running on GiveWP renewal payments
* Fixed Gravity Forms syncing empty form fields
* Fixed widget settings not saving
* Fixed Teams for WooCommerce Memberships team name not syncing if the team was created manually
* Fixed error adding new Zoho Leads without a last name
* Fixed EDD Recurring Payments integration getting product tag settings from oldest renewal payment

= 3.35.5 - 10/19/2020 =
* Added Profile Picture field for sync with Groundhogg
* Added option to disable admin menu editor interfaces
* Added wpf_render_tag_multiselect_args filter
* Added gettext support to wpf-admin.js strings and updated .pot file

= 3.35.4 - 10/14/2020 =
* Fixed tags not saving on Gravity Forms feeds in v3.35.3
* Added event location fields for sync to Events Manager integration
* Added gettext support to wpf-options.js strings and updated .pot file
* Fixed JS bug when editing taxonomy terms
* Removed AWeber integration

= 3.35.3 - 10/12/2020 =
* Added Gravity PDF support
* Added BuddyBoss profile type statuses batch export tool
* Added Phone 3 through Phone 5 fields for syncing with Infusionsoft
* Improved support for Gravity Forms User Registration addon (removed duplicate API call)
* Gravity Forms beta 2.5 bugfixes
* Fixed Import Users tool with FluentCRM
* Fixed some funny stuff when auto-enrolling a user into a LearnDash group and course simultaneously
* Fixed Filter Queries setting not working on WooCommerce Related Products
* Fixed FacetWP JS error when Exclude Restricted Items was enabled
* Fixed settings page requiring an extra refresh after resetting before changing to a new CRM
* Continuing Kartra custom field bugfixes

= 3.35.2 - 10/6/2020 =
* Added doing_wpf_webhook() function
* Added additional validation and logging regarding setting user roles during import via webhook
* Fixed Ultimate Member account activation emails not being sent when a user was imported via a webhook
* Fixed "Wrong custom field format" error adding contacts to Kartra
* Fixed loading dropdown and multi-checkbox type fields from Kartra

= 3.35.1 - 10/5/2020 =
* Added refunded transaction tagging to the MemberPress integration
* Added logging for when an invalid role slug was loaded from the CRM
* Added datetime field support to Zoho integration
* Added support for dropdown and checkbox fields with Kartra
* Removed "Copy to X related lessons and topics" option with LearnDash courses
* Fixed Salesforce not connecting when the password has a slash character in it
* Fixed Gravity Forms PayPal conditional feed running prematurely when set to "Process this feed only if the payment is successful"
* Fixed Pods user forms syncing not detecting the correct user ID
* Fixed tags not applying when a WP E-Signature standalone document was signed
* Fixed FacetWP results filtering not recognizing the current user
* Fixed tags select not saving with FooEvents variations
* Fixed FooEvents event attendee tags not being applied to the WooCommerce customer if the customer was also an attendee
* Fixed Pods data not syncing during frontend form submission
* Fixed custom fields no longer syncing in FooEvents Custom Attendee Fields v1.4.19

= 3.35 - 9/28/2020 =
* Added FluentCRM integration
* Added beta support for Mautic v3
* Refactored wpf_render_tag_multiselect()
* Added support for EngageBay webhooks
* Added conditional logic support to Ninja Forms integration
* Improved - WooCommerce checkout fields will now be pre-filled when an auto-login link is used
* Improved FooEvents logging
* Improved error handling for NationBuilder
* Removed Groundhogg < 2.x compatibility code
* Fixed some funny stuff with Ninja Forms applying tags as numbers instead of names
* Fixed ConvertKit removing the Import Trigger tag after a user was imported
* Fixed custom fields not updating with Kartra
* Fixed FooEvent custom fields not syncing when no email address was provided for primary attendee
* Fixed "add" webhook changing user role to subscriber for existing users
* Fixed bbPress forum archive redirects not working for logged-out users
* Fixed Tribe Tickets not syncing first attendee

= 3.34.7 - 9/21/2020 =
* Added Toolset Types integration
* Added pre_user_{$field} filter on user data being synced to the CRM
* Improved support for custom fields with Kartra
* Fixed XProfile fields loaded from the CRM not being logged
* Fixed MailerLite importing subscribers during an update_tags webhook if multiple subscribers were in the payload
* Fixed date fields not syncing properly with Groundhogg
* Fixed ticket name not syncing with Events Manager
* Fixed applying tags on pageview in AJAX request during an auto login session not working on WP Engine

= 3.34.6 - 9/14/2020 =
* Added LifterLMS course track completion tagging
* Added linked tag indicators in the admin posts table for LearnDash courses and groups
* Added better handling for merged / changed contact IDs to Ontraport
* Improved - stopped syncing user meta during an Easy Digital Downloads renewal payment
* Fixed user meta not syncing when Pods user forms were submitted on the frontend
* Fixed JSON formatting error applying tags with AgileCRM
* Fixed import tool with MailerLite
* Fixed Process WP Fusion Actions Again order action showing on WooCommerce Subscriptions

= 3.34.5 - 9/7/2020 =
* Added Ticket Name field for sync with Events Manager
* Added Required Tags (not) to WooCommerce variations
* Improved support for syncing multi-checkbox fields with Gravity Forms
* Enabled Sequential Upgrade on WishList members added via auto-enrollment tags
* Fixed access controls not working on LearnPress lessons
* Fixed MailerLite integration using a case-sensitive comparison for email address changes
* Fixed Gravity Forms date dropdown-type fields not syncing
* Fixed Contact ID merge field not showing in Gravity Forms notifications editor
* Fixed "update" webhooks being treated as "add" with Salesforce when multiple contacts were in the payload
* Fixed - user_id will no longer be loaded during user imports
* Fixed - Renamed class WPF_Options to WP_Fusion_Options to prevent conflict with WooCommerce Product Filter

= 3.34.4 - 8/31/2020 =
* Added profile completion tagging option to BuddyBoss
* Added LifterLMS course enrollments batch tool
* Added a do_action( 'mepr-signup' ) to the MemberPress auto-enrollment process (for Corporate Accounts Addon compatibility)
* Added support for new background_request flag with the Ontraport API
* Fixed MailEngine SOAP warning when MailEngine wasn't the active CRM
* Fixed unwanted welcome emails with users imported via ConvertKit webhooks

= 3.34.3 - 8/24/2020 =
* Added a Default Account setting for Salesforce
* Improved Hide From Menus setting - will now attempt to get a post ID out of a custom link
* Improved - Add to cart button in WooCommerce will now be hidden by default if the product is restricted
* Moved ActiveCampaign tracking scripts to the footer
* Fixed ThriveCart auto login not setting name on new user
* Fixed user_meta shortcode not displaying field if value was 0
* Fixed PHP warning in WPForo integration
* Fixed Lead Source Tracking not working for guests
* Fixed bbPress / BuddyBoss forum access rules not working when the forum was accessed as a Group Forum

= 3.34.2 - 8/17/2020 =
* Added Events Manager fields for sync
* Added "raw" field type
* Added WooCommerce Points and Rewards integration
* Removed deactivation hook
* Fixed error registering for events with Events Manager
* Fixed LearnDash content inheriting access rules from wrong course ID when using shared steps
* Fixed tags not being applied during EDD checkout for logged in users who didn't yet have a contact record
* Fixed crash with auto login with Set Current User enabled, and BuddyBoss active
* Fixed importer with EngageBay
* Fixed ActiveCampaign not loading more than 100 custom fields

= 3.34.1 - 8/12/2020 =
* WordPress 5.5 compatibility updates

= 3.34 - 8/11/2020 =
* Added EngageBay CRM integration
* Added support for multiselect fields with Mautic
* Improved support for syncing wp_capabilities and role fields
* Asynchronous Checkout for WooCommerce will now be bypassed on subscription renewal orders
* Un-hooked MemberPress checkout actions from mepr-event-transaction-completed
* Fixed ActiveCampaign list Auto Responder campaigns not running on contacts added over the API
* Fixed custom WishList Member fields not loading from the CRM
* Fixed Required Tags (All) tags not showing on lock icon tooltip in post tables
* Fixed ActiveCampaign not loading more than 20 custom fields

= 3.33.20 - 8/3/2020 =
* Fixed broken WishList Member admin menu

= 3.33.19 - 8/3/2020 =
* Added Last Course Progressed field for sync with LearnDash
* Added Last Course Completed Date field for sync with LearnDash
* Added Last Lesson Completed Date field for sync with LearnDash
* Added wpf_user_can_access() function
* Added [[wpf_user_can_access]] shortcode
* Added support for quotes around tag names with Fluent Forms
* Added cancelled tagging to WishList member
* Improved WishList Member logging
* Updated some ActiveCampaign API calls to v3 API
* Fixed LearnDash course settings getting copied to lessons when using the Builder tab in LearnDash v3.2.2
* Fixed Form Auto Login with Fluent Forms
* Fixed wpForo custom fields not loading from CRM

= 3.33.18 - 7/27/2020 =
* Added HTTP API logging option
* Added LifterLMS Groups beta integration
* Added LifterLMS voucher tagging support
* Added X-Redirect-By headers when WP Fusion performs a redirect
* Added unlock utility for re-exporting Event Espresso registrations
* Improved Event Espresso performance
* Fixed Salesforce contact ID lookup failing with emails with + symbols
* Fixed auto-login warning appearing when previewing Gravity Forms forms in the admin

= 3.33.17 - 7/20/2020 =
* Added Organizer fields for syncing with Tribe Events / Event Tickets
* Added support for Groundhogg Advanced Custom Meta Fields
* Added timezone offset back to Ontraport date field conversion
* Added Refresh Tags & Fields button to top of WPF settings page
* Added notice when checking out in WooCommerce as an admin
* Added automatic detection for Formidable Forms User Registration fields
* Added out of memory and script timeout error handling to activity logs
* Added Gravity Forms referral support to AffiliateWP integration
* Added notice to LearnDash course / lesson / topic meta box showing access rules inherited from course
* Removed job_title and social fields from core WP fields on Contact Fields list
* Improved performance of update_tags webhook with ActiveCampaign
* Improved - last_updated usermeta key will be updated via WooCommerce when a user's tags or contact ID are modified (for Metorik compatibility)
* Improved - "Active" tags will no longer be removed when a MemberPress subscription is cancelled
* Improved - If the user_meta shortcode is used for a field that has never been loaded from the CRM, WP Fusion will make an API call to load the field value one time
* Improved - Updated has_tag() function to accept an array or a string
* Fixed restricted posts triggering redirects on a homepage set to Your Latest Posts in Settings >> Reading
* Fixed Groundhogg custom fields updated over the REST API not being synced back to the user record
* Fixed undefined function bp_group_get_group_type_id() in BuddyPress
* Fixed broken import tool with Drip
* Dynamic tagging bugfixes
* AgileCRM timezone tweaks

= 3.33.16 - 7/13/2020 =
* Added "Resync contact IDs for every user" batch operation
* Added "LearnDash course enrollment statuses" batch operation
* Added notice if an auto-login link is visited by a logged-in admin
* Improved query filtering on BuddyPress activity stream
* Improved - LearnDash courses, lessons, and topics will inherit access permissions from the parent course
* Improved - Split Mautic site tracking into two modes (Standard vs. Advanced)
* Improved - If API call to get user tags fails or times out, the local tag cache won't be erased
* Fixed a new WooCommerce subscription not removing the payment failed tags from a prior failed subscription for the same product
* Fixed Preview With Tag not working if the user doesn't have a CRM contact record
* Fixed restricted post category redirects not working if no tags was specified
* Fixed Hide Term on post categories hiding terms in the admin when Exclude Administrators was off
* Fixed import tool not loading more than 1,000 contacts with AgileCRM
* Fixed AgileCRM not properly looking up email addresses for some contacts
* Fixed get_tag_id() returning tag name with Groundhogg since v3.33.15
* Refactored WooCommerce Subscriptions integration and removed cron task
* Memberoni bugfixes
* Updated .pot file

= 3.33.15 - 7/6/2020 =
* Updated User.com integration for new API endpoint
* Added BuddyPress groups statuses batch operation
* Added ability to create new tags in Groundhogg via WP Fusion
* Added setting for additional allowed URLs to Site Lockout feature
* Added Generated Password field for syncing with WooCommerce
* Added Membership Level Name field for syncing with WishList Member
* Improved support for syncing phone numbers with Sendinblue
* Users added to a multisite blog will now be tagged with the Assign Tags setting for that site
* Fixed Zoho field mapping not converting arrays when field type was set to "text"
* Fixed replies from restricted bbPress topics showing up in search results
* Fixed WooCommerce attributes only being detected from first 5 products instead of 100
* Fixed deletion tags not being applied on multisite sites when a user was deleted from a blog
* Fixed MemberPress subscription data being synced when a subscription status changed from Active to Active
* Fixed duplicate tags being applied when a MemberPress subscription and transaction were created from the same registration
* Fixed Filter Queries (Advanced) hiding restricted posts in the admin
* Fixed Contact Form 7 integration running when no feeds were configured
* Fixed Woo Memberships for Teams team name not syncing when a member was added to a team
* Fixed Mautic merging contact records from tracking cookie too aggressively
* Fixed archive restrictions not working if no required tags were specified
* Event Espresso bugfixes

= 3.33.14 - 6/29/2020 =
* Added priority option for Return After Login
* Added option to set a default owner for new contacts with Zoho
* Added Membership Status field for sync with WooCommerce Memberships
* Added product variation tagging for FooEvents attendees
* Improved multiselect support with Zoho
* Improved support for syncing multi-checkbox fields with Formidable Forms
* Fixed refreshing the logs page after flushing the logs flushing them again
* Fixed Group and Group Type tags not being applied in BuddyPress when an admin accepted a member join request
* GiveWP bugfixes

= 3.33.13 - 6/23/2020 =
* Fixed invalid redirect URI connecting to Zoho
* Fixed Loopify and Zoho getting mixed up during OAuth connection process

= 3.33.12 - 6/22/2020 =
* Added Modern Events Calendar integration
* Added status indicator for Inactive people in Drip
* Improved support for Mautic site tracking
* Improved translatability and updated pot files
* Fixed updated phone to primary_phone with Groundhogg
* Fixed Paused tags not getting removed when a WooCommerce membership comes back from Paused status during a Subscriptions renewal
* Fixed "Cancelled" tags getting applied to pending members during MemberPress Membership Statuses batch operation
* Fixed duplicate log entries when updating BuddyPress profiles
* Fixed contact ID not being detected in some Mautic webhooks
* Fixed syncing multi-checkbox fields from WPForms
* Fixes for syncing expiration dates with WooCommerce Memberships
* Fixed PHP warning while submitting Formidable forms with multi-checkbox values

= 3.33.11 - 6/18/2020 =
* Fixed fatal conflict when editing menu items if other plugins hadn't been updated to the WP 5.4 menu syntax
* Fixed AgileCRM tag name validation false positive on underscore characters
* Fixed logs items per page not saving in WP 5.4.2
* Fixed compatibility with Gifting for WooCommerce Subscriptions v2.1.1

= 3.33.10 - 6/15/2020 =
* Added WooCommerce Appointments integration
* Added WP Crowdfunding integration
* Added Subscription Status field for syncing with WooCommerce Subscriptions
* Added Last Order Payment Method field for syncing with WooCommerce
* Added Last Order Total field for syncing with WooCommerce
* Added WishList Membership Statuses batch operation
* Added wpf_get_contact_id_email filter
* Added wpf_batch_objects filter
* Added tag name validation to AgileCRM integration
* Added super secret WooCommerce Subscriptions debug report
* Reduced the amount of data saved by the background worker to help with max_allowed_packet issues
* Fixed address fields not being synced back to WordPress after an admin contact save in Groundhogg
* Fixed bug in loading MemberPress radio field values from the CRM
* Fixed Active tags getting reapplied when a WooCommerce Subscription status changed to Pending Cancel
* Fixed WishList Member v3 custom fields not syncing
* Fixed WishList Member Stripe registration creating contacts with invalid email addresses
* Fixed s2Member custom fields not being synced on profile update

= 3.33.9 - 6/8/2020 =
* Added support for syncing custom event fields with Tribe Events Calendar Pro
* Added support for BuddyPress Username Changer addon
* Added option to apply tags when a user is added to a BuddyPress group type
* Added ld_last_course_enrolled field for syncing with LearnDash
* Added tagging based on assignment upload for LearnDash topics
* Added customer_id field for sync with Easy Digital Downloads
* Added Remove Tags from Customer setting to Gifting for WooCommerce Subscriptions integration
* Fixed essay-type answers not syncing properly from LearnDash quizzes
* Fixed auto-enrollments not working with TutorLMS paid courses
* Fixed import tool not loading more than 10 contacts with Mailchimp
* Fixed import tool not loading more than 100 contacts with MailerLite
* Fixed Gifting for WooCommerce Subscriptions integration not creating a second contact record for the gift recipient when billing_email was enabled for sync

= 3.33.8 - 6/2/2020 =
* WooCommerce Subscribe All the Things tags will now be applied properly during a WooCommerce Subscription Statuses batch operation
* Fixed width of tag select boxes in LearnDash settings panel when Gutenberg was active

= 3.33.7 - 6/1/2020 =
* Added Beaver Themer integration
* Added global Apply Tags to Group Members setting for Restrict Content Pro
* Added support for syncing multiple event attendees with Tribe Tickets
* Added Username for sync with Kartra
* Added wpf_salesforce_lookup_field filter
* Added staging mode notice on the settings page
* Moved LearnDash course settings onto Settings panel
* Refactored WooCommerce Memberships integration and updated tagging logic to match WooCommerce Subscriptions
* Salesforce will now default to the field configured for sync with the user_email field as the lookup field for records
* Improved logging with Salesforce webhooks
* Fixed WooCommerce billing_country not converting to full country name when field type was set to "text"
* Fixed auto-login session from form submission ending if Allow URL Login was disabled
* WishList Member bugfixes
* Fixed SSL error connecting to Zoho's Indian data servers

= 3.33.6 - 5/25/2020 =
* Added setting for Prevent Reapplying Tags (Advanced)
* Added GiveWP Donors and GiveWP Donations batch operations
* Added Total Donated and Donations Count fields for sync with GiveWP
* Added Pending Cancellation and Free Trial status tagging for WooCommerce Memberships
* Added wpf_disable_tag_multiselect filter
* Added CloudFlare detection to webhook testing tool
* Added global Apply Tags to Customers setting for Easy Digital Downloads
* GiveWP integration will now only sync donor data for successful payments
* Improved error handling for invalid tag names with Infusionsoft
* Improved support for multiselect fields with Contact Form 7
* Fixed Filter Queries not working on search results in Advanced mode
* Fixed bug causing failed contact ID lookup to crash form submissions
* Fixed Infusionsoft not loading tag names with Hebrew characters
* Klaviyo bugfixes

= 3.33.5 - 5/18/2020 =
* Added Give Recurring Donations support
* Added Pods user fields support
* Added Remove Tags option to Restrict Content Pro integration
* Added Payment Failed tagging to Paid Memberships Pro
* Added "Paid Memberships Pro membership meta" batch operation
* Refactored and optimized Paid Memberships Pro integration
* Improved error handling for Salesforce access token refresh process
* Improved Restrict Content Pro inline documentation
* Improved filtering tool on the All Users list
* Offending file and line number will now be included on PHP error messages in the logs
* Added alternate method back to the batch exporter for cases when it's being blocked
* Fixed Cancelled tags getting applied in Paid Memberships Pro when a member expires
* Fixed Filter Queries not working on the blog index page
* Maropost bugfixes

= 3.33.4 - 5/11/2020 =
* Facebook OG scraper will now bypass access rules if SEO Show Excerpts is on
* Added validation to custom meta keys registered for sync on the Contact Fields list
* Added compatibility notices in the admin when potential plugin conflicts are detected
* Updated Fluent Forms integration
* Updated MemberPress membership data batch process to look at transactions in addition to subscriptions
* LearnDash enrollment transients are now cleared when a user is auto-enrolled into a group
* Intercom integration will now force the v1.4 API
* Fixed Spanish characters not showing in Infusionsoft tag names
* Fixed logs showing unenrollments from LearnDash courses granted by LearnDash Groups
* Fixed warning when using Restrict Content Pro and the Groups addon wasn't active
* Fixed guest checkout tags not being applied in Maropost
* Fixed set-screen-option filter not returning $status if column wasn't wpf_status_log_items_per_page (thanks @Pebblo)

= 3.33.3 - 5/5/2020 =
* Fixed an empty WooCommerce Subscriptions Gifting recipient email field on checkout overwriting billing_email
* Fixed Infusionsoft form submissions starting auto-login sessions even if the setting was turned off

= 3.33.2 - 5/4/2020 =
* Added WP-Members integration
* Added Users Insights integration
* Added WooCommerce Shipment Tracking integration
* Added event check-in and checkout tagging for Event Espresso
* Added dynamic tagging support for Event Espresso
* Added Remove Tags option for WooCommerce Memberships for Teams integration
* Added Team Name field for sync to WooCommerce Memberships for Teams integration
* Added support for tagging on Stripe Payment Form payments with Gravity Forms
* Fixed "Remove Tags" setting not being respected during a MemberPress Memberships batch operation
* Fixed Ultimate Member linked roles not being assigned when a contact is imported via webhook
* Fixed welcome emails not being sent by users imported from a Salesforce webhook with multiple contacts in the payload

= 3.33.1 - 4/27/2020 =
* Fixed fatal error in Teams for WooCommerce Memberships settings panel

= 3.33 - 4/27/2020 =
* Added WP ERP CRM integration
* Added Gifting for WooCommerce Subscriptions integration
* Added Events Manager integration
* Added WPComplete tagging for course completion
* Added support for WPForo usergroups auto-assignment via tag
* Added PHP error handling to logger
* Added Double Optin setting to Mailchimp integration
* Added Time Zone and Language fields for Infusionsoft
* Badges linked with tags in myCred will now be removed when the linked tag is removed
* Improvements to asynchronous checkout process
* Fixed Hide From Menus filter treating a taxonomy term as a post ID for access control
* Fixed Gravity Forms feeds running prematurely on pending Stripe transactions

= 3.32.3 - 4/20/2020 =
* Added Apply Tags - Profile Complete setting to Ultimate Member
* Updated WishList member integration for v3.x
* Translatepress language code can now be loaded from the CRM
* Removed "Profile Update Tags" setting
* Fixed coupon_code not syncing with WooCommerce
* Fixed unnecessary contact ID lookup in user import process

= 3.32.2 - 4/17/2020 =
* Added wpf_woocommerce_user_id filter
* Fixed MailerLite subscriber IDs not loading properly on servers with PHP_INT_MAX set to 32
* Fixed Status field not updating properly with Drip
* Fixed fatal error checking if order_date field was enabled for sync during a WooCommerce renewal payment

= 3.32.1 - 4/13/2020 =
* Added fallback method for background worker in case it gets blocked
* Added Filter Queries setting to Beaver Builder Posts module
* Added support for defining WPF_LICENSE_KEY in wp-config.php
* Added debug tool for MailerLite webhooks
* Added Status field for syncing with Drip
* Added support for wpForo User Custom Fields
* WooCommerce Subscription renewal dates will now be synced when manually edited by an admin
* Improved importer tool with ActiveCampaign
* Improved logging for MailerLite webhooks
* Fixed Ultimate Member registrations failing to sync data with multidimentional arrays
* Fixed optin_status getting saved to contact meta with Groundhogg
* Fixed "Cancelled" tags getting applied when a WooCommerce subscription was trashed
* Fixed PHP warning in updater when license wasn't active
* Fixed CRM field labels not showing in Caldera Forms
* Fixed Mautic not importing more than 30 contacts using import tool

= 3.32 - 4/6/2020 =
* Added Loopify CRM integration
* Added support for Advanced Forms Pro
* Added Set Current User option to auto-login system
* Added Send Confirmation Emails setting for MailPoet
* Added Enable Notifications option for MailerLite import webhooks
* s2Member membership level tags will now be applied when an s2Member role is changed
* Moved logs to the Tools menu
* Removed bulk actions from logs page
* Updated admin menu visibility interfaces for WP 5.4
* Fixed metadata loaded from the CRM into Toolset Types user fields not saving correctly
* Fixed temporary passwords getting synced when a password reset was requested in Ultimate Member
* Fixed sub-menu items not being hidden if parent menu item was hidden
* Fixed Gravity Forms Entries batch operation not detecting all entries
* Fixed order_id and order_date not syncing during a WooCommerce Subscriptions renewal order
* Fixed WooCommerce Subscription product name not being synced when a subscription item is switched
* Fixed email address changes not getting synced after confirmation via the admin user profile

= 3.31 - 3/30/2020 =
* Added Quentn CRM integration
* Improved support for multiselect fields in Gravity Forms
* Improved Trial Converted tagging for MemberPress
* Fixed Defer Until Activation not working with Ultimate Member when a registration tag wasn't specified
* Fixed affiliate cookies not being passed to asynchronous WooCommerce checkouts

= 3.30.4 - 3/23/2020 =
* Added WP Simple Pay integration
* Added Apply Tags on View option for taxonomy terms
* contactId can now be used as a URL parameter for auto-login with Infusionsoft
* Contacts will no longer be created in Ontraport without an email address
* Removed non-editable fields from Ontraport fields dropdowns
* Improved Return After Login feature with LearnDash lessons
* Fixed lead source variables not syncing to Ontraport
* Fixed lead source tracking data not syncing during registration

= 3.30.3 - 3/20/2020 =
* Added additional tagging options for WooCommerce Subscribe All The Things
* Added WPGens Refer A Friend integration
* Fixed issue with saving variations in WooCommerce 4.0.0 causing variations to be hidden
* Fixed Long Text type fields not being detected with WooCommerce Product Addons
* Fixed duplicate content in Gutenberg block

= 3.30.2 - 3/18/2020 =
* Added MemberPress transaction data batch operation
* Fixed payment failures in MemberPress not removing linked tags

= 3.30.1 - 3/16/2020 =
* Added Oxygen page builder integration
* Added support for Formidable Forms Registration addon
* Added WooCommerce Request A Quote integration
* Added Remove Tags option to MemberPress
* Added automatic data conversion for dropdown fields with Ontraport
* Added data-remove-tags option to link click tracking
* Added wpf_woocommerce_billing_email filter
* Added wpf_get_current_user_id() function
* Added wpf_is_user_logged_in() function
* Auto login system no longer sets $current_user global
* Fixed WooCommerce auto-applied coupons not applying when Hide Coupon Field was enabled
* Fixed Duplicate and Delete tool for MailerLite email address changes
* Fixed Formidable Forms entries not getting synced when updated
* Fixed conflict between LearnDash [course_content] shortcode and Elementor for restricted content messages
* Fixed duplicate contact ID lookup API call for new user registrations with existing contact records
* Fixed Paid Memberships Pro membership level settings not saving
* Refactored and optimized MemberPress integration
* Removed WooCommerce v2 backwards compatibility
* Compatibility updates for Advanced Custom Fields Pro v5.8.8
* Stopped loading meta for new user registrations with existing contact records

= 3.30 - 3/9/2020 =
* Added SendFox integration
* Added compatibility with WooCommerce Subscribe All The Things extension
* Added auto-enrollment tags for TutorLMS courses
* Fixed MemberPress membership levels not getting removed when the linked tag is removed
* Tribe Tickets bugfixes and compatibility updates

= 3.29.7 - 3/5/2020 =
* Added support for WooCommerce order status tagging with statuses created by WooCommerce Order Status Manager
* Fixed restricted content message not being output when multiple content areas were on a page
* Fixed New User Benchmark not firing with Groundhogg
* Fixed changed email addresses not syncing to Sendinblue
* Fixed names not syncing with profile updates in BuddyPress

= 3.29.6 - 3/2/2020 =
* Added option to send welcome email to new users imported from ConvertKit
* Added Apply Tags - Trial and Apply Tags - Converted settings to MemberPress
* Added Coupon Used field for sync with MemberPress
* Added Trial Duration field for sync with MemberPress
* Added Default Optin Status option for Groundhogg
* New user welcome emails are now sent after tags and meta data have been loaded
* Expired and Cancelled tags will now be removed when someone joins a Paid Memberships Pro membership level
* Removed admin authentication cookies from background worker
* Stopped converting dates to GMT with Ontraport
* Fixed Tags (Not) visibility bug with Beaver Builder

= 3.29.5 - 2/24/2020 =
* Added optin_status field for syncing with Groundhogg
* Added Defer Until Activation setting to BuddyPress
* Added Defer Until Activation setting to User Meta Pro
* Added wc_memberships_for_teams_team_role field for syncing with WooCommerce Memberships for Teams
* Added bulk edit support to WP Courseware courses and units
* Added wpf_forms_args filter to forms integrations
* New contacts added to Groundhogg will be marked Confirmed by default
* Added "Apply Tags - Enrolled" setting to LearnDash courses
* Fixed WooCommerce auto applied coupons not respecting coupon usage restrictions
* Fixed Recurring Payment Failed tags not being applied with Restrict Content Pro
* Fixed Mautic not listing more than 30 custom fields
* Fixed Mailchimp not loading more than 200 tags

= 3.29.4 - 2/17/2020 =
* Added Last Coupon Used field for syncing with WooCommerce
* Added support for global addons with WooCommerce Product Addons
* Added default fields to MailerLite for initial install
* Leads will now be created in Gist instead of Users if the subscriber doesn't have a user account
* Fixed auto-enrollments not working with more than 20 BuddyBoss groups
* Fixed error with myCRED when the Badges addon was disabled
* Fixed messed up formatting of foreign characters in Gutenberg block
* Fixed conflict between Clean Login and Convert Pro integrations
* Fixed underscores not loading in Infusionsoft tag labels

= 3.29.3 - 2/10/2020 =
* Added support for EDD Custom Prices addon
* Added Required Tags (not) setting to access control meta box
* Added an alert to the status bar of the background worker if API errors were encountered during data export
* Manually changing a WooCommerce subscription status to On Hold will now immediately apply On Hold tags instead of waiting for renewal payment
* Fixed background worker status check getting interrupted by new WooCommerce orders
* Fixed user_activation_key getting reset when importing new users and breaking Better Notifications welcome emails
* Fixed PHP error manually adding a member to a team in WooCommerce Memberships for Teams

= 3.29.2 - 2/4/2020 =
* Added wpf_auto_login_cookie_expiration filter
* Added wpf_salesforce_query_args filter
* Fixed Approved tags getting applied with Event Espresso when registrations are pending
* Fixed tags not applying with Event Espresso

= 3.29.1 - 2/3/2020 =
* Added WP Ultimo integration
* Added notice when linked / auto-enrollment tags are changed on a course or membership
* Added wpf_event_espresso_customer_data filter to Event Espresso
* Added option to Event Espresso to sync attendees in addition to the primary registrant
* Added additional event and venue fields for syncing with FooEvents
* Added additional event and venue fields for syncing with Event Espresso
* Added wp_s2member_auto_eot_time for syncing with s2Member
* Fixed Invalid Data errors when syncing a number to a text field in Zoho
* Fixed "Return After Login" not working with WooCommerce account login
* Maropost bugfixes

= 3.29 - 1/27/2020 =
* Added Klick-Tipp CRM integration
* Logged in users and form submissions will now be identified to the Gist tracking script
* WooCommerce order status tags will now be applied even if the initial payment wasn't processed by WP Fusion
* WooCommerce Subscriptions v3.0 compatibility updates
* Improved webhooks with MailerLite (can now handle multiple subscribers in a single payload)
* Suppressed HTML5 errors in Gutenberg block
* Fixed tags not getting removed from previous variation when a WooCommerce variable subscription was switched
* Groundhogg bugfixes
* Maropost bugfixes
* Sendinblue bugfixes

= 3.28.6 - 1/20/2020 =
* Added linked tags to Ranks with myCred
* Added BuddyPress Account Deactivator integration
* Added Entries Per Page to Screen Options in logs
* Fixed special characters in tag names breaking tags loading with Infusionsoft
* Copper bugfixes

= 3.28.5 - 1/15/2020 =
* Fixed notice with ConvertKit when WP_DEBUG was turned on
* Auto login sessions will now end on the WooCommerce Order Received page

= 3.28.4 - 1/13/2020 =
* Added support for myCred ranks
* Added Event Start Time field for syncing with Event Espresso
* Improved Paid Memberships Pro logging
* Fixed being unable to remove a saved tag on a single AffiliateWP affiliate
* Fixed special characters not getting encoded properly with Contact Form 7 submissions
* Fixed bug in updater and changelog display
* Slowed down batch operations with ConvertKit to get around API throttling
* Added logging for API throttling with ConvertKit
* Added support for dropdown-type fields with Copper
* Copper bugfixes

= 3.28.3 - 1/9/2020 =
* Fixed ActiveCampaign contact ID lookups returning error message when connected to non-English ActiveCampaign accounts

= 3.28.2 - 1/9/2020 =
* Performance improvements with LearnDash auto enrollments
* Improved debugging tools for background worker
* Menu item visibility bugfixes
* Gist compatibility updates for changed API methods

= 3.28.1 - 1/6/2020 =
* Added option for tagging on LearnDash assignment upload
* Added Share Logins Pro integration
* Tags will now be removed from previous status when a membership status is changed in WooCommerce Memberships
* Improved handling for email address changes with Sendinblue
* Give integration bugfixes
* GetResponse bugfixes

= 3.28 - 12/30/2019 =
* Added Zero BS CRM integration
* Added MailEngine CRM integration (thanks @pety-dc and @ebola-dc)
* Added wpf_user_can_access and wpf_divi_can_access filters to Divi integration
* Added option to merge order status into WooCommerce automatic tagging prefix
* Removed extra column in admin list table and moved lock symbol to after the post title
* Ultimate Member roles that are linked with a tag will no longer leave a user with no role if the tag is removed
* Added additional WooCommerce Memberships logging
* Menu item visibility bug fixes

= 3.27.5 - 12/23/2019 =
* Added option to restrict access to individual menu items
* Added FacetWP integration
* Added support for AffiliateWP Signup Referrals addon
* Added export tool for Event Espresso registrations
* Fixed BuddyPress groups not running auto-enrollments when a webhook is received

= 3.27.4 - 12/16/2019 =
* Improved support for custom fields with FooEvents
* Added wpf_aweber_key and wpf_aweber_secret filters
* Logged in users and guest form submissions will now be identified to the Autopilot tracking script
* Event Espresso integration will now sync the event date from the ticket, not the event
* Fixed Elementor Popups triggering on every page for admins
* Autopilot bugfixes

= 3.27.3 - 12/11/2019 =
* Added support for WP Event Manager - Sell Tickets addon
* Added support for Popup Maker subscription forms
* Improvements to applying tags with Kartra using the new Kartra API endpoints
* Fixed billing address fields not syncing with PayPal checkout in s2Member
* Fixed Restrict Content Pro linked tags being removed when a user cancelled their membership before the end of the payment period
* Fixed missing email addresses causing BirdSend API calls to fail
* Fixed issues with non well-formed HTML content causing errors in inner Gutenberg blocks
* Fixed auto un-enrollment from LearnDash courses not working when course access was stored in user meta
* Fixed Advanced Custom Fields integration overriding date formats from WooCommerce

= 3.27.2 - 12/3/2019 =
* Fixed load contact method with Sendinblue
* Gutenberg block will no longer output HTML if there's nothing to display

= 3.27.1 - 12/2/2019 =
* Added GravityView integration
* Added batch tool for Restrict Content Pro members
* Added additional built in Gist fields for syncing
* Added option to tag customers based on WooCommerce order status
* Added support for global webhooks with Sendinblue
* Restrict Content Pro rcp_status field will now be synced when a membership expires
* WooCommerce Smart Coupons bugfixes
* Fixed ACF date fields not converting to CRM date formats properly
* Fixed bug in Import Tool with Sendinblue
* Fixed BirdSend only loading 15 available tags
* Fixed GMT offset calculation with Ontraport date fields

= 3.27 - 11/25/2019 =
* Added BirdSend CRM integration
* Added WP Event Manager integration
* Added support for triggering LifterLMS engagements when a tag is applied
* Fixed WPF settings not saving on CPT-UI post type edit screen
* Fixed Woo Memberships for Teams team member tags not being applied with variable product purchases
* Updated Gist API URL
* Fixed import tool not loading more than 50 contacts with Sendinblue
* wpf_tags_applied and wpf_tags_removed will now run when tags are loaded from the CRM

= 3.26.5 - 11/18/2019 =
* Added Groundhogg company fields for sync
* Added Event Name, Event Venue, and Venue Address fields for sync to Event Espresso
* Improved site tracking with HubSpot for guests
* eLearnCommerce login tokens can now be synced on registration
* Fixed refreshing Zoho access token with Australian data server
* Improved support for Country field with Groundhogg
* Style compatibility updates for WP 5.3

= 3.26.4 - 11/11/2019 =
* Added Toolset Types integration
* Added event_date field to Event Espresso integration
* Added signup_type field to NationBuilder
* Updated LifterLMS auto enrollments to better deal with simultaneous webhooks
* WP E-Signature bugfixes
* Access Key is no longer hidden when connected to MailerLite
* Improved Mautic site tracking
* Improved handling of merged contacts with Mautic
* Improved compatibility with Gravity Forms PayPal Standard addon
* Give integration bugfixes

= 3.26.3 - 11/4/2019 =
* Added Fluent Forms integration
* Added AffiliateWP affiliates export option to batch tools
* Added Australia data server integration to Zoho integration
* Apply Tags on View tags won't be applied for LearnDash lessons that aren't available yet
* Mautic tracking cookie will now be set after a form submission
* Give integration will now only apply tags when a payment status is Complete
* Fixed bug with Intercom API v1.4
* Fixed bug with The Events Calendar Community Tickets addon

= 3.26.2 - 10/28/2019 =
* Added "capabilties" format for syncing capability fields
* Added India data server support to Zoho integration
* Improved handling of multi-select and dropdown field types in PeepSo
* Fixed return after login for redirects on hidden WooCommerce products

= 3.26.1 - 10/21/2019 =
* Added Memberoni integration
* Improved integration with PilotPress login process
* Woo Subscriptions actions will no longer run on staging sites
* Fixed conflict with ThriveCart auto login and UserPro

= 3.26 - 10/14/2019 =
* Added Klaviyo integration
* Fixed PeepSo multi-checkbox fields syncing values instead of labels
* Fixed Elementor Pro bug when Elementor content was stored serialized

= 3.25.17 - 10/9/2019 =
* Added support for Ranks with Gamipress
* Enabled Import Users tab for Intercom
* Added "role" and "send_notification" parameters for ThriveCart auto login
* Performance improvements and bugfixes for background worker

= 3.25.16 - 10/7/2019 =
* Added custom fields support to Give
* Added option to hide restricted wpForo forums
* Added "ucwords" formatting option to user_meta shortcode
* Ultimate Member roles will now be removed when a linked tag is removed
* Fixed special characters getting escaped on admin profile updates

= 3.25.15 - 9/30/2019 =
* Added WP E-Signature integration
* Added UserInsights integration
* Added option to hide WPF meta boxes from non admins
* Added support for syncing multi-input Name fields for WPForms
* Added Filter Queries setting to Elementor Pro Posts and Portfolio widgets
* Updated ActiveCampaign site tracking scripts
* Fixed NationBuilder not loading more than 100 available tags
* Fixed GiveWP recurring payments treating the donor as a guest
* Fixed PeepSo first / last name fields not syncing on registration forms
* Fixed fatal error when initializing GetResponse connection
* All site tracking scripts will now recognize auto login sessions

= 3.25.14 - 9/23/2019 =
* Added WPPizza integration
* Existing Elementor forms will now update available CRM fields automatically
* Added new filters and better session termination to auto login system
* Payment Failed tags will now be removed after a successful payment on a WooCommerce subscription
* Disabled comments during auto login sessions
* Fixed bug with WooCommerce Points and Rewards discounts not applying
* Fixes for HubSpot accounts with over 250 lists
* Sendinblue bugfixes

= 3.25.13 - 9/18/2019 =
* Sendinblue bugfixes
* Bugfixes for syncing LearnDash quiz answers

= 3.25.12 - 9/16/2019 =
* Added support for Woo Checkout Field Editor Pro
* Added CartFlows upsell tagging
* Added support for CartFlows custom fields
* Added ability to sync LearnDash quiz answers to custom fields
* Fixed Gravity Forms entries export issue with Create Tag(s) From Value fields
* Fixed Mailchimp contact ID getting disconnected after email address change
* Fixed BuddyPress fields not being detected on custom profile types
* Fixed WooCommerce automatic coupons not being applied properly when a minimum cart total was set
* Fixed NationBuilder Primary address fields not syncing
* Fixed updating email addresses in WooCommerce / My Account creating duplicate subscribers in Drip

= 3.25.11 - 9/9/2019 =
* Added Site Lockout feature
* Added Ahoy messaging integration
* Added prefix option for WooCommerce automatic tagging
* Added additional AffiliateWP fields
* Gravity Forms batch processor can now process all unprocessed entries
* Increased limit on LifterLMS Memberships Statuses batch operation to 5000
* Salon Booking tweaks
* Fixed restricting Woo coupon usage by tag
* Fixed WooCommerce auto-discounts not being applied when cart quantities updated
* Fixed loading CRM data into Ultimate Member multi-checkbox fields
* Fixed Mailchimp compatibility with other Mailchimp plugins
* Copper bugfixes

= 3.25.10 - 9/4/2019 =
* Fixed home page not respecting access restrictions in 3.25.8

= 3.25.9 - 9/4/2019 =
* Changed order of apply and remove tags in Woo Subscriptions
* Fixed Hold and Pending Cancel tags not being removed in Woo Subscriptions after a successful payment
* Improved MemberPress expired tagging
* FooEvents compatibility updates
* Fixed tags not being removed with Ontraport

= 3.25.8 - 9/3/2019 =
* Added Salon Booking integration
* Added Custom Post Type UI integration
* Added GDPR Consent and Agreed to Terms fields for syncing with Groundhogg
* Enabled welcome email in MailPoet when a contact is subscribed to a list
* WooCommerce will now use the user email address as the primary email for checkouts by registered users
* Made background worker less susceptible to being blocked
* Improved ActiveCampaign eCom customer lookup
* Fixed content protection on blog index page
* Fixed students getting un-enrolled from LearnDash courses if they were enrolled at the group level and didn't have a course linked tag

= 3.25.7 - 8/26/2019 =
* Added Uncanny LearnDash Groups integration
* Added event_name and venue_name to Event Tickets integration
* Event Tickets bugfixes for RSVP attendees
* Fixed "Create tags from value" option for profile updates
* Fixed initial connection to Groundhogg on Groundhogg < 2.0
* Fixed typo in NationBuilder fields dropdown
* WooCommerce deposits compatibility updates

= 3.25.6 - 8/19/2019 =
* Fix for error trying to get coupons from WooCommerce order on versions lower than 3.7

= 3.25.5 - 8/19/2019 =
* Added ability to create new user meta fields from the Contact Fields list
* Added support for Event Tickets Plus custom fields with WooCommerce
* Added ability to sync event check-ins from Event Tickets Plus to a custom field
* Added "Create tag from value" option to WPForms integration
* Added support for sending full country name in WooCommerce
* Added option to restrict WooCommerce coupon usage by tag
* Improved "Source" column in WPF logs
* Fixed event details not syncing on RSVP with Event Tickets
* Fix for Uncanny LearnDash Groups bulk-enrollment adding contacts with multiple names
* Fixed email address changes with Infusionsoft causing opt-outs
* Reverted asynchronous checkouts to use background queue instead of single request
* Performance improvements on sites with Memberium active

= 3.25.4 - 8/12/2019 =
* Added auto-login by email address for MailerLite
* Added Portuguese translation (thanks @João Alexandre)
* MailerLite will now re-subscribe subscribers when they submit a form
* Improved OAuth access token refresh process with Salesforce
* Access control meta box now requires the manage_options capability
* Fixed variable tags not getting removed during Woo subscription hold if no tags were configured for the main product
* Variable tags will now be removed when a Woo subscription is switched and Remove Tags is enabled
* Fix for WooCommerce Orders export process crashing on deleted products

= 3.25.3 - 8/6/2019 =
* Fixed fatal error in BuddyPress integration when Profile Types module was disabled
* Fixed WooCommerce orders exporter crashing when trying to access a deleted product
* Fixed wpf_woocommerce_payment_complete action not firing on renewal orders

= 3.25.2 - 8/5/2019 =
* Added support for tag linking with BuddyBoss Profile Types
* Added support for restricting access to a single bbPress discussion
* Restricted topics in BuddyBoss / bbPress will now be hidden from the Activity Feed if Filter Queries is on
* Performance improvements when editing WooCommerce Variations
* Performance improvements with Drip and WooCommerce guest checkouts
* Added additional monitoring tools for background process worker
* Cartflows bugfixes for Enhanced Ecommerce addon
* Fixed WooCommerce variable subscription tags not being removed on Hold status
* Fixed bug with borders being output on restricted Elementor widgets
* Fixed bug when sending a store credit with WooCommerce Smart Discounts

= 3.25.1 - 7/29/2019 =
* Added CartFlows integration
* Groundhogg 2.0 compatibility
* Drip site tracking will now auto-identify logged in users
* Added WooCommerce Order Notes field for syncing
* Fixed "Affiliate Approved" tags not being added when creating an AffiliateWP affiliate via the admin

= 3.25 - 7/22/2019 =
* Added MailPoet integration
* Added EDD Software Licensing integration
* Added TranslatePress integration
* Added support for MemberPress Corporate Accounts addon
* Added support for BuddyPress fields to the user_meta shortcode
* Additional tweaks to Austrailian state abbreviations with Ontraport
* Groundhogg tags now update without manual sync
* Fixed FooEvents tags getting removed during Woo Subscriptions renewal

= 3.24.17 - 7/15/2019 =
* Added Tutor LMS integration
* Added option to tag AffiliateWP affiliates on first referral
* WooCommerce integration will no longer apply tags / update meta during a Subscriptions renewal
* Groundhogg will now load tags and meta immediately instead of requiring sync
* Fixed incorrect expiration dates with Paid Memberships Pro
* Improved handling for State fields with Ontraport
* Fixed MemberPress coupon settings not saving
* Added LifterLMS membership start date as a field for syncing
* Dynamic name / SKU tags will now be removed when an order is refunded

= 3.24.16 - 7/8/2019 =
* Added GTranslate integration
* Added Customerly webhooks
* Added social media fields to Kartra
* Added option to remove tags when a page is viewed
* Added automatic SKU tagging in WooCommerce for supported CRMs
* Fixed notifications going out when using the built in import tool
* Restrict Content Pro beta 3.1 compatibility
* Better handling for missing last names in Salesforce
* When a PMPro membership is cancelled / expired the membership level name will be erased in the CRM

= 3.24.15 - 7/1/2019 =
* Added option to completely hide a taxonomy term based on tags
* Added support for built in Ultimate Member fields
* Added option to automatically tag customers based on WooCommerce product names
* Capsule bugfixes
* Bugfixes for Preview with Tag feature
* Fixed syncing changed email addresses with BuddyPress

= 3.24.14 - 6/24/2019 =
* Added new default profile fields for Drip
* Added support for catching Salesforce outbound messages with multiple contact IDs
* Added wpf_salesforce_auth_url filter for Salesforce
* Added date_joined field for Kartra
* Added WooCommerce Subscriptions subscription ID field for syncing
* Added multiselect support for HubSpot
* Added support for File Upload field with Formidable Forms
* Fixed Infusionsoft API errors with addWithDupCheck method
* Bugfixes for Restrict Content Pro 3.0
* Formidable Forms 4.0 compatibility updates
* Slowed down HubSpot batch operations to get around API limits

= 3.24.13 - 6/17/2019 =
* Added option to sync eLearnCommerce auto login token to a custom field
* Mautic performance improvements
* Linked tags from the previous level will now be removed when an RCP membership is manually changed
* Fixed Mautic webhooks failing when the contact ID had changed due to a merge
* Intercom bugfixes
* Groundhogg bugfixes

= 3.24.12 - 6/14/2019 =
* Added option to enable HubSpot site tracking scripts
* Added order_id field for syncing with WooCommerce
* Improved auto enrollment for LearnDash courses
* Reduced API calls required during EDD checkout
* Fixed ConvertKit contact ID lookup failing
* Fixed tags from WooCommerce product attributes getting applied when the attribute wasn't selected

= 3.24.11 - 6/10/2019 =
* Added better handling for ACF relationship fields
* Added password update syncing for MemberPress
* Added option to apply tags when a discount is used in Easy Digital Downloads
* Added option to restrict usage of discounts by tags in Easy Digital Downloads
* Added Last Lesson Completed and Last Course Completed fields for syncing with LifterLMS
* Added Last Lesson Completed and Last Course Completed fields for syncing with LearnDash
* Added unsubscribe notifications for ConvertKit
* Added "wpf_salesforce_auth_url" filter for overriding Salesforce authorization URL
* Restrict Content Pro linked tags will now be removed when a member upgrades
* Improvements to "Return after login" feature
* Fixed creating a contact in Zoho without a last name
* Fixed Beaver Builder elements being hidden from admins
* Fixed Event Tickets Plus tags not applying during WooCommerce checkout
* Fixed Filter Queries "Advanced" mode not working on multiple queries
* Fixed slashes getting added to tags with apostrophes in Mautic
* Tweaks to Filter Queries (Advanced) option
* Prevented linked tags from being re-applied when a Woo membership unenrollment is triggered

= 3.24.10 - 6/3/2019 =
* Added details about configured tags to protected content in post list table
* Added ThriveCart auto login / registration
* Added Pending Payment tags for Event Espresso
* Fixed settings getting reset when enabling ActiveCampaign site tracking

= 3.24.9 - 5/28/2019 =
* Added Email Changed event for Drip
* Fix for tags sometimes not appearing in settings dropdowns

= 3.24.8 - 5/27/2019 =
* Added dynamic tagging based on field values (for supported CRMs)
* Added Is X? fields for NationBuilder
* Added GetResponse support
* Enabled Sequential Upgrade for WishList Member
* Preview With Tag now bypasses Exclude Admins setting
* Fixed WooCommerce checkout not applying tags after an auto login session
* Fixed slashes in image URLs with Gravity Forms multi-file upload fields

= 3.24.7 - 5/20/2019 =
* Added WooCommerce Fields Factory integration
* Added support for syncing WooCommerce attribute selections to custom fields
* Added option to apply tags when an AffiliateWP affiliate is approved
* Added option to disable "Preview With Tag" in admin bar
* Added support for date fields in User Meta Pro
* Fixed bug with Login Meta Sync
* Fixed MailChimp looking up contacts from other lists
* Fixed redirect causing multiple API calls with contact ID lookup in Mautic
* Fixed empty date type fields sending 1/1/1970 dates
* Added WooCommerce order date meta field for syncing

= 3.24.6 - 5/13/2019 =
* Added active lists to list dropdowns with HubSpot
* Removed admin bar JS link rewriting
* Fix for sending 0 in Gravity Forms submissions

= 3.24.5 - 5/9/2019 =
* Fixed tags not applying correctly with Async Checkout when a user registered a new account
* Fixed WooCommerce Subscriptions variation tags not applying
* Toolset fixes for profile updates
* Fix for 3.24.4 turning off Filter Queries setting

= 3.24.4 - 5/6/2019 =
* Added WP Affiliate Manager support
* Added customer tagging for AffiliateWP
* Added Organisation field for syncing to Capsule
* Added "Advanced" mode for Filter Queries setting
* Added support for single checkboxes with Formidable Forms
* Added ability to modify field data formats via the Contact Fields list
* Added IP address when adding new contacts with Mautic
* Added "Add Only" option for Elementor forms
* Added option to restrict visibility of EDD price options
* Paid Memberships Pro now sends meta data before applying tags
* Deleting a WooCommerce Subscription will no longer apply Cancelled tags
* Fixed auto-enrollments into MemberPress membership levels via webhook not returning passwords
* Fixed "Expired" tags not applying with MemberPress
* Fixed date formatting with HubSpot
* Fixed syncing date fields with Capsule
* Compatibility updates for custom field formatting with Mailerlite

= 3.24.3 - 4/29/2019 =
* Added option to return people to originally requested content after login
* Added Contact ID merge field to Gravity Forms
* Improved Preview With Tag functionality
* Auto login with Mailchimp now works with email address
* WooCommerce Transaction Failed tags will now be removed after a successful checkout
* Limit logging table to 10,000 rows
* Copper bugfixes
* Fix for error when using GForms User Registration during an auto login session

= 3.24.2 - 4/22/2019 =
* Added Caldera Forms integration
* Added additional status tags for Restrict Content Pro
* Changed Woo taxonomy tagging to just use the Category taxonomy
* Modified async checkouts to use a remote post instead of AJAX
* WPForms bugfixes
* Platform.ly bugfixes
* Consolidated forms functionality into new WPF_Forms_Helper class

= 3.24.1 - 4/16/2019 =
* Fix for Paid Memberships Pro checkout error

= 3.24 - 4/15/2019 =
* Added Sendlane CRM integration
* Added WooCommerce category tagging
* Added AgileCRM site tracking scripts
* Added support for BuddyPress taxonomy multiselect fields
* Fixed expiration tags in Paid Memberships Pro
* Fixed MemberPress auto-enrollments setting expiration date in the past
* Fixes for multiselects in BuddyPress
* Fixes for XProfile fields on secondary field groups

= 3.23.7 - 4/8/2019 =
* Added account deactivation tag trigger for Ultimate Member
* Added WooCommerce Wholesale Lead Capture support
* Toolset forms compatibility updates
* Fixed logic error with "Required Tags (all)" setting
* Fixed Preview With Tag functionality in Beaver Builder
* Updated AWeber subscriber ID lookup to only use selected list

= 3.23.6 - 4/1/2019 =
* Added Teams for WooCommerce Memberships integration
* Added unit completion tagging for WP Courseware
* Added Organization Name field for ActiveCampaign
* LearnPress compatibility updates
* Better AWeber exception handling
* AccessAlly bug fixes
* Bugfixes for PeepSo and auto login sessions
* Fix for changing email addresses with Drip
* Fix for AffiliateWP affiliate data not being synced when Auto Register Affiliates was enabled

= 3.23.5 - 3/25/2019 =
* Added LifterLMS quiz tagging (thanks @thomasplevy)
* Added ability to restrict usage of EDD discount codes (thanks @pjeby)
* Added merge settings option to bulk edit
* Added setting to remove "Additional Fields" section from settings
* Added "hide" option to Convert Pro targeting rules
* Expired / Cancelled / etc tags will now be removed when an EDD subscription is re-activated
* Popup Maker compatibility updates
* AccessAlly bug fixes
* Fix for failed WooCommerce order blocking tagging on subsequent successful re-try
* Fix for Required Tags (all) option greyed out
* Paid Memberships Pro bugfixes

= 3.23.4 - 3/18/2019 =
* Added Convert Pro CTA targeting integation
* Added FooEvents integration
* Added date-format parameter to user_meta shortcode
* Added "Required tags (all)" option to post restriction meta box
* Added option for login meta sync
* Added option for tagging when WooCommerce orders fail on initial payment
* Improved pagination in WPF logs
* Mailerlite bugfixes
* Improved HubSpot error logging
* MemberPress expired tagging bugfixes
* Fix for restricting BuddyPress pages

= 3.23.3 - 3/1/2019 =
* Fixed bug in MailerLite integration

= 3.23.2 - 3/1/2019 =
* Added Event Espresso integration
* Restrict Content Pro v3.0 compatibility fixes
* Added additional status triggers for Mailerlite webhooks
* Fixes for wpf_user_can_access filter
* ConvertKit fixes for unconfirmed subscribers

= 3.23.1 - 2/25/2019 =
* CoursePress integration
* Added incoming webhook test tool
* Added WooCommerce Subscriptions Meta batch operation
* Improved Ontraport site tracking script integration
* MemberPress will now remove the payment fail tag when a payment succeeds
* Bugfixes for CartFlows upsells with WooCommerce
* Fix for syncing checkbox fields in Elementor forms
* Fix for MailerLite accounts syncing more than 100 groups
* Fix for syncing profile updates via Gravity Forms
* Fixes for Free Trial Over tags in WooCommerce Subscriptions

= 3.23 - 2/18/2019 =
* Added Mailjet CRM integration
* Added payment failed tagging for MemberPress
* Javascript bugfix for tags with apostrophes in them
* Changes to WooCommerce variations data storage
* Added option to only allow auto-login after form submission
* Fix for email addresses with + sign in MailChimp
* Fix for changed checkout field names in Paid Memberships Pro
* Fix for contact ID lookup with HubSpot
* Fix for background worker when PHP's memory_limit is set to -1
* Added ability to restrict WooCommerce Shop page
* bbPress template compatibility fixes

= 3.22.3 - 2/12/2019 =
* Added tags for Expired status in MemberPress
* Added admin users column showing user tags
* Added fields for syncing Woo Subscriptions subscription name and next payment date
* Option to hide Woo coupon field on Cart / Checkout (used with auto-applying coupons)
* Fix for restricted WooCommerce products showing "password protected" message

= 3.22.2 - 2/5/2019 =
* Elementor Popups integration
* Added ability to auto-apply discounts via tag with WooCommerce
* Added option to embed Mautic site tracking scripts
* Added Mautic mtc_id cookie tracking for known contacts
* Additional Woo Memberships statuses for tagging
* Comments are now properly hidden when a post is restricted and no redirects are specified
* Set 1 second sleep time for Drip batch processes to avoid API timeouts
* Platform.ly bugfixes
* Platform.ly webhooks added
* Fixes for custom objects with Ontraport
* Fixes for WooCommerce Deposits not tagging properly

= 3.22.1 - 1/31/2019 =
* Groundhogg bugfixes
* Drift tagging bugfixes
* WooCommerce 2.6 compatibility fixes
* Woo Subscriptions tagging bugfixes

= 3.22 - 1/28/2019 =
* NationBuilder CRM integration
* Groundhogg CRM integration
* Added batch processing tool for WooCommerce Memerships
* Added pagination to AccessAlly settings page
* Added additional AffiliateWP registration fields for sync
* Fix for Sendinblue not creating contacts if custom attributes weren't present
* Fix for being unable to remove tags from Woo variations
* Fix for Woo variations not saving correctly with Woo Memberships active
* Fix for imports larger than 50 with Capsule

= 3.21.2 - 1/21/2019 =
* Added Clean Login support
* Added Private Messages integration
* Added custom fields support for Kartra
* Added AffiliateWP referrer ID field for syncing
* Added Toggle field support for Formidable Forms
* Added PeepSo VIP Icons support
* Added Gist webhooks support
* Moved Formidable Forms settings to "Actions" to support conditions
* Fix for custom fields not syncing with MemberMouse registration
* Fix for missing Ninja Forms settings fields
* Fix for syncing multiselects / picklists with Zoho
* Fix for error when processing Woo Subscriptions payment status hold
* Fix for AJAX applying tags by tag ID
* Fix for wpf_update_tags shortcode in auto-login sessions
* Fix for error creating contacts in Intercom without any custom fields
* Additional Capsule fields / Capsule field syncing bugfixes
* Better internationalization support
* Added PHP version notice for sites running less than 5.6

= 3.21.1 = 1/14/2019 =
* Elementor Forms integration
* Advanced Ads support
* WooCommerce Addons v3.0 support
* Additional tagging options for WooCommerce Memberships
* Fix for variation tags sometimes being lost when saving a Woo product
* Support for updating Capsule email/phone/address fields without a type specifier
* Added tagging for when a LearnDash essay is submitted
* Allow for using tag labels in link click tracking

= 3.21 - 1/5/2019 =
* Copper CRM integration
* Fixes for syncing PeepSo account fields
* Fixes for LearnDash quiz results tagging with Essay type questions
* Fix for incomplete address error with MailChimp
* Support for syncing with unsubscribed subscribers in ConvertKit
* Fixes for user IDs in ConvertFox (Gist)
* Bugfix for logged-out behavior in Elementor
* Added "Process WP Fusion actions again" option to WooCommerce Order Actions
* PHP 5.4 fixes

= 3.20.4 - 12/22/2018 =
* Fixed "return value in write context" error in PHP 5.5

= 3.20.3 - 12/22/2018 =
* Added logged-out behavior to Elementor
* Added support for syncing roles when a user has multiple roles
* Added Pull User Meta batch operation
* Added support for picklist fields in Zoho
* Fix for syncing MemberPress membership level name during batch process
* Additional logging for WC Subscriptions status changes
* Added import by Topic for Salesforce
* Admin settings update to support Webhooks

= 3.20.2 - 12/14/2018 =
* Fix for JS error with Gutenberg block

= 3.20.1 - 12/14/2018 =
* Added Gutenberg content restriction block
* Better first name / last name handling for ConvertFox
* Fix for Event Tickets settings not saving

= 3.20 - 12/8/2018 =
* Autopilot CRM integration
* Customerly CRM integration
* Added Ninja Forms integration
* Added option for per-post restricted content messages
* Added user_registered date field for syncing
* Added option to sync MemberPress membership level name at checkout
* Added handling for changed contact IDs with Infusionsoft
* Userengage bugfixes
* Fix for BuddyPress multi-checkbox fields not syncing
* Fix for PeepSo group members not getting fully removed from groups
* Fix for MemberMouse password resets not syncing
* Reverted to earlier method for getting Woo checkout fields to prevent admin errors in WPF settings
* Fixed bug where bulk-editing pages would remove WPF access rules

= 3.19 - 11/29/2018 =
* Drift CRM integration
* wpForo integration
* "Give" plugin integration
* Bugfixes for MemberPress coupons
* Better support for Gravity Forms User Registration
* UserEngage bugfixes
* Fixed compatibility bugs with other plugins using Zoho APIs
* Added wpf_batch_sleep_time filter
* Better user meta handling on auto-login sessions

= 3.18.7 - 11/21/2018 =
* Popup Maker integration
* GamiPress linked tag bugfixes
* Added import tool for Mautic
* Added support for updating email addresses in Kartra

= 3.18.6 - 11/15/2018 =
* WPForms integration
* UserEngage bugfixes
* Ability to set WooCommerce product tags to apply at the taxonomy term level
* Fix for incorrect membership start date with Paid Memberships Pro

= 3.18.5 - 11/12/2018 =
* Fixed bug with WooCommerce that caused WPF settings page not to load

= 3.18.4 - 11/10/2018 =
* WPComplete integration
* Added async method for batch webhook operations
* Fix for restricted WooCommerce variations not showing in admin when Filter Queries is enabled
* Bugfixes for detecting WooCommerce custom checkout fields
* Added payment conditions for Stripe and PayPal for Gravity Forms
* Now allows updating PeepSo role by changing field value in CRM

= 3.18.3 - 10/27/2018 =
* Added batch processing tool for Gravity Forms entries
* Fixed outbound message endpoint creating error messages in Salesforce
* Better support for custom checkout fields in WooCommerce
* LifterLMS course/membership auto-enrollment tweaks
* Added Payment Failed option to Woo Subscriptions

= 3.18.2 - 10/22/18 =
* Added support for Salesforce topics
* Added tagging for MemberPress coupons
* Added option to sync user tags on login
* Added support for multi-checkboxes to Gravity Forms integration
* Capsule bugfixes

= 3.18.1 - 10/14/2018 =
* Added Weglot integration
* Restrict Content Pro bugfixes
* Kartra bugfixes for WooCommerce guest checkouts
* Divi integration bugfixes
* More flexible Staging mode

= 3.18 - 10/4/2018 =
* Added Platform.ly support
* Added logged in / logged out shortcodes
* Added option to choose contact layout for new contacts with Zoho
* Fix for AgileCRM campaign webhooks
* Fixes for checkboxes with Profile Builder
* WooCommerce Addons bugfixes
* Added custom fields support for Intercom

= 3.17.2 - 9/22/2018 =
* Added Divi page builder support
* Added update_tags endpoint for webhooks
* Fix for "restrict access" checkbox not unlocking inputs correctly
* Fix for import button not working in admin
* Cleaned up WooCommerce settings storage

= 3.17.1 - 9/17/2018 =
* Added support for WooCommerce Addons
* Improved leadsource tracking
* Added webhooks support for SalesForce
* Bugfixes for ConvertKit with email addresses containing "+" symbol
* Support for syncing passwords generated by EDD Auto Register
* Fix for MailChimp syncing tags limited to 10 tags
* Additional sanitizing of input data

= 3.17 - 9/4/2018 =
* HubSpot integration
* SendinBlue bugfixes
* Zoho authentication bugfixes
* Profile Builder bugfixes
* Added support for Paid Memberships Pro Approvals
* Added option for applying a tag when a contact record is updated
* Support for Gravity Forms applying local tags during auto-login session

= 3.16 - 8/27/2018 =
* Added MailChimp integration
* Added SendinBlue CRM integration
* Easy Digital Downloads 3.0 support
* Profile Builder Pro bugfixes

= 3.15.3 - 8/23/2018 =
* Added Profile Builder Pro integration
* AccessAlly integration
* WPML integration
* Added "wpf_crm_object_type" filter for Salesforce / Zoho / Ontraport
* Fix for date fields with Salesforce
* Improvements to logging display for API errors
* Added Elementor controls to sections and columns
* Support for multi-checkbox fields with Formidable Forms

= 3.15.2 - 8/12/2018 =
* Fix for applying tags via Gravity Form submissions with ConvertKit
* Fixed authentication error caused by resyncing tags with Salesforce
* Added Job Alerts support for WP Job Manager
* Auto-login session will now end on WooCommerce cart or checkout

= 3.15.1 - 8/3/2018 =
* WooCommerce memberships bugfixes
* Fixed PeepSo groups table limit of 10 groups
* Option to sync expiry date for WooCommerce Memberships
* Beaver Builder fix for visibility issues
* WooCommerce Checkout Field Editor Integration
* Added "remove tags" checkbox for EDD recurring price variations
* Maropost CRM integration

= 3.15 - 7/23/2018 =
* Tubular CRM integration
* Flexie CRM integration
* Added tag links for PeepSo groups
* Elementor integration
* WishList Member bugfixes

= 3.14.2 - 7/15/2018 =
* Added WPLMS support
* Improved syncing of multi-checkboxes with ActiveCampaign
* Added support for Paid Memberships Pro Registration Fields Helper add-on

= 3.14.1 - 7/3/2018 =
* Auto-login tweaks for Gravity Forms
* Added option to apply tags on LearnDash quiz fail
* LearnDash bugfixes
* Improvements to AgileCRM imports by tag
* Kartra API updates
* Allowed loading PMPro membership start date and end date from CRM
* MemberMouse syncing updates from admin edit member profile

= 3.14 - 6/23/2018 =
* UserEngage CRM integration
* Fix for auto-login links with AgileCRM
* Added refund tags for price IDs in Easy Digital Downloads
* Added leadsource tracking support for Gravity Forms form submissions
* Added "not" option for Beaver Builder content visibility
* Added access controls to bbPress topics

= 3.13.2 - 6/17/2018 =
* Added support for tagging on subscription status changes for EDD product variations
* Added support for syncing WooCommerce Smart Coupons coupon codes
* Fixed Salesflare address fields not syncing
* Improvements on handling for changed email addresses in MailerLite
* Fix for LifterLMS access plan tags not displaying correctly
* Fix for foreign characters in state names with Mautic

= 3.13.1 - 6/10/2018 =
* Gravity Forms bugfix

= 3.13 - 6/10/2018 =
* Salesflare CRM integration
* Corrected Kartra App ID
* Added option to show excerpts of restricted content to search engines
* Fix for refund tags not being applied in WooCommerce for guest checkouts
* Fix for issues with linked tags not triggering enrollments while running batch processes
* Ability to pause a MemberMouse membership by removing a linked tag
* Bugfixes for empty tags showing up in select
* Better handling for email address changes with MailerLite
* Salesforce bugfixes

= 3.12.9 - 6/2/2018 =
* Added "apply tags" functionality for Restrict Content Pro
* Added tag link for Gamipress achievements
* Added points syncing for Gamipress
* Added support for WooCommerce Smart Coupons
* Fix for "refund" tags getting applied when a WooCommerce order is set to Cancelled
* Fix for LifterLMS "Tag Link" adding a blank tag
* Removed ability to add tags from within WP for Ontraport
* Gravity Forms bugfix for creating new contacts from form submissions while users are logged in
* Support for Tribe Tickets v4.7.2

= 3.12.8 - 5/27/2018 =
* Added GDPR "Agree to terms" tagging for WooCommerce
* BuddyPress bugfixes
* Added ability to apply tags when a coupon is used in Paid Memberships Pro
* Ultimate Member 2.0 fix for tags not being applied at registration
* Bugfix for tags sometimes not saving correctly on widget controls

= 3.12.7 - 5/19/2018 =
* Beaver Builder integration
* Ultimate Member 2.0 bugfixes
* Added delay to Kartra contact creation to deal with slow API performance
* Fix for Kartra applying tags to non-registered users
* Support creating tags from within WP Fusion for Ontraport
* Added delay in WooCommerce Subscriptions renewal processing so tags aren't removed and reapplied during renewals
* Changed template_redirect priority to 15 so it runs after Force Login plugin

= 3.12.6 - 5/16/2018 =
* Bugfix for errors showing when auto login session starts

= 3.12.5 - 5/15/2018 =
* Added support for WooCommerce Deposits
* Added event location syncing for Tribe Tickets Plus
* Added BadgeOS points syncing
* WP Courseware settings page fix for version 4.3.2
* Added option to only log errors (instead of all activity)
* Bugfix for WooCommerce checkout not working properly during an auto-login session

= 3.12.4 - 5/6/2018 =
* Added event date syncing for Tribe Tickets Plus events with WooCommerce
* Fix for Zoho customers with EU accounts
* Support for syncing passwords automatically generated by LearnDash
* Restrict Content Pro bugfixes
* UM 2.0 bugfixes
* Allowed for auto-login using Drip's native ?__s= tracking link query var
* Fix for syncing to date type custom fields in Ontraport

= 3.12.3 - 4/28/2018 =
* Bugfix for "undefined constant" message on admin dashboard

= 3.12.2 - 4/28/2018 =
* Better support for query filtering for restricted posts
* Fixed a bug that caused tags not to be removed properly in Ontraport
* Fixed a bug that caused tags not to apply properly on LifterLMS membership registration
* Fixed a bug with applying tags when achievements are earned in Gamipress
* Fixed a bug with syncing password fields on ProfilePress registration forms
* Additional error handling for import functions

= 3.12.1 - 4/12/2018 =
* ProfilePress integration
* Added option to apply tags when a user is deleted
* Added setting for widgets to *hide* a widget if a user has a tag
* Added option to apply tags when a LifterLMS access plan is purchased
* More robust API error handling and reporting
* Fixed a bug in MailerLite where contact IDs wouldn't be returned for new users

= 3.12 - 3/28/2018 =
* Added Zoho CRM integration
* Added Kartra CRM integration
* Added ConvertFox CRM integration
* Added WP Courseware integration
* Changed WooCommerce order locking to use transients instead of post meta values
* Added membership role syncing to PeepSo integration
* Added User ID as an available field for sync

= 3.11.1 - 3/21/2018 =
* Added GamiPress integration
* Added PeepSo integration
* Added option to just return generated passwords on import, without requiring ongoing password sync
* "Push user meta" batch operation now pushes Paid Memberships Pro meta data correctly
* Fixed bug where ampersands would fail to send in Infusionsoft contact updates
* Cleaned up scripts and styles in admin settings pages

= 3.11 - 3/15/2018 =
* Capsule CRM integration
* Added LearnPress LMS integration
* Added batch-resync tool for LifterLMS memberships
* Tags linked to LearnDash courses will now be applied / removed when a user is manually added to / removed from a course
* Bugfixes for export batch operation
* Added "Pending Cancellation" tags for WooCommerce Subscriptions
* Improved handling for displaying user meta when using auto-login links
* Fix for AWeber API configuration errors breaking setup tab
* Improved AgileCRM handling for custom fields
* Added filter for overriding WPEP course buttons for restricted courses

= 3.10.1 - 3/3/2018 =
* Fixed a bug where sometimes a contact ID wouldn't be associated with an existing contact when a new user registers
* Added start date syncing for Paid Memberships Pro

= 3.10 - 2/24/2018 =
* MailerLite CRM integration
* Bugfixes for auto-login links with Gravity Forms
* MemberMouse bugfixes

= 3.9.3 - 2/19/2018 =
* Added option for auto-login after Gravity Form submission
* Changed auto-login links to use cookies instead of sessions
* Allowed the [user_meta] shortcode to work with auto-login links
* Modified Infusionsoft contact ID lookup to just use primary email field

= 3.9.2 - 2/15/2018 =
* Proper state and country field handling for Mautic
* Fix for malformed saving of Tag Link field in LifterLMS course settings

= 3.9.1 - 2/12/2018 =
* Added "Apply Tags - Cancelled" to Paid Memberships Pro settings
* Added Ontraport affiliate tracking
* Added Ontraport page tracking
* Improved LearnDash content restriction filtering
* Optimized unnecessary contact ID lookups when Push All User Meta was enabled

= 3.9 - 1/31/2018 =
* Added AWeber CRM integration
* Linked tags now automatically added / removed on LearnDash group assignment
* Added auto-enrollment for LifterLMS courses
* Added post-checkout process locking for WooCommerce to reduce duplicate transactions

= 3.8.1 - 1/21/2018 =
* Added [else] method to shortcodes
* Added loggedout method to shortcodes
* Performance enhancements
* ConvertKit now auto-removes webhook tags
* Added option to apply tags when a WooCommerce subscription converts from free to paid

= 3.8 - 1/8/2018 =
* Intercom CRM integration
* myCRED integration
* Added bulk import for Salesforce
* Added batch processing for s2Member
* Fixed bug with administrators not being able to view content in a tag-restricted taxonomy

= 3.7.6 - 12/30/2017 =
* Added batch processing tool for MemberPress subscriptions
* Added setting to exclude restricted posts from archives / indexes
* Added ActiveCampaign site tracking
* Added Infusionsoft site tracking
* Added Drip site tracking

= 3.7.5 - 12/21/2017 =
* WooCommerce bugfixes

= 3.7.4 - 12/15/2017 =
* Improvements to tag handling with ConvertKit
* Added collapsible table headers to Contact Fields table
* Fixed bug in Mautic with applying tags to new contacts
* UserPro bugfixes

= 3.7.3 =
* Added global setting for tags to apply for all WooCommerce customers
* Fixed issue with restricted WooCommerce variations not being hidden
* Fixed bug with syncing Ultimate Member password updates from the Account screen
* Fixed LifterLMS account updates not being synced

= 3.7.2 =
* UserPro bugfixes
* Fixed hidden Import tab

= 3.7.1 =
* Fix for email addresses not updating on CRED profile forms
* Fix for Hold / Failed / Cancelled tags not being removed on WooCommerce subscription renewal

= 3.7 =
* Added support for the Mautic marketing automation platform
* Toolset CRED integration (for custom registration / profile forms)
* Fix for newly added tags not saving to WooCommerce variations

= 3.6.1 =
* Updated for compatibility with Ontraport API changes

= 3.6 =
* WishList Member integration
* Fixed tag fields sometimes not saving on WooCommerce variations
* Added async checkout for EDD purchases

= 3.5.2 =
* Improvements to filtering products in WooCommerce shop
* Significantly sped up and increased reliability of WooCommerce Asynchronous Checkout functionality
* Added ability to apply tags when refunded in EDD
* Better Tribe Events integration

= 3.5.1 =
* Improvements to auto login link system
* Added duplicating Gravity Forms feeds
* Restrict Content Pro bugfixes
* Added admin tools for resetting wpf_complete hooks on WooCommerce / EDD orders

= 3.5 =
* Added support for Ultimate Member 2.0 beta
* Added Tribe Events Calendar support (including support for Event Tickets and Event Tickets Plus)
* Added list selection options for Gravity Forms with ActiveCampaign
* Fixed variable tag fields not saving in WooCommerce
* Fixed new user notification emails sometimes not going out
* ActiveCampaign API performance enhancements

= 3.4.1 =
* Bugfixes

= 3.4 =
* Added access controls for widgets
* Improved "Preview with Tag" reliability
* WooCommerce now sends country name correctly to Infusionsoft
* Added logging support for Woo Subscriptions
* Support for additional BadgeOS achievement types
* Support for switching subscriptions with Woo Subscriptions
* Added batch processing options for Paid Memberships Pro
* Fixed issue with shortcodes using some visual page builders

= 3.3.3 =
* Added BadgeOS integration
* Staging mode now works with logging tool
* "Apply to children" now applies to nested children
* Added backwards compatibility support for WC < 3.0
* Passwords auto-generated by WooCommerce can now be synced
* Fixed issues with MemberPress non-recurring products
* Updated EDDSL plugin updater
* Fixes for Gravity Forms User Registration add-on
* Cleaned up internal fields from Contact Fields screen
* Sped up Import tool for Drip
* Option to disable API queue framework for debugging

= 3.3.2 =
* ConvertKit imports no longer limited to 50 contacts
* Restrict Content Pro improvements
* Fixed bug when adding new tags via tag select dropdown
* Fixed bug with using tag names in wpf shortcode on some CRMs
* Importing users now respects specified role
* Fixed error saving user profile when running BuddyPress with Groups disabled

= 3.3.1 =
* 3.3 bugfixes

= 3.3 =
* New features:
	* Added new logging / debugging tools
	* Contact Fields list is now organized by related integration
	* Added options for filtering users with no contact ID or no tags
	* Added ability to restrict WooCommerce variations by tag
* New Integrations:
	* WooCommerce Memberships
	* Simple Membership plugin integration
	* WP Execution Plan LMS integration
* New Integration Features:
	* MemberMouse memberships can now be linked with a tag
	* Expiration Date field syncing for Restrict Content Pro subscriptions
	* BuddyPress groups can now be linked with a tag
	* Added Payment Method field for sync with Paid Memberships Pro
	* Expiration Date can now be synced for Paid Memberships Pro
	* Added registration date, expiration date, and payment method for MemberPress subscriptions
	* Added "Apply tags when cancelled" field to MemberPress subscriptions
* Bug fixes:
	* Fixed bugs with editing tags via the user profile
	* user_meta Shortcode now pulls data from wp_users table correctly
	* "Apply on view" tags will no longer be applied if the page is restricted
	* Link with Tag fields no longer allow overlap with Apply Tags fields in certain membership integrations
	* AgileCRM fixes for address fields
* Enhancements:
	* Optimized many duplicate API calls
	* Added Dutch and Spanish translation files

= 3.2.1 =
* Bugfixes

= 3.2 =
* Salesforce integration
* Fixed issue with automatically assigning membership levels in MemberPress via webhook
* Fixed incompatibility with Infusionsoft Form Builder plugin
* Improvements to Drip integration
* Improvements to WooCommerce order batch processing tools
* Numerous bugfixes and performance enhancements

= 3.1.3 =
* Drip CRM can now trigger new user creation via webhook
* User roles now update properly when changed via webhook
* Import tool can now import more than 1000 contacts from Infusionsoft
* Gravity Forms bugfixes
* WP Engine compatibility bugfixes

= 3.1.2 =
* Added filter by tag option in admin Users list
* Added ability to restrict all posts within a restricted category or taxonomy term
* Added ability to restrict all bbPress forums at a global level
* Fixed bug with Ultimate Member's password reset process with Infusionsoft
* Added additional Google Analytics fields to contact fields list
* Bugfix to prevent looping when restricted content is set to redirect to itself

= 3.1.1 =
* Fixed inconsistencies with syncing user roles
* Additional bugfixes for WooCommerce 3.0.3

= 3.1.0 =
* Added built in user meta shortcode system
* Added support for webhooks with ConvertKit
* Updates for WooCommerce 3.0
* Additional built in fields for Agile CRM users
* Fixed bug where incorrect tags would be applied during automated payment renewals
* Fixed debugging log not working

= 3.0.9 =
* Added leadsource tracking to new user registrations for Google Analytics campaigns or custom lead sources
* Link click tracking can now be used on other elements in addition to links
* Agile CRM API improvements
* Misc. bugfixes

= 3.0.8 =
* Drip bugfixes
* Agile CRM improvements and bugfixes
* Added EDD payments to batch processing tools
* Added EDD Recurring Payments to batch processing tools
* Misc. UI improvements
* Bugfixes and speed improvements to batch operations

= 3.0.7 =
* Integration with User Meta plugin
* Fixed bug where restricted page would be shown if no redirect was specified
* Better support for Ultimate Member "checkboxes" fields

= 3.0.6 =
* Import tool has been updated to use new background processing system
* Added WordPress user role to list of meta fields for sync
* Support for additional Webhooks with Agile CRM
* Bugfix for long load times when getting user tags

= 3.0.5 =
* New tags will be loaded from the CRM if a user is given a tag that doesn't exist locally
* Resync contact IDs / Tags moved from Resynchronize button process to Batch Operations
* ActiveCampaign integration can now load all tags from account (no longer limited to first 100)
* Bugfix for LifterLMS memberships tag link

= 3.0.4 =
* Paid Memberships Pro bugfixes

= 3.0.3 =
* WP Job Manager integration
* Added category / taxonomy archive access restrictions
* Tags can now be added/removed from the edit user screen
* Added tooltips with additional information to batch processing tools
* Batch processes now update in real time after reloading WPF settings page

= 3.0.2 =
* Bugfixes for version 3.0

= 3.0.1 =
* Bugfixes for version 3.0

= 3.0 =
* Added Formidable Forms integration
* Added bulk editing tools for content protection
* New admin column for showing restricted content
* New background worker for batch operations on sites with a large number of users
* Tags are now removed properly when WooCommerce order refunded / cancelled
* Added option to remove tags when LifterLMS membership cancelled
* Added "Tag Link" capability for Paid Memberships Pro membership levels
* User roles can now be updated via the Update method in a webhook or HTTP Post
* Introduced beta support for Drip webhooks
* Initial sync process for Drip faster and more comprehensive
* All integration functions are now available via wp_fusion()->integrations
* Updated and improved automatic updates
* Numerous speed optimizations and bugfixes

= 2.9.6 =
* Improved integration with Paid Memberships Pro and Contact Form 7
* Bugfix for Radio type fields with Ultimate Member

= 2.9.5 =
* Added "Staging Mode" - all WP Fusion functions available, but no API calls will be sent
* Added Advanced settings pane with debugging tools

= 2.9.4 =
* LifterLMS bugfixes
* Deeper MemberPress integration

= 2.9.3 =
* Support for Asian character encodings with Infusionsoft
* Improvements to Auto-login links for hosts that don't support SESSION variables

= 2.9.2 =
* Misc. bugfixes

= 2.9.1 =
* Added support for MemberPress
* Updates for WooCommerce Subscriptions 2.x

= 2.9 =
* AgileCRM CRM support
* Added support for Thrive Themes Apprentice LMS
* Added support for auto-login links
* Added ability to apply tags when a link is clicked

= 2.8.3 =
* Allows shortcodes in restricted content message

= 2.8.2 =
* Fix for users being logged out when syncing password fields
* Ontraport bugifxes and performance tweaks
* Better error handling and debugging information for webhooks

= 2.8.1 =
* Added option for customizing restricted product add to cart message
* Misc. bug fixes

= 2.8 =
* ConvertKit CRM support
* LifterLMS updates to support LLMS 3.0+
* Ability to apply tags for LifterLMS membership levels
* Restricted Woo products can no longer be added to cart via URL

= 2.7.5 =
* Fixed Infusionsoft character encoding for foreign characters
* Fixed default field mapping overriding custom field selections

= 2.7.4 =
* Fixed bug where tag select boxes on LearnDash courses were limited to one selection

= 2.7.3 =
* Fixed bugs where ActiveCampaign lists would be overwritten on contact updates
* Restricted menu items no longer hidden in admin menu editor
* Improved s2Member support
* Fix for applying tags with variable WooCommerce subscriptions

= 2.7.2 =
* Added s2Member integration
* Added support for applying tags when WooCommerce coupons are used
* Added support for syncing AffiliateWP affiliate information
* Fixed returning passwords for imported contacts
* Updates for compatibility with plugin integrations

= 2.7.1 =
* Added LifterLMS support
* Fix for password updates not syncing from UM Account page

= 2.7 =
* Added Restrict Content Pro Integration
* Tag mapping for LearnDash Groups
* Can now sync user password from Ultimate Member reset password page

= 2.6.8 =
* Fix for contact fields not getting correct defaults on first install
* Fixed wrong lists getting assigned when updating AC contacts
* Significant API performance optimizations

= 2.6.7 =
* Enabled webhooks from Ontraport

= 2.6.6 =
* Fixed error in GForms integration

= 2.6.5 =
* Added support for syncing PMPro membership level name
* Fixed tags not applying when WooCommerce orders refunded
* Bugfixes and performance optimizations

= 2.6.4 =
* Batch processing tweaks

= 2.6.3 =
* Admin performance optimizations
* Batch processing / export tool

= 2.6.2 =
* Fix for tag select not appearing under Woo variations
* Formatting filters for date fields in ActiveCampaign
* Added quiz support to Gravity Forms
* Optimizations and performance tweaks

= 2.6.1 =
* Drip bugfixes
* Fix for restricted WooCommerce products not being hidden on some themes

= 2.6 =
* Added Drip CRM support
* Option to run Woo checkout actions asynchronously

= 2.5.5 =
* Updates to support Media Tools Addon

= 2.5.4 =
* Added option to push generated passwords back to CRM
* Added ability to apply tags in LearnDash when a quiz is marked complete
* Added ability to link a tag with an Ultimate Member role for automatic role assignment

= 2.5.3 =
* Fixed bug with WooCommerce variations and user-entered tags
* Fixed BuddyPress error when XProfile was disabled

= 2.5.2 =
* Fix for license activations / updates on hosts with outdated CURL
* Updates to support WPF addons
* Re-introduced import tool for ActiveCampaign users
* PHP 7 optimizations

= 2.5.1 =
* Improvements to initial ActiveCampaign sync
* Added instructions for AC import

= 2.5 =
* Added Paid Memberships Pro support
* Added course / tag relationship mapping for LearnDash courses
* Added automatic detection and mapping for BuddyPress profile fields
* Added "Apply tags when refunded" option for WooCommerce products
* Updated HTTP status codes on HTTP Post responses
* Tweaks to Import function for Ontraport users
* Fix for duplicate contacts being created on email address change with ActiveCampaign
* Fix for resyncing contacts with + symbol in email address

= 2.4.1 =
* Bugfixes for Ontraport integration
* Added Contact Type field mapping for Infusionsoft

= 2.4 =
* Added Ontraport CRM integration

= 2.3.2 =
* MemberMouse beta integration
* Fix for license activation for users on outdated versions of CURL / SSL
* Fix for BuddyPress pages not locking properly

= 2.3.1 =
* Fixed error in bbPress integration on old PHP versions

= 2.3 =
* Added Contact Form 7 support
* All bbPress topics now inherit permissions from their forum
* Added ability to lock bbPress forums archive
* Fixed bug with importing users by tag
* Fixed error with shortcodes using Thrive Content Builder
* Removed Add to Cart links for restricted products on the Woo store page
* Added option to hide restricted products from Woo store page entirely
* Added support for applying tags based on EDD variations

= 2.2.2 =
* Fix for tag shortcodes on AC
* Improvements to tag selection on Woo subscriptions / variations
* Woo Subscription fields now show on variable subscriptions as well
* Updated included Select2 libraries
* Restricted content with no tags specified will now be restricted for non-logged-in-users

= 2.2.1 =
* Fixed fatal error with GForms integration on lower PHP versions

= 2.2 =
* Added support for re-syncing contacts in batches for sites with large numbers of users
* Added support for ActiveCampaign webhooks
* Added support for EDD Recurring Payments
* Simplified URL structure for HTTP POST actions and added debugging output
* Fix for "0" tag appearing with ActiveCampaign tags

= 2.1.2 =
* Fixed bug where AC profiles wouldn't update if email address wasn't present in the form
* Fix for redirect rules not being respected for admins
* Fix for user_email and display_name not updating via HTTP Post

= 2.1.1 =
* Fixed bug affecting [wpf] shortcodes with users who had no tags applied

= 2.1 =
* Added support for applying tags in Woo when a subscription expires, is cancelled, or is put on hold
* Added "Push All" option for incompatible plugins and "user_meta" updates triggered via functions
* Fix for ActiveCampaign accounts with no tags
* Isolated AC API to prevent conflicts with plugins using outdated versions of the same API

= 2.0.10 =
* Bugfix when using tag label in shortcode

= 2.0.9 =
* Fix for tag checking logic with shortcode

= 2.0.8 =
* Fix for has_tag() function when using tag label
* Fixes for conflicts with other plugins using older versions of Infusionsoft API
* Support for re-adding contacts if they've been deleted in the CRM

= 2.0.7 =
* Resync contact now deletes local data if contact was deleted in the CRM
* Update license handler to latest version
* Resynchronize now force resets all tags
* Moved upgrade hook to later in the admin load process

= 2.0.6 =
* Support for manually marking WooCommerce payments as completed
* Improved support for servers with limited API tools
* Fixed wp_fusion()->user->get_tag_id() function to work with ActiveCampaign
* Bugfixes to shortcode content restriction system
* Fix for fields with subfields occasionally not showing up in GForms mapping
* Fix for new Ultimate Member field formats

= 2.0.5 =
* Fix for user accounts not created properly when WooCommerce and WooSubscriptions were both installed
* Added "apply to related lessons" feature to Sensei integration
* WooCommerce will now track leadsources and save them to a customer's contact record

= 2.0.4 =
* Bugfix for PHP notices appearing when shortcodes were in use and current user had no CRM tags
* Added SQL escaping for imported tag labels and categories
* Fix for contact address not updating existing contacts on guest checkout
* Fix for ACF not pulling / pushing field data properly

= 2.0.3 =
* Bugfix for importing users where CRM fields were mapped to multiple local fields
* Bugfix for Setup tab not appearing on initial install

= 2.0.2 =
* Bugfix for notices appearing for admins when admin bar was in use

= 2.0.1 =
* Bugfix for "update" action in HTTP Posts

= 2.0 =
* Complete rewrite and refactoring of core code
* Integration with ActiveCampaign, supporting all of the same features as Infusionsoft
* Custom fields are now available as a dynamic dropdown
* Ability to re-sync tags and custom fields within the plugin
* Integration with Sensei LMS
* Infusionsoft integration upgraded to use XMLRPC 4.0
* 100's of bug fixes, performance enhancements, and other improvements

= 1.6.4 =
* Improved compatibility with other plugins that use the iSDK class
* Changes to options framework to support 3rd party addons
* Added backwards compatibility for PHP versions less than 5.3

= 1.6.3 =
* Fix for registering contacts that already exist in Infusionsoft

= 1.6.2 =
* Fix for saving WooCommerce variation configuration
* Added automatic detection for when contacts are merged
* Improvements to wpf_template_redirect filter
* Added ability to apply tags per Ultimate Member registration form
* Ability to defer adding the contact until after the UM account has been activated
* Fixed bug with tags not appearing on admin user profile page
* Added filters for unsetting post types
* Added wpf_tags_applied and wpf_tags_removed actions

= 1.6.1 =
* Added has_tag function
* Added wpf_template_redirect filter
* Improved detection of registration form fields
* Fixed PHP notices appearing when using ACF
* Updates for compatibility with WP 4.3.1

= 1.6 =
* Can feed Gravity Forms data to Infusionsoft even if the user isn't logged in on your site
* Added support for Easy Digital Downloads
* Fixed bug with pulling date fields into Ultimate Member

= 1.5.2 =
* Fixed a bug with the "any" shortcode method
* More robust handling for user creation

= 1.5.1 =
* Fixed bug with account creation and Ultimate Member user roles

= 1.5 =
* LearnDash integration: can now apply tags on course/lesson/topic completion
* Content restrictions can now apply to child content
* New Ultimate Member fields are detected automatically
* Added ability to set user role via HTTP Post 'add'
* Added 'any' option to shortcodes

= 1.4.5 =
* Fixed global redirects not working properly
* Fixed issue with Preview As in admin bar
* Added 'wpf_create_user' filter
* Allowed for creating / updating users manually
* API improvements

= 1.4.4 =
* Misc. bugfixes with last release

= 1.4.3 =
* Improved compatibility of WooCommerce checkout with caching plugins
* Fixed bug with static page redirects
* Improved Ultimate Member integration
* Added support for combining "tag" and "not" in the WPF shortcode
* Added support for separating multiple shortcode tags with a comma
* Reduced API calls when profiles are updated
* Fixed bugs with guest checkout in WooCommerce

= 1.4.2 =
* Fixed bug with Ultimate Member integration in last release

= 1.4.1 =
* "Resync Contact" now pulls meta data as well
* Can now validate custom fields by name as well as label
* Added warning messages for WP Engine users
* Improved support for Ultimate Member membership plugin
* Fixed bug with redirects on Blog page / archive pages

= 1.4 =
* Added support for locking bbPress forums based on tags
* Added wpf_update_tags and wpf_update_meta shortcodes
* Support for overriding the new user welcome email with plugins
* Fixed bug with API Key generation
* Fixed bug with tags not applying after the specified delay
* Improved integration with WooCommerce checkout

= 1.3.5 =
* Added integration with Ultimate Member plugin

= 1.3.4 =
* Added "User Role" selection to import tool
* Added actions for user added and user updated
* Added "lock all" button to preview bar dropdown
* Fixed bug where tag preview wouldn't work on a static home page
* Fixed bug where shortcodes within the `[wpf]` shortcode wouldn't execute

= 1.3.3 =
* Improved integration support for user meta / profile plugins

= 1.3.2 =
* Tags will be removed when a payment is refunded
* Added support for applying tags with product variations
* Fixed bug with pushing ACF meta data on profile save
* Added support for pulling ACF meta data on profile load

= 1.3.1 =
* Added wpf_woocommerce_payment_complete action
* Added search filter to redirect page select dropdown
* Fixed "Class 'WPF_WooCommerce_Integration'" not found bug

= 1.3 =
* Added ability to import contacts from Infusionsoft as new WordPress users
* Added new plugin API methods for updating meta data and creating new users (see the documentation for more information)
* Added "unlock all" option to frontend admin toolbar
* Tags applied by a WooCommerce subscription can be removed when the subscription fails to charge, a trial period ends, or the subscription is put on hold
* Added support for syncing password and username fields
* Fixed a bug with applying tags at WooCommerce checkout when the user isn't logged in

= 1.2.1 =
* Added pull_user_meta() template tag
* Fixed bug with pushing user meta when no contact ID is found

= 1.2 =
* Added support for syncing multiselect fields with a contact record
* Added ability to trigger a campaign goal when a user profile is updated
* Added ability to manually resync a user profile if a contact record is deleted / recreated
* Now supports syncing with Infusionsoft built in fields. See the Infusionsoft "Table Documentation" for field name reference
* Users registered through a UserPro registration form will now have their password saved in Infusionsoft
* Fixed several bugs with user account creation using a UserPro registration form
* Fixed bug where tag categories with over 1,000 tags wouldn't import fully
* Fixed a bug that would cause checkout to fail with WooCommerce if a user is in guest checkout mode
* Numerous other bugfixes, optimizations, and improvements

= 1.1.5 =
* Fixed bug that would cause a user profile to fail to load when an IS contact wasn't found
* "Preview with tag" dropdown now groups tags by category and sorts alphabetically
* Fixed a bug with applying tags at WooCommerce checkout
* Notices for inactive / expired licenses

= 1.1.4 =
* Check for UserPro header on initial sync bug fixed
* Removed PHP notices on meta box when no tags are present
* "Preview with tag" has been removed from admin screens

= 1.1.3 =
* Automatic update bug fixed

= 1.1.2 =
* Fixed bug where users without email address would kill initial sync

= 1.1.1 =
* Changed name to WP Fusion

= 1.1 =
* EDD software licensing added

= 1.0.3 =
* Cleaned up apply_tags function

= 1.0.2 =
* Misc. bugfixes
* Added ability to apply tags to contact on WooCommerce purchase

= 1.0.1 =
* Misc. bugfixes
* Added content selection dropdown on post meta box

= 1.0 =
* Initial release


= 951 =