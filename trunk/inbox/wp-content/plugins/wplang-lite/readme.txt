=== WPLANG Lite ===
Contributors: SergeyBiryukov
Tags: l10n, translations, php, memory, optimization
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: 0.3

Creates a separate tiny .mo file to use on a site front-end.

== Description ==

Creates a separate tiny .mo file to use on a site front-end. Allows to save some amount of RAM on a shared hosting server.

Thanks to [MAX](http://maxsite.org/) for the original non-plugin solution and to [AlexPTS](http://my-wordpress.ru/) for the idea.

== Installation ==

1. Upload `wplang-lite` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

On some servers you may also need to change permissions for `wp-content/languages` to 775 or 777.

== Changelog ==

= 0.3 =
* The file is created in a separate call to save up memory

= 0.2 =
* Added automatic creation of .mo file

= 0.1 =
* Initial release
