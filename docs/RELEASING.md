# Releasing Church Sermon Manager

How to ship a new version so every install updates automatically. Follow this
exactly — the update pipeline has been proven end-to-end (sites self-update
from GitHub releases), but several steps have sharp edges noted below.

## How updates reach users (background)

The plugin bundles the plugin-update-checker library (`lib/plugin-update-checker`,
v5.6), wired at the bottom of `sermons.php`. On WordPress's normal update cycle
(~12h) each install queries this repo's **latest non-prerelease GitHub release**,
compares versions, and offers the update through the standard WordPress UI.
Because release assets are enabled, what gets installed is the
`church-sermon-manager.zip` attached to the release — never the raw source
archive. The `Update URI:` header in `sermons.php` keeps wordpress.org from
ever offering a different plugin over this one. Users can force a check with
the "Check for updates" link on the Plugins screen.

## Release checklist

1. **Test first.** Run the full plugin against a real dataset (see
   "Testing" below). Never tag from an untested tree.

2. **Bump versions — all three places:**
   - `sermons.php` header: `* Version: X.Y.Z`
   - `readme.txt`: `Stable tag: X.Y.Z`
   - Changelog entries in BOTH `changelog.txt` and the `## Changelog ##`
     section of `readme.txt`.

3. **Commit to `dev` and push.** `dev` is the default and release branch.

4. **Tag and push the tag:**
   ```
   git tag vX.Y.Z && git push origin vX.Y.Z
   ```
   The tag is `v`-prefixed; the plugin header version is not.

5. **Build the install zip.** The zip MUST contain a single top-level folder
   named exactly `church-sermon-manager` (that's the install directory —
   changing it would orphan every existing install):
   ```
   rm -rf /tmp/zipbuild && mkdir -p /tmp/zipbuild/church-sermon-manager
   rsync -a --exclude '.git' --exclude '.github' --exclude '.gitignore' \
     --exclude '.idea' --exclude 'docs' --exclude 'tests' --exclude 'bin' \
     --exclude 'node_modules' --exclude 'phpcs.xml.dist' \
     --exclude 'phpunit.xml.dist' --exclude '/composer.json' \
     --exclude '/composer.lock' --exclude 'CODE_OF_CONDUCT.md' \
     --exclude '.DS_Store' --exclude 'pro/cache' \
     ./ /tmp/zipbuild/church-sermon-manager/
   cd /tmp/zipbuild && zip -qr church-sermon-manager.zip church-sermon-manager
   ```

6. **Create the GitHub release and attach the zip.** Note: `gh release create`
   may fail if the token lacks the `workflow` scope; the API route always works:
   ```
   gh api repos/riceguitar/Church-Sermon-Manager/releases \
     -f tag_name=vX.Y.Z -f name="X.Y.Z — short title" \
     -F prerelease=false -f body="Release notes here."
   gh release upload vX.Y.Z /tmp/zipbuild/church-sermon-manager.zip \
     -R riceguitar/Church-Sermon-Manager
   ```
   The asset must be named `church-sermon-manager.zip`. The release must be
   non-draft and non-prerelease, or the updater will ignore it. For betas,
   use `-F prerelease=true` — installs will NOT be offered pre-releases,
   which is the point.

7. **Verify the pipeline.** GitHub's "latest release" endpoint can lag a few
   minutes after publishing; don't panic if a check run immediately shows
   nothing. To verify on any install:
   ```
   wp eval 'delete_option("external_updates-church-sermon-manager");
            delete_site_transient("update_plugins"); wp_update_plugins();'
   wp plugin update church-sermon-manager
   ```
   The `external_updates-church-sermon-manager` option is the updater's own
   cache — delete it to force a fresh GitHub query. Expected result:
   `old_version X.Y.(Z-1) → new_version X.Y.Z, status Updated`.

## Testing

- Test on a WordPress install carrying a real dataset (sermons, series,
  templates, Elementor pages), not an empty site.
- If the test install's plugin directory is a **symlink to this repo**, never
  run `wp plugin update` there — WordPress would replace the symlink with an
  extracted directory. Symlinked installs update via `git pull`.
- Minimum smoke pass before tagging: homepage, sermon archive, a sermon
  single (description text must render), an Elementor page using the
  `sermon_archive` widget, the sermon editor (content present), the podcast
  feed (`/?feed=rss2&post_type=wpfc_sermon` — valid iTunes XML), and zero
  new fatals in the PHP error log.

## Rules that keep existing sites working

These names are **frozen** — they're stored in user databases and page-builder
content across every install:

- Post types: `wpfc_sermon`, `wpfc_sm_template`, `wpfc_sm_podcast`;
  taxonomies `wpfc_preacher`, `wpfc_sermon_series`, `wpfc_sermon_topics`,
  `wpfc_bible_book`, `wpfc_service_type`.
- Option prefixes `sermonmanager_*` and `smp_*`; meta keys
  (`sermon_description`, `sermon_date`, `sermon_audio`, `bible_passage`, …).
- Elementor widget names (`sermon_archive`, `sermon_filtering`, …) and
  shortcode tags.
- The user-data directory `wp-content/data/sermon-manager-for-wordpress/`
  (historical name, kept deliberately).
- The install folder name `church-sermon-manager` and the zip's inner folder.
- Public `sm_*` / `smp/*` hook names and template functions
  (`wpfc_get_partial`, …).

## Architecture notes for anyone touching the bootstrap

- `sermons.php` is a **thin loader**: header + legacy-coexistence guard +
  `require includes/main.php`. Keep it that way — PHP early-binds top-level
  classes at compile time, so any class defined directly in `sermons.php`
  would exist even when the guard returns early, defeating it.
- The guard idles this plugin while the legacy "Sermon Manager for WordPress"
  or "Sermon Manager Pro" plugins are active. Data carries over in place;
  there is deliberately no import step.
- Pro-era modules live under `pro/` with their original internal layout;
  `SMP_PATH`/`SMP_URL` constants point there from `includes/main.php`.
- Template filesystem paths are re-rooted at read time
  (`pro/includes/templating/template.php`) — never trust a stored absolute
  path; it breaks on site migration.
