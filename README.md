# Church Sermon Manager

Add audio and video sermons, manage speakers, series, topics, and service types,
publish podcast feeds, design sermon layouts with the built-in template engine,
and drop sermon widgets into Elementor, Divi, Beaver Builder, Visual Composer,
or shortcodes — all in one plugin.

Church Sermon Manager is the community-maintained successor to **Sermon Manager
for WordPress** and **Sermon Manager Pro** by WP for Church, which were
discontinued and left thousands of church sites without updates. As of version
3.1.0 the two plugins are unified here: every Pro feature is built in, all
licensing and phone-home code is gone, and the plugin updates itself from
GitHub releases.

## Why this fork exists

- Upstream stopped shipping code in 2020; the Pro licensing servers are dead.
  On many sites the license checks stalled page loads for up to 35 seconds.
- Pro's features (templating, podcasting, page-builder widgets) are too good to
  lose — they now live in this plugin with the dead weight removed.
- Existing sites keep their data: **same post types, taxonomies, options, meta
  keys, shortcodes, widget names, and hooks.** Switching requires no migration.

## Installing

1. Download `church-sermon-manager.zip` from the
   [latest release](https://github.com/riceguitar/Church-Sermon-Manager/releases/latest)
   and install it via Plugins → Add New → Upload.
2. Activate **Church Sermon Manager**.
3. Updates arrive automatically through the built-in GitHub update checker.

## Switching from Sermon Manager / Sermon Manager Pro

### Supported upgrade paths

| You're running | Support |
|---|---|
| Sermon Manager 2.15.x – 2.30.x (WP for Church, the wordpress.org continuation, or the 2.30.x fork) | ✅ Fully supported — data is read in place, nothing to migrate |
| Sermon Manager 2.8 – 2.15.x | ✅ Supported — bundled background migrations bring older data current automatically on activation (dates, series metadata, rendered content) |
| Sermon Manager 2.x earlier than 2.8 | ⚠️ Best effort — the same migration chain includes pre-2.8 converters and should work; take a database backup first |
| Sermon Manager 1.x | ❌ Not supported |
| Sermon Manager Pro 2.0.0 – 2.0.13 (every public 2.0 release) | ✅ Fully supported — templates, podcast settings, and description content carry over in place |
| Sermon Manager Pro 1.0 betas | ❌ Not supported directly — update to Pro 2.0.x first, or switch anyway and run Sermons → Migrate Pro Content to recover descriptions |

Note: the original plugin's wordpress.org listing was closed in December 2025
over a security issue and is no longer available for download — sites still
running it receive no updates of any kind.

### How to switch

1. Install Church Sermon Manager (leave your old plugins alone for now).
2. Activate it. While the legacy plugins are still active it stays idle and
   shows a reminder notice — nothing breaks.
3. Deactivate **Sermon Manager for WordPress** and **Sermon Manager Pro**, then
   reload. Church Sermon Manager takes over with your existing sermons,
   settings, templates, and podcast feeds intact.
4. If your theme shows empty sermon descriptions afterward, run
   **Sermons → Migrate Pro Content** — it copies Pro's description field into
   native post content (with automatic backups).
5. Re-save Settings → Permalinks if sermon URLs 404.
6. Don't reactivate the legacy plugins afterward; WordPress will refuse the
   activation to prevent a conflict.

## What's included

| Feature | Notes |
|---|---|
| Sermon post type + taxonomies | Speakers, series, topics, books, service types |
| Audio/video players | Plyr, MediaElement, WordPress native |
| Podcasting | iTunes-compatible feeds, per-series/speaker/topic feeds, podcast manager |
| Templates | Twig-based sermon layouts with a visual editor |
| Page builders | Elementor widgets, Divi, Beaver Builder, Visual Composer, shortcodes |
| Import/export | Sermon Audio, Series Engine, and more |
| REST API | `/wp-json/wp/v2/wpfc_sermon` |

## Requirements

- WordPress 6.4+
- PHP 8.1+ (tested through 8.4; 3.1.2 is the last release supporting PHP 7.4)

## Contributing

Issues and pull requests are welcome. The `dev` branch is the working branch;
releases are tagged from it. Please keep the data-compatibility guarantee in
mind: post type names, option names, meta keys, and widget names are frozen.

## Credits and license

Originally based on Sermon Manager by WP for Church. Forked and maintained by [Sierra Marketing](https://sierra.host/church-sermon-manager/) (David Sudarma).
GPL-2.0 — see [LICENSE](LICENSE).
