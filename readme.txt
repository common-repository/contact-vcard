=== Contact vCard ===
Contributors: Sheridan, санаторий имени Г.К. Орджоникидзе
Tags: qrcode, qr code, mobile, contact, contacts, vcard
Requires at least: 2.5
Tested up to: 3.3.2
Stable tag: trunk
License: GPLv2

The plugin is designed to create a contact list and placing them on the site. Supports vcard.vcf file and generate vCard QRCode 

== Description ==

The plugin is designed to create a contact list and placing them on the site. Supports vcard.vcf file and generate vCard QRCode 
The procedure works as follows: 
1. Determine and set the field list of contacts 
1. Set up a template output contact, as well as the required quality of QR-code 
1. Adding contacts to the list 
1. Add shorcode to article or post 

Shortcodes: 
*   [All_contacts] - Displays all contacts 
*   [Single_contact id=ID] - Displays a given contact by ID 

Template fields: 
As a template, use the following fields: 
% Field% - is replaced by the value of field named 'field' 
%! Field% - is replaced by the name of the field named 'field' 
% Qrcode% - replaced by an image - QR-Code 
% Vcard-file% - replaced by a reference to vCard.vcf 
The templates can also specify other shortcodes


QR Code generation based on <a href='http://phpqrcode.sourceforge.net/'>PHP QR Code</a> without any modifications

== Installation ==

1. Upload the full directory into your /wp-content/plugins/ directory, or install through the admin interface
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Determine and set the field list of contacts 
1. Set up a template output contact, as well as the required quality of QR-code 
1. Adding contacts to the list 
1. Add shorcode to article or post 

== Frequently Asked Questions ==

= Can I generate QR Card based vCard with this plugin? =

Yes, the plugin was designed for this.

= What is a QR Code? =

Read <a href="http://en.wikipedia.org/wiki/QR_Code">Wikipedia QR Code article</a>.

= What is a vCard? =

Read <a href="http://en.wikipedia.org/wiki/VCard">Wikipedia vCard article</a>.

== Screenshots ==
1. Base options (Template, QR-Code)
2. Contacts list
3. Contact add\edit
4. Batch contacts edit
5. Fields list
6. Field add\edit
7. Result of render one contact from screenshot 2 with options from screenshot 1

== Changelog ==

= 0.3 =
 * First public version
 