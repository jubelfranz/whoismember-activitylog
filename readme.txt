=== WhoIsMember - Activity Log Add-on ===
Contributors: jubelfranz
Tags: user management, activity log, tracking, member tracking, admin tools
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://einfachalles.at

Official Activity Log Add-on for WhoIsMember. Tracks detailed frontend activity of users, guests, and bots.

== Description ==

WhoIsMember - Activity Log is a powerful extension for the WhoIsMember plugin. It allows administrators to monitor website traffic in real-time, providing a detailed log of who (user, guest, or bot) accessed which content and when.

IMPORTANT: This plugin is an add-on and requires an active installation of WhoIsMember Pro.

= Key Features =
* **Detailed Logging:** Capture timestamps, usernames/roles, IP addresses, and User Agents.
* **Frontend Display:** Use the [activity-log] shortcode to view the log directly on any page (visible to admins only).
* **Advanced Pagination:** Smooth navigation through large datasets with an optimized page selector.
* **Automatic Pruning:** Automatically delete old records based on a customizable retention period (default: 30 days).
* **Bot Detection:** Identifies known bots and crawlers using the WhoIsMember core logic.
* **GDPR Compliant:** Respects user tracking consent settings and local privacy laws.

== Installation ==

1. Ensure the main plugin "WhoIsMember" is installed and pro-licensed.
2. Upload the `whoismember-activitylog` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Navigate to WhoIsMember -> Settings to define the log retention period.
5. Place the shortcode `[activity-log]` on a page restricted to administrators.

== Frequently Asked Questions ==

= Who can see the Activity Log? =
By default, the output of the [activity-log] shortcode is only visible to users with administrator privileges (manage_options).

= Do I need a separate license? =
Yes, this add-on requires its own license to enable the advanced tracking features.

= How is database size managed? =
The plugin includes a self-cleaning mechanism. You can set the "Log Retention" period in the settings to automatically remove old entries.

== Screenshots ==

1. The frontend Activity Log table with pagination.
2. Log retention settings within the main WhoIsMember settings panel.

== Changelog ==

= 1.2.5 =
* Enhanced pagination with "Sliding Window" logic.
* Optimized license verification via SDK.
* Added status labels in the admin settings.

= 1.1.0 =
* Implemented admin-only check for the shortcode.
* Fixed table initialization bugs.

= 1.0.0 =
* Initial release of the Activity Log Add-on.

== Upgrade Notice ==

= 1.2.5 =
This update improves database pruning stability and is highly recommended for all users.
