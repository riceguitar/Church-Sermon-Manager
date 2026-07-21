# WordPress.org Compliance Record — 3.4.2

Remediation of the Plugin Check report dated 2026-07-20 (4,315 findings,
228 files). Strategy: full submission-readiness now; actual directory
submission deferred. Every front-end-affecting change was verified
byte-identical against captured 3.4.1 baselines (`tools/parity/`, both
renderer modes, 12-page matrix on a full production dataset) unless listed
under Deviations.

## Fixed (by commit)

| Commit | Scope |
|---|---|
| `73bb0ff` | Parity harness (`tools/parity/`) + 3.4.1 baselines — the verification instrument for everything below |
| `a48a9bc` | Text domain → `church-sermon-manager`: 975 i18n args, plugin header, 4 translation file pairs + `.pot` renamed (989 findings) |
| `ab63cfc` | 30 ABSPATH guards; License header; 49 `date()`→`gmdate()`; `parse_url`/`strip_tags`/`unlink`/`rand` → WP alternatives; 8 `wp_redirect`→`wp_safe_redirect`; deprecated params; `wp_get_http`→`wp_remote_get` (stream); translators comments; 28 missing-domain args |
| `ea04e55`, `c829055` | Output escaping (~833 findings): `esc_*` where output is byte-identical for legitimate values; `phpcs:ignore` with a specific, verified reason where output is intentional markup (player renderers, theme helpers, CDATA-wrapped WXR, filterable slots, pre-entity-encoded core values) |
| `5b5e916` | `tools/build-zip.sh` (committed-files-only archive; `--wporg` variant strips the update checker + `Update URI` header); `.gitattributes` export-ignore for dev paths; `pro/cache` untracked; Twig compile cache relocated to `uploads/sm-twig-cache` (plugin-directory writes eliminated) |
| `4d5ef88` | Input handling (~207 findings): `wp_unslash` + type-appropriate sanitizers on all request reads; nonce annotations with the true guarding mechanism named (WP core save_post flow, capability-gated admin screens, read-only request state) |
| `ef0ab5f` | Remaining borrowed text domains (`elementor-pro`, `fl-builder`, `locale`, `sermons-blog-layout`, `sermons-taxonomy-layout`, `wordpress-importer`, `Divi` i18n uses) unified to `church-sermon-manager` (685 findings); Divi *theme-detection* string comparisons untouched |

## Declined — with reasons

- **PrefixAllGlobals (~1,100: variables, hooks, functions, namespaces,
  constants, classes).** The `sm_*`/`smp_*`/`wpfc_*`/`SMP\` names are the
  frozen compatibility contract (docs/RELEASING.md §Rules): they are stored
  in user databases and page-builder content across every existing install,
  and referenced by user themes. Renaming breaks live sites. These are
  warnings, documented here as permanent legacy-compatibility exceptions.
- **DirectDatabaseQuery / NoCaching (~105).** Concentrated in the frozen
  legacy templating engine and importer/exporter admin tools. The renderer
  redesign (`2026-07-20-native-renderer-design.md`) centralizes the hottest
  repeated query behind a memoized resolver; the rest is
  admin-surface-only and stays as-is.
- **SlowDBQuery meta/tax-query warnings (~46).** Inherent to the sermon
  data model (meta-keyed dates, taxonomy filtering); indexed lookups would
  require schema changes out of scope for a compliance release.
- **EnqueuedResources / NotInFooter / MissingVersion (~30).** Script
  loading rework is deliberately out of scope; several relate to the
  Cloudflare async-script path that mirrors registered-script data.
- **load_plugin_textdomain (2).** Still required for GitHub-distributed
  installs to load bundled translations; drop after directory submission.

## Deferred to submission time (per decision 2026-07-20)

- `lib/plugin-update-checker/` (all findings in that path) and the
  `Update URI` plugin header. GitHub-installed sites continue receiving
  updates until the plugin is live on WordPress.org; the `--wporg` build
  variant already produces the compliant artifact (verified: zero updater
  files, no Update URI header).
- `bin/`, `tests/`, `phpcs.xml.dist`, `phpunit.xml.dist`,
  `CODE_OF_CONDUCT.md` "application files" findings: excluded from every
  built zip via `.gitattributes`; they exist only in the repository.

## Deliberate behavior changes (not byte-identical, each a bug fix)

1. WXR exporter wraps raw `wp:post_content` in `wxr_cdata()` — previously
   produced malformed XML for HTML/entity content; the bundled importer
   parses both forms identically.
2. Import fetch (`class-sm-import-sm.php`) stores downloaded post content
   unslashed — quotes/backslashes were previously double-slashed.
3. Sanitization edge cases (malformed input only): non-numeric IDs coerce
   to 0, notice text is tag-stripped, term names with quotes now match on
   save. Legitimate values unchanged.
4. Podcast feed channel fields are now XML-escaped at output — output
   changes only where a site's settings contained raw `&`/`<` (previously
   invalid XML).

## Verification

- Parity: 12-page matrix × legacy + native modes, byte-identical after
  whitelisted normalization, re-run after every wave (final: `w6-domains`).
- Zero plugin-originated PHP log lines during the full capture series.
- Closure greps: no i18n call carries a non-plugin domain; no unescaped
  `_e()`; no `date()` outside vendored CMB2; no plugin-directory cache
  writes; all shipped files ABSPATH-guarded (vendored libraries excluded).
- Both zip variants build clean: standard (updater included, no dev
  files), `--wporg` (updater stripped, no Update URI).
- A fresh Plugin Check run is expected to report: zero ERRORs outside
  `lib/plugin-update-checker/`; WARNINGs limited to the Declined classes
  above.

## Re-scan (2026-07-21) and final pass

The re-run report dropped from 4,315 findings to 1,499 (non-updater errors
2,750 → 82). The final pass resolved the 82: line-drift misses in the
filtering/attachments partials and taxonomy views; annotation placements
the sniff ignores on multiline statements (restructured or converted to
`phpcs:disable` blocks); `wp_is_writable`/`wp_mkdir_p` swaps;
`utf8_encode` replaced (PHP 8.2 deprecation); prepared-query annotations;
justified script-print annotations (Cloudflare Rocket Loader, Divi
builder); a missed ABSPATH guard; translators comments moved inside PHP
context; and deletion of stale compiled Twig cache files from the working
tree. Every change was A/B-verified output-neutral in both renderer modes
(same-session capture pairs — day-old baselines had gone environmentally
stale via a WP core auto-update and nonce tick rollover; the harness
normalization now covers those tokens).

**Scan the built zip, not the repository.** Remaining repo-path findings
(`tools/`, `bin/`, `tests/`, lint configs, `.gitattributes`,
`lib/plugin-update-checker/`) are excluded from or deferred in the shipped
artifact; `tools/build-zip.sh --wporg` produces the submission-clean zip.
