# Native Rendering System — Design Specification

- **Status:** Approved design, pending implementation plan
- **Target:** one milestone release in the 3.4.x series
- **Reference revision:** all file:line references are against 3.4.1 (`99b209c`)

## 1. Summary and goals

CSM's rendering is split across two systems: the classic native path (template
hierarchy → `views/` → theme-overridable partials) and the Pro-era Twig
templating system inherited from Sermon Manager Pro. This project freezes the
Twig system as a **legacy compatibility layer** — never removed, never
invested in — and establishes the native path as the future, behind a single
explicit renderer-selection seam.

Hard requirements:

1. **Byte-identical front-end output** for every existing site, in both
   modes, after normalization of known dynamic tokens (§6a).
2. **Twig must not load at all** on sites not using a legacy template.
3. **Every frozen name survives** (docs/RELEASING.md §Rules): partial
   filenames, `wpfc_sm_template` CPT, `sm_template` option, public filter
   names (`wpfc_sermon_single_v2`, …), template functions
   (`wpfc_get_partial`, …).
4. The **renderer inventory (§3) is the change-management artifact**: every
   legacy render site is enumerated with its planned change, and
   implementation closes each row (§6c).

## 2. Decision record

**Context.** A native-WordPress rendering future requires deciding how the
native and legacy renderers coexist: minimal re-plumbing, an explicit
interface/resolver architecture, or pure bootstrap gating. Constraints:
identical output, one 3.4.x release, frozen names, and incoming users from
legacy Sermon Manager / Sermon Manager Pro who depend on the Twig system.

**Alternatives considered.**

- **A — Conditional bootstrap + thin façade.** Keep the hook-based switchover
  untouched; add load-time gating and port the Elementor views. Strongest
  property: smallest regression surface. Strongest weakness: call sites stay
  scattered; no foundation for later work.
- **B — Renderer interface + resolver.** `SM\Rendering` with
  `RendererInterface`, `NativeRenderer`, `LegacyTwigRenderer`, and a
  `Resolver` as the single decision authority; all call sites route through
  it. Strongest property: one permanent seam for all future rendering work.
  Strongest weakness: touches every render call site in one release; the
  interface calcifies once shipped.
- **C — Pure bootstrap gating.** Conditional requires only; no abstractions.
  Strongest property: tiniest diff. Strongest weakness: maintenance, not a
  system; the "is Twig needed?" decision stays duplicated.

**Criteria.** Blast radius if wrong; foundation value for follow-on work
(decomposition, visual refresh, blocks); reviewability of the
identical-output claim; frozen-contract safety.

**Choice.** B.

**Why.** "I want full documentation of where each change is going to be
made, so that we can preserve the artifact and use it for much more accurate
and precise change management. It's vital to document every place the legacy
renderer exists so we thoroughly replace/upgrade it." B is the only option
whose one-seam architecture makes a complete, auditable change inventory
possible (foundation value + reviewability).

**What would change the answer.** "If you can't make that full documentation
of renderers, then this thing is going to be bug hell and result in angry
customers. If you begin breaking all our rendering with Elementor and Divi,
then it's a mess." Operationally: if the inventory (§3) cannot enumerate
every legacy render site, or if Elementor/Divi output breaks under the
resolver rewiring, B loses to A.

## 3. Legacy renderer inventory

The audit commands that produced this inventory are in Appendix A; they are
re-run at implementation end and the diff must be explainable line-by-line
(§6c). Three review rounds established the granularity rules below.

### Rule 1 — no file may be conditionally loaded

`pro/includes/templating/functions.php` and `pro/includes/templating/wp.php`
fuse universal-output features with the Twig switchover. Verified
consequences of gating either file:

- `functions.php:1143` calls `smp_maybe_install_default_templates()` at file
  load — gating it would stop template registration/rescan for migrating
  SMP sites.
- `functions.php:999` `smp_get_sermons_per_row()` is called from
  `pro/includes/shortcodes/wordpress/wp_archive.php:181` and
  `wp_taxonomy.php:223` on all sites — gating fatals.
- `functions.php:1213` `smp_disable_divi_comments` guards on the Divi theme,
  not on templating — gating changes Divi sites' comment behavior.
- `wp.php:165` registers the **`wpfc_sm_template` CPT** (frozen name) and
  `wp.php:189` creates the default template post that
  `get_default_template_id()` depends on — gating locks users out of the
  legacy system entirely.

