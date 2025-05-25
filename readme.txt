=== Socialize ===
Contributors: JonBishop
Donate link: http://www.jonbishop.com/donate/
Tags: social sharing, share buttons, call to action, sharing, social svg
Requires at least: 5.6
Tested up to: 6.8.1
Stable tag: 3.0.1
License: GPLv2
Requires PHP: 7.4

Easily add social sharing buttons to posts: float beside content or place in a call-to-action box below.

== Description ==

Socialize makes it easy to add relevant social sharing buttons to your posts and pages without clutter or complexity.

You can add sharing buttons in two locations:
1. Above your post content, left, right, or center aligned (or floating on the left or right)
2. In a call-to-action (CTA) box below your content

Unlike many other plugins, Socialize provides a panel within the post editor for selecting which buttons appear per post. This helps you display only the most relevant sharing options to encourage engagement.

You can also display a customizable CTA box below the content. Use this space to ask readers to comment, share or take another action. You control the message and which buttons appear here, on a per-post basis.

All visual elements, including button layout, floating behavior, and CTA styles, can be modified in the plugin settings. Developers will also find useful actions and filters for extending functionality.

= Features =
* Includes 6 core buttons: Twitter, Facebook, LinkedIn, Reddit, Pinterest, Pocket
* Includes lightweight SVG-style alternatives for each service
* Display sharing buttons above or below content
* Supports floating button bar (left or right)
* Buttons can be aligned left, right, or center above the content (non-floating)
* Call-to-action section with editable text and layout
* Custom post type support
* Developer-friendly with actions and filters
* Minimal footprint and modernized layout

