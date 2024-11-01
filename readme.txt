=== Lemme Know ===

Contributors: clash82
Tags: notifications, email, newsletter, subscribe2, mailing, smtp
Requires at least: 4.6
Tested up to: 6.4
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2
Donate link: https://www.paypal.me/clash82

Sends e-mail notification for all subscribers when a new post is published.

== Description ==

This plugin is currently in alpha stage. It includes only basic features like sending e-mail notifications using built-in PHP mail() function or by using external SMTP server (recommended). Work of this plugin depends mostly on the SMTP server configuration.

Lemme Know plugin allows you to send e-mail notifications only for a small amount of subscribers. There are plans to implement Cron-based solution which will allows to send notifications in chunks and omit server limitations.

== Installation ==

* upload the `wp-lemme-know` directory to the `/wp-content/plugins/` directory
* activate the plugin through the 'Plugins' menu in WordPress
* go to `Settings > Lemme Know` and fill out required settings
* go to `Themes > Widgets` and add Lemme Know widget to the sidebar

Todo:

* implement Cron-based feature allowing to send e-mails in chunks
* add e-mail list management (add/edit/remove subscribers manually)
* add e-mail import/export option
* add e-mail groups
* add double opt-in feature for e-mail subscriptions
* add translations

Feel invited to contribute if you can help make this plugin better :-)

Visit https://github.com/clash82/wp-lemme-know, fork the project, add your feature and create a Pull Request. I'll be happy to review and add your changes.

== Screenshots ==

1. Widget (user view)
2. Widget (administration area)
3. Dashboard panel
4. Plugin settings (General tab)
5. Plugin settings (Notifications tab)
6. Plugin settings (Mail tab)

== Changelog ==

= v0.10.0 =
* added: latest 50 subscribers is now displayed in the dashboard panel.

= v0.9.0 =
* added: tabs in the settings page.

= v0.8.0 =
* fixed: PHPMailer paths deprecations.

= v0.7.0 =
* added: new line is now converted to `<br>` for `{{post_excerpt}}` and `{{post_body}}` tags.

= v0.6.0 =
* fixed: issue related to incorrect admin-ajax.php file path when WP is installed in subdirectory (wp-admin),
* added: validation if `e-mail from` value is specified when sending test message.

= v0.5.0 =
* fixed: issue related to incorrect admin-ajax.php file path when WP is installed in subdirectory.

= v0.4.0 =
* fixed: send notifications only when `post` type content is published,
* added: new test SMTP configuration option which allows you to test the current configuration by sending an example e-mail.

= v0.3.0 =
* replaced SwiftMailer with built-in PHPMailer (decreased plugin size!).

= v0.2.0 =
* fixed: removed notification sending after post edit (notifications are now sent only after first post publish),
* added: internal Administrator notifications now includes e-mail unsubscriptions,
* added: site name is now part of the e-mail's title used when sending internal notifications (useful when using plugin for more than one site).

= v0.1.0 =
* first alpha release.

== Upgrade notice ==

* nothing :-)
