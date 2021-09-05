# TukuToi Contact Form ![ClassicPress Plugin: Required CP Version](https://img.shields.io/badge/dynamic/json?color=%23057f99&label=classicpress&prefix=v&query=%24.data.minimum_cp_version&url=https%3A%2F%2Fdirectory.classicpress.net%2Fapi%2Fplugins%2Ftkt-contact-form) ![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/tkt-contact-form)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-contact-form&metric=bugs)](https://sonarcloud.io/dashboard?id=TukuToi_tukutoi-contact-form) [![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-contact-form&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=TukuToi_tukutoi-contact-form) [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-contact-form&metric=alert_status)](https://sonarcloud.io/dashboard?id=TukuToi_tukutoi-contact-form) [![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-contact-form&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=TukuToi_tukutoi-contact-form) [![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-contact-form&metric=security_rating)](https://sonarcloud.io/dashboard?id=TukuToi_tukutoi-contact-form) [![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=TukuToi_tukutoi-contact-form&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=TukuToi_tukutoi-contact-form)

Simple Contact Form for WordPress Websites.

## Description

TukuToi Contact Form Plugin lets you add a simple Contact Form to any Page, Post or Custom Post of your WordPress Website.
Using the ShortCode `[tkt_cntct_frm_contact_form]` with attributes you can configure several aspects of the Contact Form, such as:
- Form ID
- Label for the Name Input
- Label for the Email Input
- Label for the Subject Input
- Label for the Message Input
- Label for the Send Button
- Error message for required Fields
- Error for invalid Emails
- Success message

Note: Only one Contact Form can be inserted each page or post.

## Installation

1. Install and Activate like any other WordPress Plugin
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
function special_receiver( $email, $id ){
	if( (int)$id === 1 ){// If your Form ID is 1
		return 'an@email.com';
	} else {
		return 'another@email.com';
	}
}
```

* `tkt_cntct_frm_subject`. Allows to modify the Subject of email sent. Second argument are ALL the form fields (array). Form ID is part of the form fields. Third argument is the receiver Email.
```
add_filter( 'tkt_cntct_frm_subject', 'special_subject', 10, 3 );
function special_subject( $subject, $form_fields, $receiver ){
	if( $form_fields['id'] === 'my-contact-form' ){// If your Form ID is my-contact-form
		return 'Custom Subject';
	} else {
		return $subject;
	}
}
```
* `tkt_cntct_frm_message`. Allows to modify (or append to) the Message of email sent. Second argument are ALL the form Fields (array). Form ID is part of the form fields. Third argument is the receiver Email.
```
add_filter('tkt_cntct_frm_message', 'append_to_message', 10, 3);
function append_to_message( $message, $form_fields, $receiver ){
	if( $form_fields['id'] === 'my-contact-form' ){// If your form is ID my-contact-form
		return $message . '<p>appended string</p>';
	} elseif( $receiver === 'my@receiver.com' ){
	  return 'overwrite the entire message';
	} else{
		return $message;
	}

}
```

* `tkt_cntct_frm_redirect_uri`. Allows to filter the Redirect URL. Defaults to current page with `?success=true` appended on success. Second argument passed is Form ID.
```
add_filter('tkt_cntct_frm_redirect_uri', 'redirect_url', 10, 2);
function redirect_url( $redirect, $id ){
	if( $id === 'my-contact-form' ){// If your form is ID my-contact-form
		return 'https://custom.url/thing';
	} else{
		return $redirect;
	}

}
```

* `tkt_cntct_frm_ip`. Allows to filter the IP Address String appended to the email body (useful to remove it, for example). Second argument is the Form ID.
```
add_filter('tkt_cntct_frm_ip', 'filter_ip', 10, 2);
function filter_ip( $ip, $id ){
	if( $id === 'my-contact-form' ){// If your form is ID my-contact-form
		return '';// remove IP alltogether.
	}
}
```

* `tkt_cntct_frm_pre_send_mail`. Action fired right before the mail is sent. Second argument all form fields. Helpful to do things before the mail is sent...
```
add_action( 'tkt_cntct_frm_pre_send_mail', 'pre_send_mail', 10, 1 );
function pre_send_mail($form_fields){
	if($form_fields['id'] === 'my-contact-form'){
		wp_mail( 'custom@email.com', 'new mail', 'someone is about to send an email with your contact form' );
	}
}
```

* `tkt_cntct_frm_post_send_mail`. Action fired right after the email is sent. Arguments are $receiver, $email_subject, $email_message, $headers, $form_fields. Helpful to for example send another email to another place, after the mail was sent. Or whatever, abort the script, if you like.
```
add_action( 'tkt_cntct_frm_post_send_mail', 'post_send_mail', 10, 5 );
function post_send_mail($receiver, $email_subject, $email_message, $headers, $form_fields){
	if($form_fields['id'] === 'my-contact-form'){
		wp_mail( 'custom@email.com', 'new mail', 'someone has sent an email with your contact form' );
	}
}
```

* `tkt_cntct_frm_pre_redirect`. Action fired right before the wp_redirect is fired. Arguments are $redirect_url, $form_id
```
add_action( 'tkt_cntct_frm_pre_redirect', 'pre_redirect', 10, 2 );
function pre_redirect($redirect_url, $form_id){
	if($form_id === 'my-contact-form'){
		wp_redirect( 'https://wherever.com' );
		exit;
	}
}
```


* `tkt_cntct_frm_post_redirect`. Action fired right after the wp_redirect is fired. No arguments.
```
add_action( 'tkt_cntct_frm_post_redirect', 'post_redirect', 10 );
function post_redirect(){
	wp_mail( 'custom@email.com', 'new mail', 'someone has sent an email with your contact form and everything went well, they where redirected to your target url' );
}
```

## Styling the form

The Form is built with native HTML and minimal markup, so you can apply whatever styles you want in general.
The few classes and IDs passed have all a `tkt-` prefix.
Available classes and IDs:
* `tkt-contact-form`. Class for the `form` HTML attribute.
* Form ID is the ID you pass to the ShortCode `id` attribute.
* Each input has an error class set when failing validaton: `tkt-missing-or-invalid`
* Heneypot fields are usually not to be styled further, but who knows you might need to access them, they use class `tkt-ohnohoney`

Note that Plugin CSS (and JS) are enqueued only when the ShortCode is added to a page/post/ For this reason scripts are added to the footer.

## Changelog

### 2.1.3
[Fixed] Error in the readme

### 2.1.2
[Fixed] Make sure HTML is not lost from IP Paragraph

### 2.1.1
[Added] Readme Examples for Filters
[Added] Comments to code 
[Fixed] IP Address filter was not filtering the actual string
[Fixed] Redirect in code was not using wp_safe_redirect
[Fixed] Few form contents where not escaped/sanitized

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