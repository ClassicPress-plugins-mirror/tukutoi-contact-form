# TukuToi Contact Form

Simple Contact Form for ClassicPress Websites.

## Description

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

## Installation

1. Install and Activate like any other ClassicPress Plugin
1. Insert and configure the ShortCode `[tkt_cntct_frm_contact_form]` anywhere you want to see the form

## ShortCode Attributes

* `id`. ID of the Form. Defaults to 1 if not passed. Must be set when using Filters or actions referring to this ID, Accepts only text or numeric value.
* `label_name`. Label of Name Input. Defaults to "Your Name". Accepts only text.
* `label_email`. Label of the Email Input. Defaults to "Your E-mail Address". Accepts only text.
* `label_subject`. Label of the Subject Input. Defaults to "Subject". Accepts only text.
* `label_message`. Label of the Message Input. Defaults to "Your Message". Accepts only text.
* `label_submit`. Label for the Submit Button. Defaults to "Submit". Accepts only text.
* `error_empty`. Error message for empty field(s). Defaults to "Please fill in all the required fields.". Accepts only text.
* `error_noemail`. Error message for empty or invalid Email Input. Defaults to "Please enter a valid e-mail address.". Accepts only text.
* `success`. Success message shown instead of the Form when the email was sent successfully. Defaults to "Thanks for your e-mail! We'll get back to you as soon as we can.". Accepts only text.

## Actions and Filters

The Plugin offers several actions and filters.

* `tkt_cntct_frm_email`. Allows to modify the receveir email of a Contact form. Defaults to `get_bloginfo( 'admin_email' )`. Second argument passed is the Form ID.
```
add_filter( 'tkt_cntct_frm_email', 'special_receiver', 10, 2 );
function new_email( $email, $id ){
	if( (int)$id === 1 ){// If your Form ID is 1
		return 'an@email.com';
	} else {
		return 'another@email.com';
	}
}
```

* `tkt_cntct_frm_subject`. Allows to modify the Subject of email sent. Second argument are ALL the form fields (array). Form ID is part of the form fields. Third argument is the receiver Email.
* `tkt_cntct_frm_message`. Allows to modify (or append to) the Message of email sent. Second argument are ALL the form Fields (array). Form ID is part of the form fields. Third argument is the receiver Email.
```
add_filter('tkt_cntct_frm_message', 'append_to_message', 10, 3);
function append_to_message( $message, $form_fields, $receiver ){
	if( (int)$form_fields['id'] === 2 ){// If your form is ID 2
		return $message . 'appended string';
	} elseif( $receiver === 'my@receiver.com' ){
	  return 'overwrite the entire message';
	} else{
		return $message;
	}

}
```
(Same example can be applied to the Subject Filter)

* `tkt_cntct_frm_redirect_uri`. Allows to filter the Redirect URL. Defaults to current page with `?success=true` appended on success. Second argument passed is Form ID.

* `tkt_cntct_frm_pre_send_mail`. Action fired right before the mail is sent. Second argument all form fields. Helpful to do things before the mail is sent...

* `tkt_cntct_frm_post_send_mail`. Action fired right after the email is sent. Arguments include $receiver, $email_subject, $email_message, $headers, $form_fields. Helpful to for example send another email to another place, after the mail was sent. Or whatever, abort the script, if you like.

* `tkt_cntct_frm_pre_redirect`. Action fired right before the wp_redirect is fired. Arguments are $redirect_url, $form_id

* `tkt_cntct_frm_post_redirect`. Action fired right after the wp_redirect is fired. No arguments.

## Styling the form

The Form is built with native HTML and minimal markup, so you can apply whatever styles you want in general.
The few classes and IDs passed have all a `tkt-` prefix.
Available classes and IDs:
* `tkt-contact-form`. Class for the `form` HTML attribute.
* Form ID is the ID you pass to the ShortCode `id` attribute.
* Each input has an error class set when failing validaton: `tkt-missing-or-invalid`
* Heneypot fields are usually not to be styled further, but who knows you might need to access them, they use class `tkt-ohnohoney`


## Changelog

### 2.1.0
[Added] Separated IP from message body so body can be filtered alone
[Added] Readme.md file
[Changed] Message body now uses wp_kses_post when filter is applied
[Changed] Updated Readme

### 2.0.1
[Fixed] Error in the readme description for filter tkt_cntct_frm_message.

### 2.0.0
* [Removed] ShortCode attribute to change receiver email
* [Added] ShortCode attribute to define Form ID
* [Added] Several Filters and actions to control Form and Mail events/contents
* [Fixed] When form is successfully submitted it now properly redirects to a location instead of replacing the form
* [Fixed] All CSS Classes and IDs now use specific tkt- prefix.


### 1.0.1
* [Changed] Updated Readme

### 1.0.0
* Initial Release