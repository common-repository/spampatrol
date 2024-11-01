=== SpamPatrol ===
Contributors: spampatrol
Donate link: 
Tags: spam, detection, forms, filtering, analysis, nlp
Requires at least: 5.0
Tested up to: 6.2
Stable tag: 1.35.16
Requires PHP: 7.0+
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

SpamPatrol provides intent-based spam detection for form submissions and other message based environments that need text analysis.

== Description ==

Stop wasting time sifting through garbage and start focusing on submissions that need attention.

The SpamPatrol plugin will automatically detect other supported form plugins that you are using and integrate with them to process spam submissions based on your desired settings.

It doesn't matter whether it's a bot or human abusing your contact form, SpamPatrol will analyze the submissions and determine whether it's spam.

Avoid playing whack-a-mole with unruly spammers that use a wide range of IPs and domains. SpamPatrol works based on understanding the message submitted to catch all the things that your typical website visitor or app user won't do.

More details can be found at https://spampatrol.io/docs/wordpress


== Installation ==

1. Add the plugin from within Wordpress or by uploading the zip archive
2. Activate the plugin
3. Supported form plugins will need any fields to be analyzed indicated using special attributes. For details can be found at https://spampatrol.io/docs/wordpress
4. Under the plugin settings you can indicate your desired spam score threshold (default 50) as well as what you would like to do with the spam submissions.

Developers are also able to use the included **`spampatrol_analyze`** helper function for any custom integration that needs it.

```php
<?php

spampatrol_analyze( array(
    'content' => '', // Text content to be analyzed (required)
    'visitorIp' => '', // IP address of the end-user/visitor (optional)
    'expectedCountries' => array(
        'US',
        'CA','GB',
    ), // optional list of ISO 639-1 counties to indicate where submissions are expected to originate from
    'expectedLanguages' => array(
        'en',
        'es',
    ), // optional list of ISO 3166-1 alpha-2 languages to indicate the language expected for **content**
) );
```

== Frequently Asked Questions ==

= How are false positives handled? =

We spend a lot of time ensuring that the detection accuracy is as high as
possible but there's always a small chance of a false positive depending on
where you set your threshold. In general we recommend against discarding
messages entirely. Instead messages should be sent to a spam only address or
placed in your Spam/Junk folder and reviewed periodically.

= Does SpamPatrol replace CAPTCHAs and honey pots? =

Yes. If you are looking for a cleaner form experience to save end-users time
and frustration SpamPatrol can replace other mechanisms. It's also fine to use
it in addition to existing mechanisms you have in place.

= Which form plugins does it integrate with? =

The SpamPatrol plugin currently supports Contact Form 7, Ninja Forms and
Formidable Forms.


== Screenshots ==

1. SpamPatrol configuration settings for your spam threshold and ways to handle spam submissions.

== Changelog ==


== Upgrade Notice ==

