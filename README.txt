=== TukuToi Contact Form ===
Contributors: TukuToi
Donate link: https://www.tukutoi.com/
Tags: contact form, form, classicpress
Requires at least: 1.0.0
Tested up to: 4.9.99
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple Contact Form for ClassicPress Websites.

== Description ==

TukuToi Contact Form Plugin lets you add a simple Contact Form to any Page, Post or Custom Post of your ClassicPress Website.
Using a ShortCode with attributes you can configure several aspects of the Contact Form, such as:
- Receiver Email (defaults to the Website's Admin Email)
- Label for the Name Input
- Label for the Email Input
- Label for the Subject Input
- Label for the Message Input
- Label for the Send Button
- Error message for required Fields
- Error for invalid Emails
- Success message

== Installation ==

1. Install and Activate like any other ClassicPress Plugin
1. Insert and configure the ShortCode anywhere you want to see the form

You can use the ShortCode like so:
`[tkt_cntct_frm_contact_form email="receiver_email@domain.tld" label_name="Custom Label for Name Input" label_email="Custom Label for Email Input" label_subject="Custom Label for Subject Input" label_message="Custom Label for Message Input" label_submit="Custom Label for Submit Input" error_empty="Error Message when a Field is missing" error_noemail="Error Message when the Email field is missing or invalid" success="Success Message replacing the Form when the submission was succesful and email has been sent"]`


== Changelog ==

= 1.0.1 =
* [Changed] Updated Readme

= 1.0.0 =
* Initial Release