= Useful Links =
For more information and additional resources, you can visit the following pages:
* [Main Plugin Page](https://jonbishop.com/downloads/wordpress-plugins/socialize/)
* [API Page](https://jonbishop.com/downloads/wordpress-plugins/socialize/socialize-api/)

**Note:** For proper social metadata (e.g., titles, images), I recommend using a dedicated OpenGraph plugin.

== Installation ==

1. Upload the 'socialize' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Socialize to configure your global defaults
4. Optionally customize buttons or CTA on individual posts or pages

== Frequently Asked Questions ==

= Can I change the background or border of the button containers? =  
Yes. These visual styles can be adjusted from the plugin settings.

= How do I hide sharing buttons on a specific post? =  
Uncheck all buttons in the 'Socialize' panel for that post.

= Can I disable the CTA box for a single post? =  
Yes. Use the "Hide Call To Action Box below this post" checkbox in the post’s Socialize panel.

= How do I use my own Twitter username? =  
Set your preferred handle under "Twitter Source" in the settings.

= What's the difference between floating and static top buttons? =  
Floating buttons stay visible alongside the content. Static top buttons appear above the content and can be left, center, or right aligned.

= Why don’t all the buttons show up when I select many? =  
The plugin is optimized for minimalism and speed. Too many buttons can look cluttered and affect load time. Try to limit to three or fewer per section.

== Screenshots ==

1. Scalable Vector Graphics (SVG) Buttons
2. Official Social Share Buttons
3. Default Button Setup: Drag and Drop to Rearrange
4. Per-Page Button and Call-To-Action (CTA) Customizations

== Changelog ==

The current version is 3.0.1 (2025.05.25)

= 3.0.1 (2025.05.25) =
* Fixed cta box default display issue
* Updated svgs
* Updated settings page to use SVG icons
* Cleaned out old icons

= 3.0 (2025.05.20) =
* Removed OpenGraph integration
* Removed Bitly integration
* Removed %%% syntax for manual button placement from CTA templates
* Removed deprecated services and buttons
* Added SVG-based versions of supported buttons
* Added support for floating top buttons with left or right alignment
* Added option to center-align top button group (non-floating)
* Added background and border controls for both button sections
* Updated default display behavior for top buttons (no longer inline with content)
* Updated CTA default messaging to reflect modern engagement patterns
* Cleaned up post editor panel UI
* Cleaned up and optimized CSS
* Cleaned up and optimized JS
* Removed unused assets
* Fixed alignment issues in some themes
* Confirmed support for core post types and custom post types
* Verified compatibility with WordPress 6.x

= 2.3 (2013.10.01) =
* Removed Digg button
* Fixed Pinterest button
* Added rules to fix new Pinterest counter values
* Added Bitcoin as a donation option
* Added Pocket button
* Make Socialize admin responsive

= 2.2.3 (2013.02.09) =
* Added strip_tags() to og:title

= 2.2.2 (2013.02.09) =
* Fixed a few bugs in admin

= 2.2.1 (2012.09.26) =
* Fixed saving of default CTA

= 2.2 (2012.08.09) =
* Added shortcode support
* Global update old posts/pages settings share settings
* Floating share bar
* Cleaned up admin
* Expanded API
* Cleaned up button styles

= 2.1 (2012.07.30) =
* Added Pinterest
* Removed Google Buzz
* Changed location of settings and switched to tabbed navigation
* Added filters and actions for easy customization

= 2.0.6 (2011.08.27) =
* Fixed bitly integration (sponsored by Bryan Eggers of VegasNews.com)

= 2.0.5 (2011.08.19) =
* Switched to new Google Plus One button

= 2.0.4 (2011.07.14) =
* Fixed assorted services javascript glitches

= 2.0.3 (2011.07.07) =
* Added Google +1 button
* Slimmed down number of js calls

= 2.0.2 (2011.05.10) =
* Fixed display issues with default buttons
* Cleaned up admin a bit

= 2.0.1 (2011.04.25) =
* Fixed a few upgrade glitches
* New screenshots

= 2.0 (2011.04.22) =
* Adopted Open Share icon (http://www.openshareicons.com)
* Optimized and organized code into classes
* Created new options pages
* Open graph support
* More design/display options
* Bitly integration
* Sortable buttons

= 1.3.1 (2010.11.30) =
* Fixed LinkedIn javascript

= 1.3 (2010.11.30) =
* Added LinkedIn button
* Cleaned up CSS
* Added option to not display 'Call To Action' box on pages

= 1.2.3 (2010.11.15) =
* Fixed bug in socialize.php in socialize_metabox_action_admin(), variables not loaded properly

= 1.2.2 (2010.11.15) =
* Added unneeded returns to readme.txt

= 1.2.1 (2010.11.15) =
* Used wrong WordPress version in 'Tested up to' in readme.txt

= 1.2 (2010.11.15) =
* Renamed 'Alert Box' to 'Call to Action' Box and edited readme.txt to match
* Re-designed settings page with collapsible drag and drop boxes to organize content
* Moved 'Please Domate' area in settings page
* Added Support area to settings page
* Added Tips and Tricks to settings page
* Added 'Call to Action' meta box to posts and pages so poster can change the call to action for a specific post/page
* Added official Twitter count button
* Added Topsy button
* Added Facebook Like button
* Added additional security to forms and data
* Added color picker to admin
* Updated header css with wp_enqueue_style

= 1.1.5 (2010.06.28) =
* SVN glitch

= 1.1.4 (2010.06.25) =
* Fixed issue where default Google/Yahoo Buzz options didn't work

= 1.1.3 (2010.06.22) =
* Fixed glith where alert box would only display if inline buttons were bing displayed
* Fixed CSS glitch with delicious inline button
* Removed unneccesary calls to displayButtons()

= 1.1.2 (2010.06.18) =
* Added current_theme_supports('post-thumbnails') to prevent error when no featured image

= 1.1.1 (2010.06.18) =
* SVN glitch

= 1.1 (2010.06.18) =
* Fixed Delicious button

= 1.0 (2010.06.18) =
* Commented out javascript
* Added security to page meta box
* Added Yahoo Buzz and Google Buzz buttons
* Removed custom facebook button and replaced with official Facebook Share button
* Created Delicious button with save count
* Provided options to float in-content buttons to right or left
* Provided options to display buttons on different pages
* Added option to hide alert box on specific pages
* Fixed CSS
* Plugin now updates upon activation and keeps record of version
* Buttons can now be displayed in feeds

= 0.4 (2010.03.29) =
* Added wp_is_post_revision() and wp_is_post_autosave() to prevent WordPress from trying to save empty data when autosaving
* Can now add buttons to pages

= 0.3 (2009.10.06) =
* Fixed default options code

= 0.2 =
* Added http://www.fbshare.me widget
* Added backtype Tweetcount Widget
* Added new settings for Twitter Source
* Added default settings

= 0.1 =
* Plugin released

== Upgrade Notice ==

= 3.0 =
Upgrade to 3.0 for modern SVG buttons, enhanced floating options, custom CTA styles, and overall optimizations. Benefit from improved UI, support for WordPress 6.x, and removed deprecated features.