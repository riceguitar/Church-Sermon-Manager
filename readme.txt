# Church Sermon Manager #  
Contributors: riceguitar, wpforchurch, Alex Gutierrez 
Donate link: https://sierra.host/  
Tags: church, sermon, sermons, preaching, podcasting, manage, managing, podcasts, itunes  
Requires at least: 6.4  
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 3.1.1
License: GPLv2  
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add audio and video sermons, manage speakers, series, and more to your church website.

## Description ##

### Church Sermon Manager is the #1 WordPress Sermon Plugin ###

**This is a maintained fork of the original Sermon Manager plugin.**
**Original plugin by WP for Church: http://wpforchurch.com/**
**Current maintainer: David Sudarma (Sierra.host)**

Sermon Manager is designed to help churches easily publish sermons online. Some of the features include:

* Add Speakers, Series, Topics, Books, and Service Types
* Attach images to sermons, series, speakers, and topics
* Attach MP3 files as well as PDF, DOC, PPT (or any other type!)
* Bible references integrated via Bib.ly for easy text viewing
* Completely integrated with WordPress search
* Embed video from popular providers such as Vimeo or YouTube
* Full-featured API for developers (check it out at `/wp-json/wp/v2/wpfc_sermon`)
* Full-featured iTunes podcasting support for all sermons, plus each sermon series, preachers, sermon topics, or book of the Bible!
* Import sermons from other WordPress plugins
* PHP 5.3+ - you can use Sermon Manager even with older websites!
* PHP 7.2 ready - Sermon Manager is 100% compatible with latest PHP version
* Super flexible shortcode system
* Supports 3rd party plugins such as Yoast SEO, Jetpack, etc
* Quick and professional *free* and paid support
* Works with any theme and can be customized to display just the way you like. You'll find the template files in the `/views` folder. You can copy these into the root of your theme folder and customize to suit your site's design.

### One-Click Importing ###

Sermon Manager supports migration/importing from other popular sermon plugins, such as Sermon Browser and Series Engine.

This is a one click process and currently only supports migration/importing within existing WordPress installations.
Soon you will be able to migrate from those 3rd party plugins to Sermon Manager on a separate server. (for example: moving to completely new website & WordPress installation)

### Popular Shortcodes ###

* `[sermons]` — This will list the 10 most recent sermons.
* `[sermons per_page="20"]` — This will list the 20 most recent sermons.
* `[sermon_images]` — This will list all sermon series and their associated image in a grid.
* `[list_podcasts]` — This will list available podcast services with nice large buttons.
* `[list_sermons]` — This will list all series or speakers in a simple unordered list.
* `[latest_sermon]` — This will list all  latest sermons.
* `[latest_series]` — This will display information about the latest sermon series, including the image, title (optional), and description (optional).
* `[sermon_sort_fields]` — Dropdown selections to quickly navigate to all sermons in a series or by a particular speaker.