The legacy boundary is therefore a **runtime-decision boundary**: files keep
loading; the decision centralizes in the Resolver (§4).

### Rule 2 — single decision authority

`Templating_Manager::is_active()` is today called independently from 8+
sites (`wp.php:62/99/109/229`, `pro/includes/plugin.php:287/301/369`,
`pro/includes/templating/settings.php:56`,
`pro/includes/shortcodes/wordpress/functions.php:39`,
`includes/admin/settings/class-sm-settings-display.php:128`), each call
running an uncached DB query via `get_default_template_id()`
(`templating_manager.php:172–187`; `Template::get_instance()` also
constructs fresh, `template.php:259–275`). The Resolver becomes the sole
authority, memoized per request per blog (§4); admin mutations are safe with
memoization because each ends in `wp_redirect()` + `exit` (verified
`wp.php:515–516`).

### Rule 3 — Twig's eager load is the composer files-autoload

`pro/vendor/composer/autoload_files.php` eagerly includes four Twig runtime
files plus Symfony polyfills on every request, via the unconditional
`require SMP_PATH . 'vendor/autoload.php'` at `plugin.php:118`. Pro's vendor
contains only `twig/twig ^3.28` (+ polyfills). Deferral is safe: CSM's only
`mb_*` usage is `mb_substr`/`mb_strlen` in `views/wpfc-podcast-feed.php:328–329`,
both shimmed by WordPress core (`wp-includes/compat.php:113, :191`) on hosts
without ext-mbstring; there is zero `ctype_*` usage. Composer's
`$GLOBALS['__composer_autoload_files']` dedupe means deferral adds no
cross-plugin redeclare risk.

### 3A. Twig engine core → wrapped by `LegacyTwigRenderer`, internals untouched

| Location | Role | Planned change |
|---|---|---|
| `pro/includes/templating/templating_manager.php` | Engine; only file touching Twig classes (:12–14, :424–425); `render()`, template CRUD, `is_active()` | Wrapped by adapter; `is_active()` becomes delegating shim to Resolver |
| `pro/includes/templating/template.php` | Template model, path re-rooting | Untouched |
| `pro/includes/templating/settings.php` | Per-template `t_settings`; empty-array guard when inactive (:56) | Untouched |
| `pro/includes/templating/functions.php` | Universal-output hooks + installer (see 3B) | Always-loaded, unchanged registrations |
| `pro/includes/templating/wp.php` | Switchover hooks + `wpfc_sm_template` admin UI + CPT | See 3B classification |
| `pro/includes/templating/views/html-editor.php` | Admin template editor | Stays |
| `pro/vendor/` (Twig 3.28), `pro/cache/twig/` | Engine + compile cache | Vendor require deferred into `LegacyTwigRenderer::boot()`; boot-time existence check stays (§5d) |
| `pro/templates/{genesis,gsquare,epiclesis}` | Shipped template sources | Keep shipping |

### 3B. Hook-level classification of `wp.php` and `functions.php`

**Always registered — universal native output (frozen behavior on all sites):**

- `wp.php:112` — `.sm-filtering` wrapper div (unguarded, all sites)
- `wp.php:127–140` + `:196–215` — `.smpro-items-container` wrappers (all
  sites; settings degrade to defaults when inactive)
- `functions.php` — every registration: `:671` settings filter, `:712`
  footer config, `:758` query args, `:805` filtering settings, `:826`/`:841`
  Date filter + hiding, `:882–886`/`:924` query filtering (all builders),
  `:990` date dropdown, `:1213` Divi comments, `:999` per-row columns,
  `:1143` installer (admin-guarded internally)

**Always registered — admin management + frozen CPT:**

- `wp.php:165` CPT registration; `:189` default-template creation; `:43–47,
  :167–191` editor replacement, columns, row actions,
  duplicate/switch/rescan/delete/save

**Registered only when `Resolver::mode() === 'legacy'` (today self-gating
via internal `is_active()` checks):**

- `wp.php:49` (`wpfc_sermon_single_v2`), `:59` (`wpfc_sermon_excerpt_v2`),
  `:98` (`sm_shortcode_sermons_single_output`), `:108`
  (`sm_shortcode_output_override`), `:229` (template CSS enqueue)

### 3C. Direct render call sites

