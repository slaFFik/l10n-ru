=== YATCP ===
Contributors: jpraetorius
Donate link: 
Tags: comments, comment, thread, threaded, reply, answer
Requires at least: 2.0.10
Tested up to: 2.5
Stable tag: 0.6.5

YATCP allows you to have threaded comments (i.e. reply to existing comments) in your wordpress.

== Description ==

Using standard Wordpress your comments are linear, despite the fact that Wordpress provides the technical ability to thread comments. That way longer discussion Threads tend to get confusing, as the reader has to keep track who is responding to whom. YATCP (Yet another threaded comments plugin) tries to solve that problem by leveraging the possibilities provided by Wordpress.

There are already solutions to this Problem, most known is probably Brians Threaded Comments. Anyway I didn't like the Idea of requiring Javascript for anyone to be able to comment on a comment. Moreover I felt this made customizing that (really cool) plugin difficult.

So YATCP came to live in a small private Project where I needed nested comments. Flexibility was a goal for this plugin so it should be easy to use YATCP in a way that fits your needs.

If you come across anything that is not solvable or obvious to see, don't hesitate to write me a Mail at yatcp@organisiert.net and we'll see what can be done. Finally YATCP is released under GPL, so feel free to get the Code and cook your own Plugin from that. 

== Installation ==

1. Download the latest Version of YATCP
1. Copy the .zip File to your Wordpress Plugins Folder
1. Unzip the File (resulting in a new Directory yatcp being created)
1. Activate YATCP in the Admin Panel
1. Set the YATCP Options in the Options Panel (under "Options" - "YATCP Options") to your needs
1. In the Admin Panel under "Presentation" - "Theme Editor" - "Single Post" change ` <?php comments_template(); ?>` to ` <?php yatcp_comments_template(); ?>`

== Frequently Asked Questions ==

= I've found a Bug or have a Problem/Feature Request with YATCP. What should I do? =

Mail me at yatcp@organisiert.net. Include at least a Bug report (Patches are highly welcome), and we see what we can do.

= How is YATCP licensed? =

YATCP is made available under GPL v2.

= When I use YATCP in my non-english Wordpress, I get english Text in the Comments and the Comments Form. =

Since Version 0.6 YATCP uses the Standard Wordpress i18n mechanism to provide translated texts. If you have configured your Wordpress correctly and there already is a translation for your language YATCP should pick that up automatically. If your're getting english texts, most probably no translation exists for your language. Have a look at the [Translation Page](http://organisiert.net/yatcp/translations.html) for further information. 

= I dont like how YATCP is displaying my comments. Can I change that? =
						    
YATCP was made to be adaptible to the template is has to live in, so you can change it nearly in any way. Please refer to the [Customization Description](http://organisiert.net/yatcp/customization.html) for more detail on that Issue.

= On which Wordpress Version does YATCP run? =
										    
I have tested YATCP on Wordpress 2.0.7, 2.1, 2.2.1, 2.3.x and 2.5 successfully. I'm not quite aware since when the comment_parent column is present in Wordpress, but your Version has at least to have that Database Column in order for YATCP to work correctly. 

== Screenshots ==

1. YATCP Comments selection: Select the comment you want to answer on (also provided by the Reply Link behind each Comment in the Default Styling).
2. YATCP Default Comments styling: By default threaded comments are indented a little to the right and show a thin border.