For more information on each of these shortcodes please visit [our knowledge base](https://wpforchurch.com/my/knowledgebase/12/Sermon-Manager).

### Expert Support ###

The Sermon Manager is available as a FREE download however in order to maintain a free version we offer [premium support packages](https://wpforchurch.com/wordpress-plugins/sermon-manager/#pricing) for those who need any custom assistance. Paid support means you get exclusive access to the Sermon Manager forum as well as support tickets. This is also a way you can donate to the project to help us offer prompt support and a free version of the plugin.

You can access the paid support options via [our website](http://wpforchurch.com/).

Bug fixing and fixing unexpected behavior *is free* and *always will be free*. Just [make an issue on GitHub](https://github.com/WP-for-Church/Sermon-Manager/issues/new) or [create a support thread on WordPress](https://wordpress.org/support/plugin/sermon-manager-for-wordpress#new-post) and we will solve it ASAP.

### Sermon Manager Pro Features ###

* Change your look with Templates
* Multiple Podcast Support
* Divi Support & Custom Divi Builder Modules
* Custom Elementor Elements
* Custom Beaver Builder Modules
* Custom WPBakery Page Builder Modules
* Works with YOUR theme
* Page Assignment for Archive & Taxonomy
* Migration from other plugins is a breeze
* SEO & Marketing Ready
* Live Chat Support Inside the Plugin
* PowerPress Compatibility
* [Full List of Pro Features]

When you upgrade to Pro you also get premium ticket and support for the free version of Sermon Manager too!

### Developers ###

Would you like to help improve Sermon Manager or report a bug you found? This project is open source on [GitHub](https://github.com/WP-for-Church/Sermon-Manager)!

(Note: Please read [contributing instructions](https://github.com/WP-for-Church/Sermon-Manager/blob/dev/CONTRIBUTING.md) first.)

### WP for Church ###

* [WP for Church](https://wpforchurch.com/) provides plugins and responsive themes for churches using WordPress.
* Keep up with the latest product news & tips, sign up to our [newsletter](https://www.wpforchurch.com/blog)!

## Installation ##

Installation is simple:

1. Just use the "Add New" button in Plugin section of your WordPress blog's Control panel. To find the plugin there, search for `Sermon Manager`
2. Activate the plugin
3. Add a sermon through the Dashboard
4. To display the sermons on the frontend of your site, just visit the `http://yourdomain.com/sermons` if you have pretty permalinks enabled or `http://yourdomain.com/?post_type=wpfc_sermon` if not. Or you can use the shortcode `[sermons]` in any page.

## Frequently Asked Questions ##

### How do I display sermons on the frontend? ###

Visit the `http://yourdomain.com/sermons` if you have pretty permalinks enabled or `http://yourdomain.com/?post_type=wpfc_sermon` if not. Or you can use the shortcode `[sermons]` in any page or post.

### How do I create a menu link? ###

Go to Appearance → Menus. In the "Custom Links" box add `http://yourdomain.com/?post_type=wpfc_sermon` as the URL and `Sermons` as the label and click "Add to Menu".

### I wish Sermon Manager could... ###

We are open to suggestions to make this a great tool for churches! Submit your feedback at [WP for Church](https://feedback.userreport.com/05ff651b-670e-4eb7-a734-9a201cd22906/)

### More Questions? ###

Visit the [plugin homepage](https://wpforchurch.com/wordpress-plugins/sermon-manager/ "Sermon Manager homepage")

## Screenshots ##
1. Sermon Details
2. Sermon Files

## Changelog ##

### 3.1.0 (2026-07-14) ###
* Major: Sermon Manager Pro absorbed — templating, podcasting, and page-builder widgets are built in; existing Pro sites keep all data with no migration.
* Removed: all licensing/phone-home code for the defunct upstream servers.
* Added: self-updates from GitHub releases; legacy-coexistence guard; Migrate Pro Content tool.
* Fixed: empty sermon editor for Pro-era content; template paths after site migration; PHP 8 fatal in date sorting.

### 3.0.1 (2025-05-29) ###
* Added: Support for WordPress 6.8
* Added: Support for PHP 7.4
* Changed: Minimum WordPress version requirement to 6.4
* Fixed: Various compatibility issues

### 3.0.0 (2025-05-29) ###
* Major: Complete codebase modernization
* Update: Raised minimum PHP version requirement to 7.4
* Add: Proper PSR-4 compliant autoloading
* Add: Strict type declarations throughout the codebase
* Add: Complete namespace implementation (SM namespace)
* Add: Type hints and return type declarations for all methods
* Add: Proper dependency management
* Refactor: Converted procedural code to OOP
* Refactor: Modernized Shortcodes class with proper type safety
* Refactor: Improved error handling and input validation
* Remove: Deprecated functions and features
* Remove: Old custom autoloader
* Security: Enhanced WordPress security practices
* Performance: Improved code organization and efficiency

### 2.30.0 (2024-02-XX) ###
* Fixed: Removed the "Description" custom field
* Added: "Data Sync" button in Settings to resolve data issues after updating to the latest version
* Added: Option to enable or disable the Gutenberg Block Editor for sermons

### 2.20.0 (2024-01-XX) ###
* Fixed: TwentyTwentyFour theme design support added

### 2.18.0 (2023-12-XX) ###
* Fixed: Post content field not updating correctly with the post meta key

### 2.17.2 (2023-11-XX) ###
* Added: New shortcode `[latest_sermon per_page=10 order="ASC" orderby="post_modified"]`
* Fixed: iTunes:explicit "false" value in feed

### 2.17.1.2 (2023-10-XX) ###
* Fixed: `[sermon_images hide_title="yes"]` shortcode functionality
* Fixed: PHP Warning with PHP 8.x

### 2.17.1.1 (2023-09-XX) ###
* Added: Support for http:// or https:// in enclosure URL under Podcast settings

### 2.17.1 (2023-08-XX) ###
* Fixed: Compatibility issues with PHP 8.x

### 2.17.0 (2023-07-XX) ###
* Fixed: Error when updating content
* Fixed: PHP Error Unparenthesized
* Fixed: RSS feed not working with PHP 8.0

### 2.16.9 (2023-06-XX) ###
* Fixed: Issues saving with PHP 8.0 and WP 5.9.2
* Fixed: PHP 8 error for twig and divi
* Fixed: Fatal error (Cannot access offset of type string on string)
* Fixed: Sermon series order list in shortcode
* Fixed: Compatibility issues with WP 5.9.3
* Fixed: Image size issue

### 2.16.7 (2023-05-XX) ###
* Fixed: Single and multiple file attachment support

### 2.16.6 (2023-04-XX) ###
* Fixed: Old missing PDF file data issue

### 2.16.5 (2023-03-XX) ###
* Fixed: Hyperlinks being stripped in Description Field

### 2.16.4 (2023-02-XX) ###
* Fixed: Support for multiple PDF file uploads for Notes and Bulletins

### 2.16.3 (2023-01-XX) ###
* Fixed: Image size display in shortcode `[sermon_images display="preachers" order="ASC" orderby="id" size="thumbnail"]`
* Fixed: No follow attribute for mp3 on single & archive pages

### 2.16.2 (2022-12-XX) ###
* Fixed: `sm_get_screen_ids()` issue
* Added: Shortcode parameters control for title, description, and image display

### 2.16.1 (2022-11-XX) ###
* Fixed: WordPress 5.5 compatibility issues

### 2.16.0 (2022-10-XX) ###
* Fixed: Bug with CMB2

### 2.15.19 (2022-09-XX) ###
* Fixed: Security issues
* Fixed: Backend errors

### 2.15.18 (2022-08-XX) ###
* Fixed: Compatibility issues with PHP 7.4 in Elementor

### 2.15.17 (2022-07-XX) ###
* Fixed: Plyr audio download button

### 2.15.16 (2022-06-XX) ###
* Fixed: "after" parameter not working in `[sermons]` shortcode
* Fixed: Improved `[latest_series]` shortcode
* Added: Sidebar in dashboard compatibility for Pro version

### 2.15.15 (2022-05-XX) ###
* Fixed: RSS feed not working

### 2.15.14 (2022-04-XX) ###
* Added: Compatibility for "Pro" theme
* Added: Setting to change default sermon ordering
* Fixed: Date filtering in shortcode
* Fixed: Improved `[latest_series]` shortcode
* Fixed: Service Type filter in backend
* Added: Conditional fields in settings
* Added: Dynamic option retrieval in settings
* Fixed: Select field in settings returning error

### 2.15.13 (2022-03-XX) ###
* Added: Dutch translation
* Added: Support for Dunamis theme
* Added: Support for TwentyNineteen
* Added: Support for ExodosWP
* Changed: Added WordPress author metabox
* Fixed: Service Type not saving in quick edit

### 2.15.12 (2022-02-XX) ###
* Fixed: Fatal error when saving a sermon
* Fixed: Podcast buttons shortcode margin

### 2.15.11 (2022-01-XX) ###
* Added: Support for "The7" theme
* Changed: Added "sermon" order to `[sermon_images]` shortcode
* Fixed: Speed of post saving
* Fixed: Terms not having sermon date set

### 2.15.10 (2022-01-XX) ###
* Added: "include" and "exclude" parameters to shortcode
* Added: Option to force loading plugin views
* Fixed: Edge case PHP bug in feed with taxonomy
* Fixed: Notice when using shortcode

### 2.15.9 (2021-12-XX) ###
* Added: Support for Hueman and Hueman Pro themes
* Added: Support for NativeChurch theme
* Added: Support for Betheme theme
* Added: NIV to verse Bible version
* Changed: Replaced series subtitle with short description in feed
* Changed: Added "action" parameter to filtering shortcode
* Changed: Updated Plyr to 3.4.7
* Fixed: Notice in settings after saving
* Fixed: Filtering arguments in sermons shortcode
* Fixed: Filtering not hiding

### 2.15.8 (2021-11-XX) ###
* Added: Callable select options
* Added: Custom values to settings

### 2.15.7 (2021-10-XX) ###
* Fixed: PHP warning with archive output
* Fixed: Podcast items sorting

### 2.15.6 (2021-09-XX) ###
* Changed: Disabled autocomplete for date preached
* Fixed: Comments not appearing on Divi
* Fixed: Invalid podcast images

### 2.15.5 (2021-08-XX) ###
* Changed: Disabled check for PHP output buffering

### 2.15.4 (2021-07-XX) ###
* Fixed: Output Buffering detection

### 2.15.3 (2021-06-XX) ###
* Added: Option to disable "views" count for editors and admins
* Added: Option to enable sermon series image fallback in feed
* Fixed: Podcast shortcode SVG icons in Firefox
* Fixed: 404 on filtering
* Fixed: Sermon Manager errors with output buffering disabled

### 2.15.2 (2021-05-XX) ###
* Added: Maranatha theme support
* Added: Saved theme support
* Added: Brandon theme support
* Changed: Removed default image
* Fixed: Plyr not loading with Cloudflare
* Fixed: Sermon image not showing
* Fixed: image_size argument in shortcode

### 2.15.1 (2021-04-XX) ###
* Fixed: Multi-term filter for feeds

### 2.15.0 (2021-03-XX) ###
* Added: Ability to override CSS with "sermon.css"
* Added: Default image during installation
* Added: Setting for showing/hiding filter
* Added: Setting for default image
* Changed: Updated Plyr to 3.4.3
* Changed: Re-organized settings
* Fixed: Importing from Sermon Browser
* Fixed: Audio file length and size
* Fixed: Taxonomy archive sermons ordering
* Fixed: "sermon" argument in shortcode
* Fixed: Database errors on Import/Export screen
* Fixed: Pause button display
* Fixed: "Upload Image" button in Podcast settings
* Fixed: Audio file issues
* Fixed: Theme support for pagination
* Fixed: Image selector in settings
* Fixed: Filter in shortcode
* Fixed: Plyr border

### 2.14.0 (2021-02-XX) ###
* Added: Support for Sermon Browser bible verses
* Changed: Adjusted Title column width in admin
* Changed: Organized "Debug" settings
* Fixed: Taxonomy feed URLs
* Fixed: Re-importing deleted sermons
