# Church Sermon Manager #
Contributors: riceguitar
Donate link: https://sierra.host/church-sermon-manager/
Tags: church, sermon, sermons, preaching, podcasting, manage, managing, podcasts, itunes
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 3.3.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sermons, speakers, series, podcasting, templates, and page-builder widgets for your church website — the community-maintained successor to Sermon Manager and Sermon Manager Pro.

## Description ##

### The community-maintained successor to Sermon Manager and Sermon Manager Pro ###

The original Sermon Manager plugins by WP for Church were discontinued, leaving church sites without updates and with Pro license checks calling servers that no longer exist. Church Sermon Manager unifies both plugins into one — every Pro feature is built in, all licensing code is gone, and updates ship from GitHub. Sites switching from the original pair keep all of their data.

**Core features:**

* Add Speakers, Series, Topics, Books, and Service Types
* Attach images to sermons, series, speakers, and topics
* Attach MP3 audio plus PDF, DOC, PPT, or any other files (notes, bulletins)
* Bible verse popups on Scripture references
* Embed video from providers such as Vimeo or YouTube
* Full iTunes-compatible podcasting for all sermons, plus per-series, per-preacher, per-topic, and per-book feeds
* Completely integrated with WordPress search; works with SEO plugins such as Yoast
* REST API at `/wp-json/wp/v2/wpfc_sermon`
* Import from Sermon Browser, Series Engine, and Sermon Audio
* Works with any theme — template files in `/views` can be copied into your theme and customized

**Formerly-Pro features, now built in:**

* Sermon layout templates (Twig-based) with a visual editor
* Multiple podcast feeds with a podcast manager
* Elementor widgets: sermon archive, filtering, and more
* Divi, Beaver Builder, and WPBakery (Visual Composer) modules
* Page assignment for archive and taxonomy pages
* PowerPress compatibility

**Popular shortcodes:**

* `[sermons]` — list the most recent sermons (`per_page="20"` to change the count)
* `[sermon_images]` — sermon series with images in a grid
* `[list_podcasts]` — available podcast services with buttons
* `[list_sermons]` — all series or speakers as a list
* `[latest_sermon]` / `[latest_series]` — the newest sermon or series
* `[sermon_sort_fields]` — filter dropdowns by series, speaker, topic, or date

### Switching from Sermon Manager / Sermon Manager Pro ###

Your data carries over in place — same post types, settings, meta fields, templates, and page-builder widgets. Install and activate Church Sermon Manager (it stays idle with a reminder while the old plugins are active), then deactivate the legacy pair. If your theme shows empty sermon descriptions afterward, run Sermons → Migrate Pro Content. Don't reactivate the old plugins afterward; WordPress will refuse, to prevent a conflict.

### Updates and support ###