| Location | Today | Planned change |
|---|---|---|
| `pro/includes/shortcodes/elementor/skin-classic.php:103` | `Templating_Manager::render('archive-elementor')` | Dispatch via Resolver (§4) |
| `pro/includes/shortcodes/elementor/skin-cards.php:850` | same | same |
| `pro/views/elementor/archive.twig` | Fallback view; forces Twig on all Elementor-widget sites | Ported to native partial (§5a); retained for legacy mode |
| `pro/views/elementor/taxonomy.twig` | **Dead** — `taxonomy-elementor` has zero call sites (only whitelist entry `templating_manager.php:369`) | Delete under §5b protocol |
| `pro/views/divi/*.twig` + `-divi` contexts (`templating_manager.php:357, :367, :370, :402–405`) | **Dead** — zero call sites; Divi renders via the WP shortcode path | Delete under §5b protocol |
| Taxonomy Elementor skins (`skin-cards-taxonomy.php`, `skin-list-taxonomy.php`) | Native PHP already | Verification targets only |

### 3D. Independent render families (unaffected, listed for completeness)

- **Gutenberg blocks** (`shortcodes_manager.php:151–153`): `gutenberg-v2`
  (stub; registers a live `wp_ajax_*_render` endpoint that outputs a
  placeholder) and deprecated v1 `sermons_blog`/`sermons_taxonomy` (own
  inline markup, `rest_api_init` routes, call native helpers directly).
  Bypass both renderers.
