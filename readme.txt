=== Traffic Jammer ===
Contributors: slick2
Donate link: https://www.paypal.com/donate/?hosted_button_id=8M46X2F79WATW
Tags: security
Requires at least: 4.7
Tested up to: 6.0.2
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin to block IP and bots that causes malicious traffic

== Description ==

The poormans WAF.  WordPress plugin to block IP and bots categorized as harmful, resulting in heavy server loads from frequently crawled pages, or utilized in vulnerability/security breach scans.  The plugiin can block:

- Single IP
- Range of IP using CIDR format
- User agents 
- Known bad bots

<h4>WP-CLI commands</h4>

- wp jam block 127.0.0.10
- wp jam unblock 127.0.0.10


== Installation ===

1. Download the plugin via WordPress.org page
1. Upload the compress archive through the 'Plugins > Add New > Upload' screen in your WordPress dashbboard
1. Activate the plugin through the 'Plugins' menu in WordPress 


== Changelog ==
= 1.0.0 =
* 2022-10-07
* Stable release approved by WordPress.org

= 0.9 =
* 2022-09-29 
* replace icon on the menu using dashicons

= 0.8 =
* 2022-09-28
* renamed the plugin name to comply with WordPress.org 

= 0.7 =
* 2022-09-27
* all options used are not set to autoload
* added a feature to whitelist an IP in wp-login.php


= 0.6 =
* 2022-09-26
* activation hook, load known bad bots
* bug fix blocking of user agents
* refactor code on checking of IP

= 0.5 =
* 2022-09-19
* add jam wp-cli command
* add block IP using wp-cli
* unblock IP using wp-cli


= 0.4 =
* 2022-09-06
* fix PHP warning when DEBUG is set to true
* added ip range blocking using cidr

= 0.3 =
* 2022-09-05 
* added feature to block user agent
* adhere to WordPress coding standards

= 0.2 =
* 2022-08-13 
* Initial release 
