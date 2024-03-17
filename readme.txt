=== Traffic Jammer ===
Contributors: slick2
Donate link: https://www.paypal.com/donate/?hosted_button_id=8M46X2F79WATW
Tags: block, ip, pantheon, block ip, security, malicious ip, block bots, pantheon block ip
Requires at least: 5.2
Tested up to: 6.4.3
Stable tag: 1.2.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin that automatically blocks top IP visits which are reportedly malicious.  Protects the login area by limiting access and blocking failed login attempts.  It prevents spamming bots that cause traffic overages.

== Description ==

Prevent unwanted traffic incidents that might result in site outages and billing overages.  WordPress plugin that blocks IP and bots categorized as harmful, resulting in heavy server loads from frequently crawled pages, or utilized in vulnerability/security breach scans.  

<h3>Firewall</h3>

- Manually add an IP to be blocked
- Manually add Bots/User-Agents/Scrappers to prevent site visit
- Manually add an IP to be whitelisted on the login page
- Automatically block malicious traffic thru analysis on an hourly basis
- Automatically block excessive login attempts with configurable threshold
- Automatically block excessive visits from an incremented query that would bust the CDN cache

<h3>WP-CLI commands</h3>
	Example
	wp jam block 127.0.0.10
	wp jam unblock 127.0.0.10
	wp jam topip

<h3><a href="https://pantheon.io">Pantheon.io</a></h3>
Prevent traffic overages due to excessive visits from malicious traffic. The plugin can be used on sites hosted on <a href="https://pantheon.io">Pantheon.io</a> and no additional symlinks required.

<h4>Pantheon terminus command</h4>
	terminus wp sitename.env -- jam block 127.0.0.1
	terminus wp sitename.env -- jam unblock 127.0.0.1

== Installation ===

1. Download the plugin via WordPress.org page
1. Upload the compress archive through the 'Plugins > Add New > Upload' screen in your WordPress dashbboard
1. Activate the plugin through the 'Plugins' menu in WordPress 

== Screenshots ==

1. Admin UI
2. Block Bots 
3. Whitelist IP
4. AbuseIPDB Integration
5. Settings
6. Reports - Top IP
7. Reports - Top User Agents

== Changelog ==
= 1.2.5 =
* Tested to work with version 6.4.3

= 1.2.4 =
* Tested to work with version 6.4.1

= 1.2.3 =
* Tested to work WordPress version 6.3.2

= 1.2.2 =
* Tested to work WordPress version 6.3.1

= 1.2.1 =
* fixed PHP warnings when running cli commands
* added for the commands topip 

= 1.1.2 =
* fixed deprecated warnings 

= 1.1.1 =
* prevent selection or entering 0 to the login threshold that would result in lockout

= 1.1.0 =
* added 5 days in the selection of retention period of logs

= 1.0.10 =
* fix bug on detecting real IP when domain is using Cloudflare proxy

= 1.0.9 =
* fix bug on updating abuseipdb key and options
* added checking of error when calling abuseipdb endpoint

= 1.0.8 =
* Settings for AbuseIPDB has a separate tab
* Added threshold field for minimal abuse score

= 1.0.7 =
* added AbuseIPDB feature to block malicious traffic 

= 1.0.6 =
* added feature to automatically block IPs which have failed login
* option to limit the number of retries of failed login before it would be blocked

= 1.0.5 =
* added blocking for cache busting URL query strings

= 1.0.4 =
* added schedule event for log trimming
* added setting for log retention

= 1.0.3 =
* added database version log for future updates

= 1.0.2 =
* added reports page

= 1.0.1 =
* tabbed content
* show current IP
* simple instructions

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
