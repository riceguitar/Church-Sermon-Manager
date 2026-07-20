# Rendering parity harness

Byte-parity check for sermon rendering. Run before and after any change
that can affect front-end output; the diff between runs must be empty.

    SITE=http://epiclesis-dev.local ./capture.sh <run-name>
    diff -ru runs/<before> runs/<after>

- `urls.txt` pins the page matrix (tab-separated `label	path`).
- Normalization is whitelisted only: `?ver=` tokens, `index-<rand>` ids,
  nonce values. Do not widen it — a masked diff is a missed regression.
- Mode matrix: capture once per renderer mode. Switch with
  `wp option update sm_template <id>` (Genesis 14490 = legacy,
  Sermon Manager 14492 = native on the dev site) and switch back.
- `mu-sm-twig-absence.php`: copy into the site's `wp-content/mu-plugins/`
  to log Twig vendor inclusion per request into
  `wp-content/twig-absence.log`; remove it when finished.
- `runs/` is gitignored.