- **Classic native path** (`NativeRenderer`'s frozen surface):
  `includes/sm-template-functions.php` — `template_include` (:16–22),
  `wpfc_sermon_single_v2()` (:546, filter :567), `wpfc_sermon_excerpt_v2()`
  (:587, filter :606), `wpfc_get_partial()` (:760), sorting output (:260),
  media safety net (:62–99); `views/` (8 templates + 6 partials);
  `includes/class-sm-shortcodes.php:895–900, :1316–1322`;
  `pro/includes/shortcodes/wordpress/wp_archive.php:97`,
  `wp_taxonomy.php:106`.
- No AJAX/REST legacy render paths exist. Elementor pagination is
  page-reload. Remaining AJAX handlers are admin, non-render.

### 3E. State flags

| Flag | Producers | Consumers | Disposition |
|---|---|---|---|
| `$GLOBALS['smpro_template']` | `templating_manager.php:520` | none (write-only) | Delete under §5b protocol |
| `SMPRO_RENDER_ERROR` | `wp.php:53, :92`; skins `:105/:852` | skins `:56/:774` | Contract preserved (§5d) |
| `$GLOBALS['smp_in_sm_shortcode']` | `wp.php:88, :103` | `wp.php:67` | Legacy-branch internal; unchanged |

### 3F. Bootstrap chain

- `includes/main.php:1105` loads `pro/includes/plugin.php`, which
  self-instantiates (`plugin.php:759`) at **plugin include time, before
  `plugins_loaded`** — the mode decision therefore executes at include time
  (`$wpdb`/options available). Anything mutating `sm_template` later in the
  same request sees the new mode next request (no in-repo actor does this;
  admin mutations redirect).
- `plugin.php:114–119` `_register_autoloader()`: keeps the boot-time
  `file_exists` check + admin notice; the vendor `require` moves into
  `LegacyTwigRenderer::boot()`.
- `plugin.php:190–191, :210` — templating requires + `Templating_Manager`
  instantiation stay unconditional (Rule 1).

## 4. Architecture — `SM\Rendering`

Four files under `src/Rendering/`, served by the existing `SM\` autoloader
(`/autoload.php`; no bootstrap changes).

**`Resolver`** — sole decision authority.
`Resolver::mode(): 'native'|'legacy'`, memoized per request **keyed by
`get_current_blog_id()`**. Logic moved from `is_active()`: legacy ⟺
`sm_template` option set and ≠ default "Sermon Manager" template ID.
`Templating_Manager::is_active()` becomes a one-line shim to the Resolver
(all scattered callers and `smp_is_templating_being_used()` keep working).

**`RendererInterface`** — `render( string $context, ?WP_Post $post = null,
array $args = [] ): string`. Live context names only (`single`, `archive`,
`taxonomy`, `archive-elementor`); renderers return, never echo; empty string
means "no output, caller uses its fallback."

**`LegacyTwigRenderer`** — adapter over `Templating_Manager::render()`,
internals untouched. `boot()` performs the deferred vendor require
(idempotent). Exceptions pass through to the callers' existing
`catch (RuntimeException)` + `SMPRO_RENDER_ERROR` + notice-div contract.

**`NativeRenderer`** — v1 owns exactly the `archive-elementor` context via
the ported partial (§5a). Page contexts stay on the existing pipeline
(rerouting them is motion without benefit while output is frozen); the
interface is shaped so post-v1 modernization can absorb them.

**Dispatch rule.** `mode === 'legacy'` → `LegacyTwigRenderer` for **all**
contexts, including the Elementor fallback view — legacy sites change by
zero bytes. `mode === 'native'` → `NativeRenderer`. The port's fidelity
target is therefore native sites only, where template settings are empty
(verified guard) and inputs are fully specified by the widget.

**Wiring diff (complete list):**

1. `plugin.php:118` vendor require → deleted (check remains).
2. `wp.php` five switchover callbacks → registered only in legacy mode,
   bodies dispatch via the adapter.
3. Skins (two lines: `skin-classic.php:103`, `skin-cards.php:850`) →
   dispatch via Resolver.
4. `Templating_Manager::is_active()` → shim.
5. Deletions per §5b.

**Request flows.** Native page: unchanged pipeline; Twig never loads; five
legacy filters never registered. Native Elementor widget: skin → Resolver →
NativeRenderer → ported partial; Twig never loads (this is the release's
main win). Legacy site (any page): today's flow exactly, Twig booted lazily
at first dispatch.

## 5. Elementor port, deletions, shims, error handling

### 5a. Ported partial — `views/partials/content-sermon-elementor-archive.php`

Input contract (verified at call sites): `$args = ['thumbnail_html' => …,
'settings' => <widget-derived>, 'elementor' => true]`
(`skin-classic.php:88–93`; skin-cards adds `show_image_video`, `thumbnail`,
`image_position`, `show_badge`, `badge_term`, `show_avatar`, `avatar` —
`skin-cards.php:810–834`). Loaded as a **direct include from the plugin** —
deliberately not routed through `wpfc_get_partial()`, so it is not a
theme-override surface and every future fix reaches every site (the legacy
data-dir templates already demonstrate what override version-skew costs;
long-horizon review, §8). Overridability can be added later if requested;
the reverse is
impossible once themes copy the file. `NativeRenderer` renders it with
output buffering and returns the string. The port replicates `render()`'s
three settings injections (`use_published_date`, `preacher_label`,
`date_format` — `templating_manager.php:471–490`).

**Port rules (all verified against 3.4.1 behavior):**

1. **Settings normalization map.** The partial normalizes `$settings`
   against a complete default map (all keys, empty/falsy defaults) before
   use. Twig silently yields null for absent keys; naive PHP array access
   would warn on every classic-skin render (classic passes none of the
   cards-only keys, and `thumbnail_position` instead of `thumbnail` — so the
   thumbnail block never renders for classic today; empty defaults
   reproduce exactly that).
2. **Escaping parity by echo-vs-return, not by `|raw` markers.**
   `Template_Tags::the_title/the_metadata/the_excerpt` **echo and return
   null** (`template_tags.php`), so their output bypasses Twig's
   autoescaper; in PHP the port calls them directly — same functions, same
   output path. Returned-value interpolations are autoescaped by Twig and
   the port escapes them identically: `esc_url` (permalinks), `esc_html`
   (`badge_term`, `read_more_text`), `esc_attr` (class fragments from
   `columns`/`image_position`). `|raw` values stay unescaped:
   `thumbnail_html`, `avatar`, `wpfc_render_video(...)`.
3. **Preserved quirk 1 — unwrapped excerpt.** The view's
   `{% if settings.show_excerpt and TemplateTags('the_excerpt', settings) %}`
   echoes the excerpt during condition evaluation and always evaluates
   false, so the `elementor-post__excerpt` wrapper is unreachable dead
   markup. The port renders today's actual DOM: excerpt echoed once,
   unwrapped; no `elementor-post__excerpt` div.
4. **Preserved quirk 2 — escaped video embed.**
   `{{ fn('do_shortcode', sermon_video) }}` has no `|raw`, so the
   video-embed thumbnail variant renders entity-escaped markup today. The
   port replicates with `esc_html( do_shortcode( … ) )`.
   Both quirks are **scheduled fixes in the fast-follow release (§8)** —
   intentional behavior changes with changelog entries, landed in the
   window before any site depends on the quirk markup.
5. The port reuses `Template_Tags`
   (`pro/includes/shortcodes/template_tags.php`, always loaded) — a markup
   transcription, not a logic reimplementation.

### 5b. Deletions (dead-code protocol)

Re-verify zero call sites at implementation time (Appendix A greps), then
delete in the same release, each with a changelog entry:

- `pro/views/divi/` (both files) + `-divi` whitelist entries
- `pro/views/elementor/taxonomy.twig` + `taxonomy-elementor` whitelist entry
- `$GLOBALS['smpro_template']` write (`templating_manager.php:520`)

### 5c. Shims

`Templating_Manager::is_active()` → Resolver (keeps all 8+ callers and
`smp_is_templating_being_used()` working). Filter names, partial filenames,
context strings: untouched.

### 5d. Error handling

- Missing `pro/vendor`: boot-time detection + admin notice, unchanged;
  native rendering unaffected (`boot()` never called).
- Invalid/missing legacy template files: unchanged — `render()` throws
  `RuntimeException`; callers' catch → error div + `SMPRO_RENDER_ERROR`
  (verified both skins + `wp.php:91–95`), including the "next item
  canceled" behavior (`skin-classic.php:54–58`).
- Missing default-template post: `get_default_template_id()` returns 0 →
  mode `native` — matches today's `is_active() === false`.
- Elementor editor/preview: same dispatch, no special-casing.

## 6. Testing and verification

### 6a. Byte-parity harness

- **Baseline captured from clean 3.4.1 before implementation begins** (the
  plugin is symlinked into the live test site; capture order is mandatory).
- Page matrix (pinned URLs, epiclesis-dev.local, production dataset): sermon
  single with audio+video+description; archive pages 1–2; one term page per
  all five taxonomies; a `[sermons]` shortcode page; one Elementor
  `sermon_archive` page per skin (classic, cards); filtering UI; podcast
  feed.
- Mode matrix: legacy (site's current state — Genesis active,
  `sm_template = 14490`) and native (`sm_template = 14492`, restored after).
  Insurance: one page each rendered under Gsquare and Epiclesis templates.
- Diff must be empty after a **whitelisted** normalization only: `?ver=`
  tokens, `id="index-<rand()>"` (`content-sermon-wrapper-start.php:312`),
  nonces if present. Whole-URL normalization is forbidden.
- The harness itself — capture script, pinned URL list, normalization
  rules, the Twig-absence mu-plugin (§6b) — is **committed to the repo at
  `tools/parity/` with a README**, so it re-runs on any future rendering
  change instead of rotting as one-off scaffolding.

### 6b. Twig-absence assertion

Temporary mu-plugin logs `get_included_files()` at shutdown: native pages
must include zero files under `pro/vendor/`; legacy pages must include them
(control group). Query count on the archive must be ≤ baseline in both
modes (memoization removes today's repeated `get_default_template_id()`
queries).

### 6c. Inventory closure

Re-run Appendix A; assert no `Templating_Manager::render(` call outside the
adapter, no mode-decision logic outside the Resolver (the `is_active()` shim
delegates; its remaining callers are exactly the §3 Rule 2 list), and the
vendor require only in `boot()`. Every §3 row's planned change is checked
off; the grep diff against spec-time output must be explainable
line-by-line.

### 6d. Behavioral checks

Admin round-trip in both modes (Templates screen lists all four; switch
native→Genesis→native via UI; duplicate; rescan; editor opens). Template
CSS on legacy pages only. Elementor editor preview renders both skins.
`SMPRO_RENDER_ERROR` drill: break `genesis/archive.twig`, verify error div +
canceled-item notice, restore. PHP error log: **zero new lines including
warnings**. Podcast feed byte-identical.

### 6e. Release and canary

Standard RELEASING.md checklist; version stays in 3.4.x. **Publish
Monday–Wednesday only** — church-site traffic peaks Sunday, and the
rollback plan (§10) needs its worst-case propagation window to land on
weekdays. Canary asymmetry:
epiclesis.org runs legacy mode (Genesis), so production canaries the legacy
branch; the native branch's real-world proof is the test-site matrix plus a
fresh-install smoke test. Rollback: no DB or option changes; deletions are
git-reversible; downgrade = previous release zip.

## 7. Known risks

1. Conditional registration changes `has_filter('wpfc_sermon_single_v2')`
   truthiness on native sites; an external plugin sniffing SMP that way
   would false-negative. No in-repo probes exist.
2. External consumers of `smp/shortcodes/archive/skin_classic/render_args`
   could inject keys only the Twig path honored. No in-repo consumers.
3. Mode memoization is per-request; a plugin mutating `sm_template`
   mid-request would see the change next request. No known actor.
4. `class-sm-settings-display.php:128` references a nonexistent
   `\SermonManagerPro\Templating\` namespace (`method_exists` always false)
   — latent dead check, inventoried; fix-or-preserve decided at
   implementation.

## 8. Scheduled follow-on work

Long-horizon review of this spec surfaced that its strategic premise —
"Twig fades with usage" — has no engine unless the follow-on is named and
scheduled. It is:

1. **Fast-follow minor release** (the next 3.4.x after this ships): fix
   preserved quirks 1 and 2 (§5a) as intentional behavior changes.
2. **Second `NativeRenderer` consumer, named now:** the shortcode item
   path (`sm_shortcode_sermons_single_output` /
   `wpfc_sermon_excerpt_v2` chain) is the next context to route through
   the seam, so the interface does not calcify as a one-context wonder.
3. **Legacy-usage signal** (in the milestone release itself, small):
   a Site Health → Info section reporting renderer mode and active
   template name, plus a one-time dismissible notice on the Templates
   screen for legacy-mode sites stating that the built-in renderer is the
   maintained default path. No telemetry exists in the update pipeline;
   the signal is user-facing, and sunset decisions revisit with
   support-ticket evidence.

## 9. Out of scope

Visual refresh; decomposition of `content-sermon-wrapper-start.php` /
`sm-template-functions.php`; block/FSE support; rerouting page contexts
through `NativeRenderer` beyond §8.2; removal of the Twig system or its
admin UI; Gutenberg block families.

## 10. Rollback plan

**Irreversible component:** published releases propagate to auto-updating
sites and cannot be recalled; WordPress never offers downgrades, so
recovery is roll-forward only.

- **Trigger:** epiclesis.org canary fails the post-release smoke pass
  (broken/blank archive, single, or Elementor sermon page, or new fatals);
  or ≥2 independent user reports of sermon rendering broken by this
  version; or an unexplained parity-harness diff against a reporting
  site's configuration.
- **Steps:** (1) classify failure branch via the reporting site's active
  template; (2) delete the bad release + tag to stop further updates;
  (3) publish roll-forward — last good tag, next patch number above the
  bad version, Stable tag + revert changelog line, zip with inner folder
  `church-sermon-manager`, `gh api` release + upload — within 1 hour;
  (4) verify on the LocalWP site (delete
  `external_updates-church-sermon-manager`, force check; expect a few
  minutes of GitHub latest-release lag); (5) if production itself is
  broken, SSH + `~/bin/wp plugin install <previous-zip-url> --force`
  (the documented emergency exception); (6) post-incident, isolate the
  diff with `tools/parity/`, fix, re-release.
- **RTO:** production 15 minutes (manual reinstall); fleet roll-forward
  available ≤1 hour, individual sites recover at their next update check.
- **Not undoable:** visitor-facing breakage already seen during each
  site's exposure window; sites with updates disabled stay on the bad
  version until their admin acts. No data/option residue exists.

## Appendix A — audit commands

Run from the repo root; exclude `pro/vendor/` and `pro/cache/`:

```sh
# Engine + call sites
grep -rn "Templating_Manager\|SMP\\\\Templating" --include="*.php" . | grep -v "pro/cache\|pro/vendor"
grep -rn "use Twig\|Twig_Environment\|Twig_Loader\|new \\\\Twig" --include="*.php" . | grep -v vendor
# Switchover surface
grep -rn "wpfc_sermon_single_v2\|wpfc_sermon_excerpt_v2" --include="*.php" . | grep -v vendor
grep -rn "sm_shortcode_output_override\|sm_shortcode_sermons_single_output\|render_wpfc_sorting_output" --include="*.php" .
# Contexts (dead-code re-verification)
grep -rn "archive-elementor\|taxonomy-elementor\|archive-divi\|taxonomy-divi\|'-divi'" --include="*.php" --include="*.twig" . | grep -v vendor
# State flags
grep -rn "smpro_template\|SMPRO_RENDER_ERROR\|smp_in_sm_shortcode" --include="*.php" . | grep -v vendor
# Decision authority
grep -rn "is_active()" --include="*.php" includes pro/includes src
# Render endpoints beyond page loads
grep -rn "wp_ajax\|rest_api_init" --include="*.php" pro/includes includes
# Polyfill exposure
grep -rn "mb_[a-z_]*(\|ctype_[a-z]*(" --include="*.php" includes views src pro/includes | grep -v vendor
```