The plugin updates itself from [GitHub releases](https://github.com/riceguitar/Church-Sermon-Manager/releases) — no marketplace account, no license key. Report bugs or request features on the [issue tracker](https://github.com/riceguitar/Church-Sermon-Manager/issues). Development happens in the open at [github.com/riceguitar/Church-Sermon-Manager](https://github.com/riceguitar/Church-Sermon-Manager).

Originally based on Sermon Manager by WP for Church. Now maintained by [Sierra Marketing](https://sierra.host/church-sermon-manager/).

## Installation ##

1. Download `church-sermon-manager.zip` from the [latest GitHub release](https://github.com/riceguitar/Church-Sermon-Manager/releases/latest).
2. In your WordPress dashboard go to Plugins → Add New → Upload Plugin, choose the zip, and install.
3. Activate **Church Sermon Manager**. (Switching from the old Sermon Manager plugins? See the switching notes on the Description tab.)
4. Add a sermon under Sermons → Add New. Your sermons appear at `/sermons/` (with pretty permalinks) or via the `[sermons]` shortcode on any page.
5. Future updates arrive automatically through the built-in GitHub update checker.

## Frequently Asked Questions ##

### Will my data survive switching from Sermon Manager / Sermon Manager Pro? ###

Yes. Church Sermon Manager uses the same post types, taxonomies, settings, and meta fields as the originals, and the same template and widget names as Pro. Nothing is imported or converted — the plugin simply reads the data that's already there.

### Which versions can I upgrade from? ###

Sermon Manager (free) 2.15.x–2.30.x: fully supported, data reads in place. Sermon Manager 2.8–2.15.x: supported — bundled background migrations update older data automatically on activation. Sermon Manager 2.x before 2.8: best effort (the migration chain includes pre-2.8 converters) — back up your database first. Sermon Manager Pro 2.0.0–2.0.13 (every public 2.0 release): fully supported, including templates and podcast settings. Sermon Manager 1.x and Pro 1.0 betas are not supported — for old Pro betas, update to Pro 2.0.x first or use Sermons → Migrate Pro Content after switching.

### How do updates work? ###

Through the standard WordPress update system, sourced from GitHub releases instead of wordpress.org. Use "Check for updates" on the Plugins screen to check immediately, or enable auto-updates to install new versions automatically.

### Why isn't this on wordpress.org? ###

The original plugin's directory listing is controlled by another party — and was closed by wordpress.org in December 2025 over a security issue, so it no longer ships anything at all. Distributing through GitHub keeps this fork independent while updates still arrive automatically.

### How do I display sermons on the frontend? ###

Visit `/sermons/` (with pretty permalinks enabled) or `/?post_type=wpfc_sermon`, use the `[sermons]` shortcode on any page, or drop the sermon widgets into Elementor, Divi, Beaver Builder, or WPBakery layouts.

### How do I create a menu link? ###

Go to Appearance → Menus, add a Custom Link with `/sermons/` (or `/?post_type=wpfc_sermon`) as the URL and "Sermons" as the label.

### Where do I get help or suggest a feature? ###

Open an issue on the [GitHub issue tracker](https://github.com/riceguitar/Church-Sermon-Manager/issues).

## Changelog ##

### 3.3.0 (2026-07-16) ###
* Added: MP3 duration auto-calculated for remote audio files (not just local uploads) — the edit screen fills it from a remote URL, and imports/REST sermons get it filled on save. Manual entry still overrides.

### 3.2.3 (2026-07-16) ###
* Fixed: saving a sermon destroyed its taxonomy term dates (root cause of "latest series" showing an old series); future-preached sermons excluded from archives; date comparisons use the site timezone; prev/next navigation ordered by preached date.
* Fixed: podcast feed — enclosure query strings preserved, full-size episode art, multibyte-safe subtitles.
* Fixed: duplicate sermon image on singles; REST video fields saved to correct meta; Divi double comments; PHP 8 nav-menu warning; Twenty Nineteen wrapper class; Import/Export dead links.

### 3.2.2 (2026-07-15) ###
* Fixed: [sermons] shortcode rendered an empty list and could fatal on PHP 8; the real renderers (templating pipeline) are authoritative again and shortcode pagination works across pages.
* Fixed: audio/video players no longer disappear on single sermon pages when a theme renders post content directly ("media missing after 2.16/2.17 updates", upstream #306) — players compose live from sermon data when absent; unaffected sites render byte-identically.
* Fixed: PHP 8 fatal in the sort-fields date dropdown.

### 3.2.1 (2026-07-15) ###
* Fixed: YouTube videos added by URL never started playing in Chrome (player stuck loading) — bundled Plyr upgraded 3.4.7 → 3.7.8; YouTube and Vimeo playback verified, audio regression-checked.
* Note: video-by-URL requires the "Plyr" player under Sermons → Settings.

### 3.2.0 (2026-07-15) ###
* Major: Twig template engine upgraded 1.36 (EOL) → 3.28 (current). Output verified byte-identical across a production-data page corpus and all bundled template sets.
* Changed: Minimum PHP is now 8.1. PHP 7.4/8.0 sites should remain on 3.1.2 until hosting is updated.
* Fixed: Template rendering errors degrade gracefully instead of fataling.

### 3.1.2 (2026-07-15) ###
* Changed: Rewrote plugin-details content — accurate features (including built-in Pro features), switching guide, GitHub update/support links; removed dead vendor copy and upsells.

### 3.1.1 (2026-07-15) ###
* Fixed: Plugins-screen row no longer shows the defunct "Premium support" and "Get Sermon Manager Pro" links; Support points to the GitHub issue tracker.

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
