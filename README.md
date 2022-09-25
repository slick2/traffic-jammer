# Wordpress Traffic Jammer
Contributors: slick2

Tags: security

Requires at least: 4.7

Tested up to: 6.0.2

Stable tag: 0.6

Requires PHP: 7.4

License: GPLv2 or later

License URI: (https://www.gnu.org/licenses/gpl-2.0.html)

## Description

The poormans WAF.  WordPress plugin to block IP and bots categorized as harmful, resulting in heavy server loads from frequently crawled pages, or utilized in vulnerability/security breach scans.  The plugiin can block:

- Single IP
- Range of IP using CIDR format
- User agents 
- Known bad bots

## Command Line
- Block an IP using wp-cli
```
wp jam block <ip>
```
Unblock an IP using wp-cli
```
wp jam unblock <ip>
```


## Installation

1. Download the plugin via the github release page
1. Upload the compress archive through the 'Plugins > Add New > Upload' screen in your WordPress dashbboard
1. Activate the plugin through the 'Plugins' menu in WordPress 