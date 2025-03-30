# Traffic Jammer - WordPress Plugin #  
**Contributors:** [slick2](https://profiles.wordpress.org/slick2/)  
**Tags:** security  
**Requires at least:** 5.6  
**Tested up to:** 6.7.2  
**Stable tag:** 1.4.8 
**Requires PHP:** 7.4   
**License:** GPLv2 or later  
**License URI:** (https://www.gnu.org/licenses/gpl-2.0.html)  

## Description ##


Prevent unwanted traffic incidents that might result in site outages and billing overages.  WordPress plugin that blocks IP and bots categorized as harmful, resulting in heavy server loads from frequently crawled pages, or utilized in vulnerability/security breach scans.  The plugiin can block:

- Single IP
- Range of IP using CIDR format
- User agents 
- Known bad bots

When adding an IP, use the formats listed below.
|      |                 |                                            |
| ---  | ---             | ------------------------------------------ |
| IPv4 |	IPv4 Address |	192.168.1.1                               |
| IPv4 |	CIDR range	 |  192.168.1.0/24                            |  
| IPv6 |    IPv6 Address |	2001:4450:49b6:9900:6498:6f80:4b15:240a   |

## Command Line ##
- Block an IP using wp-cli
```
wp jam block <ip>
```
Unblock an IP using wp-cli
```
wp jam unblock <ip>
```

## Installation ##

1. Download the plugin via the github release page
1. Upload the compress archive through the 'Plugins > Add New > Upload' screen in your WordPress dashbboard
1. Activate the plugin through the 'Plugins' menu in WordPress 
