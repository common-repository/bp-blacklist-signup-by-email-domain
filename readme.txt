=== BP Blacklist Signup by Email Domain  ===
Contributors: r-a-y
Donate link: paypal.me/GeorgeChaplin
Tags: buddypress, registration, email blacklist
Requires at least: WordPress 3.4.x, BuddyPress 1.6
Tested up to: 6.6
License: GPLv2 or later
Stable tag: 1.1.0
 
Only allow users with email addresses not on the domain blacklist to register in BuddyPress.

== Description ==

User registration spam is prevalent in BuddyPress.

One way to dramatically decrease signup spam is to restrict the email address domains that users can sign up with.

WordPress multisite has a native option called "Limited Email Registrations".  But this option requires you to enter the full email domain.  For example, if you only wanted to allow .edu email addresses to register, this is not possible.

This plugin restricts registrations to the email domains that you are not specified in the blacklist and works in WordPress single-site and multi-site.

Plugin is a fork of r-a-y's BP Restrict Signup by Email Domain.

Tested on BuddyPress 2.5, but should work all the way down to BuddyPress 1.6.

== Installation ==

* Download, install and activate this plugin.
* Login to the WP admin dashboard and navigate to the "Settings > BuddyPress" page.  Next, click on the "Options" tab.  (If you're using BuddyPress 2.4 or lower, click on the "Settings" tab).
* You should see a new section called "Email Address Restrictions".
* Under "Blacklist Email Domains", list the email domains that are not allowed.

eg. If you type in:

    .edu
    .org
    mycustomdomain.com

This will prevent users with .edu, .org, or mycustomdomain.com email addresses from registering on the site.

Any other email address will be allowed registration.

* To customize the error message and registration blurb, edit the other two fields.

== Screenshots ==

screenshot-1.jpg

== Changelog ==

= 1.1.0 =

* 20/07/2024

* Upgrade: Improved translations and escaping of inputs.

= 1.0.3 =

* 11/02/2021

* Fix: Corrected require statement for the admin file.

= 1.0.2 =

* 10/02/2021

* Fix: Translation improvements

= 1.0.1 =

* 11/12/2018

= Fix: Fix for substr_compare start location error

= 1.0.0 =
* Initial public release
