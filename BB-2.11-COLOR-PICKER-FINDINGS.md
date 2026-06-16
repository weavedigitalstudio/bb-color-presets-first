# Beaver Builder 2.11: Native "Default to Presets Tab" — Findings

**Date:** 2026-06-17
**Investigated in:** `~/Projects/bb-color-presets-first/` (against the bundled BB 2.11-beta.4 + Beaver Themer 1.6-beta.2 betas)
**Purpose:** Record how BB 2.11 now natively defaults the color picker to the presets tab, so dependent plugins (e.g. `GP-Beaver-Integration`) can decide what to drop.

---

## TL;DR

Beaver Builder **2.11** adds a native setting that defaults the color picker to the Presets tab. It is implemented properly inside the React picker as initial component state — **not** a DOM hack. Any plugin currently forcing this behaviour by clicking the tab in JS can retire that code and instead just **force the core option on**.

The native setting **only** handles tab-defaulting. It does **not** hide/restrict the "add custom color" controls. If a plugin also locks sites to Global Colors, that part must stay.

---

## How core implements it (BB 2.11-beta.4)

Verified in `bb-plugin/` (Version: 2.11-beta.4). Three pieces:

### 1. The admin setting
`classes/class-fl-builder-admin-advanced.php` (~line 243):
```php
'default_presets_tab' => array(
    'label'   => 'Color Picker: Default to Presets Tab',
    'default' => 0,          // OFF by default
    'group'   => 'ui',
    'description' => 'When enabled, the color picker will open with the Presets tab selected by default.',
),
```
Stored as WP option: **`_fl_builder_default_presets_tab`** (`'1'` / `1` = on).

### 2. Exposed to JS config
`classes/class-fl-builder-config.php` (~line 185) and `classes/class-fl-controls.php` (~line 72):
```php
'defaultPresetTabInColorPicker' => '1' === $default_presets_tab || 1 === $default_presets_tab,
```
Available at runtime as `FLBuilderConfig.defaultPresetTabInColorPicker` (boolean).

### 3. The React picker reads it as the initial tab
`js/build/fl-controls.bundle.js` (source: `src/FL/controls/components/color/ui/index.js`):
```js
const availableTabs = { srgb, mix, presets };

const Picker = ({
  defaultTab = FLBuilderConfig.defaultPresetTabInColorPicker ? 'presets' : 'srgb',
  ...
}) => {
  const [visibleTab, setVisibleTab] = useState(defaultTab);
  ...
};
```
The picker simply **mounts on the presets tab**. No clicking, no timing, no observers.

---

## The old client-side approach (what to replace)

`bb-color-presets-first` (and likely the GP-Beaver-Integration equivalent) forced this with a JS hack:

- A `MutationObserver` on `document.body` watching for `.fl-controls-dialog`.
- Stacked `setTimeout` retries (10ms / 50ms / 150ms).
- Finds `.fl-controls-picker-bottom-tabs`, grabs the **last** `.fl-control` button, and `.trigger('click')` if not already `.is-selected`.
- Also hooked an iframe observer for `fl-builder.preview-rendered`.

This is brittle (timing-dependent, assumes presets is the last button) and is fully superseded by core in 2.11.

---

## Recommended migration for any dependent plugin

**Replace the entire tab-clicking JS** with a one-line PHP filter that forces the core option on (since the core default is OFF):

```php
// Force BB 2.11+ color picker to default to the Presets tab.
add_filter( 'pre_option__fl_builder_default_presets_tab', function () {
    return '1';
} );
```

Or, if you prefer to set it once rather than filter on every read, `update_option( '_fl_builder_default_presets_tab', 1 )` on activation/init. The `pre_option` filter is cleaner because it can't be overwritten by the user toggling the UI setting.

### Caveats / things to verify before cutting code
1. **Version-gate it.** Only force the option on BB >= 2.11. Check `FL_BUILDER_VERSION` (`version_compare( FL_BUILDER_VERSION, '2.11', '>=' )`). For older BB, the option/picker doesn't exist, so keep whatever legacy path you need (or just no-op).
2. **The custom-color lockdown is NOT in core.** If the plugin also hides the "add color" / "Saved Colors" UI to enforce Global Colors, that CSS must stay. In 2.11 the "+" add-preset button renders as a `.fl-control` button inside `.fl-color-picker-toolbar`. Re-test any restriction selectors against the rebuilt React markup — the old DOM structure changed.
3. **Tab keys are `srgb`, `mix`, `presets`.** If you ever want to default to a different tab, that's the vocabulary.

---

## Bottom line for GP-Beaver-Integration

- The "presets panel first" behaviour → **drop the JS, add the one-line `pre_option` filter, version-gated to BB 2.11+.**
- Any "lock to Global Colors / hide add-color" behaviour → **keep it**, but re-verify selectors against BB 2.11's new picker markup.
- BB did **not** use our code; it's an independent native reimplementation that happens to produce the same result more robustly.
