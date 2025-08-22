## AI Decision Cards — Refactor TODO List (Modularization Plan)

This checklist guides the migration from a single PHP file to a modular, class-based WordPress plugin. Complete phases in order; each phase should be independently shippable with no behavior changes unless stated.

- Owner: <fill>
- Target version bump: 1.3.0 (after all phases)
- WordPress: 6.0+
- PHP: 7.4+

---

## Guiding principles
- Keep public APIs, option names, meta keys, and asset handles unchanged.
- Move code first; change behavior later. Each phase must be releasable.
- Use namespaces `AIDC\*` and prefixes `aidc_` / `_aidc_` consistently.
- Place rendering in `views/`; keep logic in classes.
- Sanitize inputs, escape outputs; verify nonces; respect capabilities.
- Wrap user-facing strings for i18n.

---

## Phase 0 — Bootstrap skeleton (no functional changes) ✅ **COMPLETED**
- [x] Create `includes/class-plugin.php` orchestrator (singleton `Plugin::instance()->boot()`).
- [x] Create `includes/class-activator.php` and `includes/class-deactivator.php`.
- [x] Create `includes/class-i18n.php` (textdomain loading to be moved later).
- [ ] ~~Update `ai-decision-cards.php`~~ → **Moved to Phase 8** (bootstrap switch)

Acceptance
- [x] Plugin activates/deactivates; no errors/warnings.
- [x] No behavioral changes; existing functionality preserved.

---

## Phase 1 — CPT + Meta extraction ✅ **COMPLETED**
- [x] Create `includes/class-cpt.php` and move `register_cpt()` and `register_meta_fields()`.
- [x] Hook registration on `init` inside the class.
- [x] Ensure rewrite flush on activation via `Activator` (handled via temporary dual loading).

Acceptance
- [x] `decision_card` CPT present and editable.
- [x] Meta `_aidc_status`, `_aidc_owner`, `_aidc_due` work unchanged.

---

## Phase 2 — Public layer (assets, content filter, shortcodes) ✅ **COMPLETED**
- [x] Create `public/class-public-assets.php` to enqueue public CSS/JS.
- [x] Create `public/class-public.php` and move: `prepend_meta_banner()`, `get_or_create_public_page_url()`.
- [x] Create `public/class-shortcodes.php`; move `register_shortcodes()`, `shortcode_decision_cards_list()`, `shortcode_single_decision_card()`.
- [x] Extract shortcode HTML into `public/views/shortcode-list.php` and `public/views/shortcode-single.php`.
- [x] Keep `custom_search_where()` with shortcodes (or a small helper if shared).

Acceptance
- [x] Shortcodes render identically (list and single) across themes.
- [x] Banner prepends on `decision_card` content.
- [x] Public page helper returns a working permalink.

---

## Phase 3 — Admin layer (menus, pages, assets, notices) ✅ **COMPLETED**
- [x] Create `admin/class-admin-assets.php` to enqueue admin CSS/JS (+ localize).
- [x] Create `admin/class-admin.php` for menus: Settings, Generate, Display, Shortcodes, Changelog.
- [x] Move admin notices into Admin class.
- [x] Extract admin page renders into `admin/views/`: `settings.php`, `generate.php`, `display.php`, `shortcodes-guide.php`, `changelog.php`.
- [x] Reuse public views for preview where possible.

Acceptance
- [x] All admin screens work and match current UX.
- [x] Assets load only on our admin pages.

---

## Phase 4 — Generator (admin-post) + AI client ✅ **COMPLETED**
- [x] Create `includes/class-ai-client.php` (OpenAI-compatible): endpoint building, headers, requests, error handling.
- [x] Create `includes/class-generator.php` and move `handle_generate()`; register `admin_post_aidc_generate`.
- [x] Keep current prompt and sanitization.

Acceptance
- [x] Generate form creates a draft `decision_card` with meta.
- [x] Errors/notices redirect as before.

---

## Phase 5 — Admin AJAX (Test API) ✅ **COMPLETED**
- [x] Create `admin/class-admin-ajax.php` and move `handle_api_test()`; register `wp_ajax_aidc_test_api`.
- [x] Keep response structure and messages.

Acceptance
- [x] Test API button returns success/error as before.

---

## Phase 6 — Helpers and shared utilities
- [ ] Create `includes/class-helpers.php` for small utilities: `redirect_with_notice()`, `esc_attr_val()` (or place where most appropriate).
- [ ] Avoid bloat; keep helpers minimal and generic.

Acceptance
- [ ] No duplicated helper code; single, reusable implementations.

---

## Phase 7 — Views tidy-up and styling
- [ ] Remove stray inline styles from PHP; keep in `assets/css/*`.
- [ ] Ensure `views/` are presentation-focused; no business logic.

Acceptance
- [ ] No unnecessary inline styles remain; accessibility preserved.

---

## Phase 8 — QA, i18n refresh, docs, version bump
- [ ] Run manual test checklist (below) end-to-end.
- [ ] Update POT file if strings changed (optional).
- [ ] Update developer docs/readme with new structure.
- [ ] Update changelog and bump to 1.3.0.

Acceptance
- [ ] No regressions in features, shortcodes, admin UX, or public display.

---

## Manual test checklist (run after each phase)
- [ ] Activate/deactivate without errors; CPT registers; rewrites OK.
- [ ] Create/edit/publish `decision_card` posts.
- [ ] Shortcodes in a page (list + single) render; filters/search/pagination work.
- [ ] Meta banner appears on single `decision_card` content.
- [ ] Admin: Settings save; Test API works; Generate creates draft and redirects.
- [ ] Public Display page link works and shows cards; admin preview matches.

---

## Optional nice-to-haves (after Phase 8)
- [ ] Add Settings link on Plugins list row.
- [ ] Simple view helper for locating/rendering templates.
- [ ] Unit tests for AI client (mock HTTP) and helpers.
- [ ] PSR-4 autoload via Composer (keep runtime requires until adopted).

---

## Working notes
- Prefer “move code” commits before extracting views when splitting large methods.
- One phase per PR for clean history and easy rollback.

---

## Detailed implementation steps (actionable)

The steps below specify exactly what to create/change, where to move code from `ai-decision-cards/ai-decision-cards.php`, and how to verify. Follow in order. Keep commits small and focused.

### Phase 0 — Bootstrap skeleton (no functional changes) ✅ **COMPLETED**
- Create directories:
  - [x] `includes/`
  - [x] `admin/`
  - [x] `admin/views/`
  - [x] `public/`
  - [x] `public/views/`
- Create files (stubs only, no behavior):
  - [x] `includes/class-plugin.php` — namespace `AIDC\Includes`; class `Plugin` with `instance()` and `boot()` (empty `boot()` for now).
  - [x] `includes/class-activator.php` — namespace `AIDC\Includes`; class `Activator` with static `activate(): void` (empty for now).
  - [x] `includes/class-deactivator.php` — namespace `AIDC\Includes`; class `Deactivator` with static `deactivate(): void` (empty for now).
  - [x] `includes/class-i18n.php` — namespace `AIDC\Includes`; class `I18n` with `register()` that later hooks `load_plugin_textdomain`.
- Do not change current loader (`aidc_init`) yet. This phase introduces files only.
- PR checklist:
  - [x] New files added, autoload not required, no references from runtime.
  - [x] No fatal errors when activating plugin.

### Phase 1 — CPT + Meta extraction ✅ **COMPLETED**
- Create file:
  - [x] `includes/class-cpt.php` — namespace `AIDC\Includes`.
    - Methods: `register(): void` (hooks `init`), `register_cpt(): void`, `register_meta_fields(): void` (copy logic from `AIDC_Plugin::register_cpt()` and `register_meta_fields()`).
- Wire-up (minimal, non-breaking):
  - [x] In `includes/class-plugin.php`, inside `boot()`, require `class-cpt.php` and call `(new \AIDC\Includes\Cpt())->register();`.
  - [x] In `ai-decision-cards.php`, temporarily disable duplicate registration by guarding the original call paths:
    - In `AIDC_Plugin::init_hooks()`, comment or remove the `add_action( 'init', [ $this, 'register_cpt' ] )` line.
    - Keep `register_meta_fields()` only within new class.
    - Note: This is the only edit in the legacy class for this phase.
  - [x] **Additional**: Added temporary dual loading in `aidc_init()` to ensure new CPT class works before Phase 8 bootstrap switch.
- Testing:
  - [x] Visit Admin > Decision Cards. Create/edit a post.
  - [x] Confirm CPT UI and meta still work.
  - [x] Hard refresh permalinks if needed.
- PR checklist:
  - [x] No duplicate CPT registration warnings or errors.
  - [x] Activation flush handled (temporarily still via legacy, moved in Phase 0/8).
  - [x] **Fixed**: Invalid CSS block in shortcode meta box removed.

### Phase 2 — Public layer (assets, content filter, shortcodes) ✅ **COMPLETED**
- Create files:
  - [x] `public/class-public-assets.php` — namespace `AIDC\PublicUi`; method `register()` adds `wp_enqueue_scripts` hook; move logic from `AIDC_Plugin::enqueue_public_assets()`.
  - [x] `public/class-public.php` — namespace `AIDC\PublicUi`; method `register()` adds `the_content` filter for `prepend_meta_banner()` and exposes `get_or_create_public_page_url()`.
  - [x] `public/class-shortcodes.php` — namespace `AIDC\PublicUi`; method `register()` adds both shortcodes; methods `render_list_shortcode()` and `render_single_shortcode()`; keep `custom_search_where()` here.
  - [x] Views
    - [x] `public/views/shortcode-list.php` — markup only (list/grid; accept sanitized data).
    - [x] `public/views/shortcode-single.php` — markup only.
- Wiring:
  - [x] In `includes/class-plugin.php::boot()`, instantiate and call `register()` on the three public classes.
  - [x] In legacy `AIDC_Plugin::init_hooks()`, comment/remove: `register_shortcodes()` and `wp_enqueue_scripts` hook and `the_content` filter to prevent duplicates.
- Data flow rules:
  - [x] Prepare/sanitize data in classes; pass arrays to views; escape in views on output.
- Testing:
  - [x] Shortcodes still render on frontend pages.
  - [x] Search, filters, pagination work.
  - [x] Banner still prepends to `decision_card` content.
- PR checklist:
  - [x] No duplicate hooks; assets load once.
  - [x] Views contain no business logic.

### Phase 3 — Admin layer (menus, pages, assets, notices) ✅ **COMPLETED**
- Create files:
  - [x] `admin/class-admin-assets.php` — namespace `AIDC\Admin`; move logic from `AIDC_Plugin::enqueue_admin_assets()` inc. `wp_localize_script`.
  - [x] `admin/class-admin.php` — namespace `AIDC\Admin`; registers menus and renders via views (below); move `register_admin_pages()` and `aidc_admin_notices()` behavior here.
  - [x] Views under `admin/views/`:
    - [x] `settings.php` — form markup; read/write options via provided variables; nonce present.
    - [x] `generate.php` — form markup; nonce present; submit to `admin-post.php`.
    - [x] `display.php` — admin preview; reuse public render where possible.
    - [x] `shortcodes-guide.php` — content moved from `render_shortcodes_page()`.
    - [x] `changelog.php` — content moved from `render_changelog_page()`.
- Wiring:
  - [x] In `includes/class-plugin.php::boot()`, instantiate `AdminAssets` and `Admin` only when `is_admin()`.
  - [x] In legacy `AIDC_Plugin::init_hooks()`, comment/remove: `admin_menu`, `admin_enqueue_scripts` hooks and associated render methods to avoid duplicates.
- Testing:
  - [x] All admin pages render correctly; settings save; localized strings appear.
  - [x] Only our admin pages enqueue our assets.
- PR checklist:
  - [x] Nonces/capabilities preserved (`manage_options`, `edit_posts`, `read`).

### Phase 4 — Generator (admin-post) + AI client ✅ **COMPLETED**
- Create files:
  - [x] `includes/class-ai-client.php` — namespace `AIDC\Includes`; class `AiClient` with `completeChat( $endpointBase, $apiKey, $model, array $messages, array $options=[] ): array` returning decoded array or throwing `WP_Error`.
  - [x] `includes/class-generator.php` — namespace `AIDC\Includes`; class `Generator` with `register()` to hook `admin_post_aidc_generate`; move logic from `AIDC_Plugin::handle_generate()`; call `AiClient`.
- Behavior parity:
  - [x] Preserve prompt, sanitization (`wp_kses` allowed tags), post creation, meta updates, and redirects.
- Wiring:
  - [x] In `includes/class-plugin.php::boot()`, instantiate `Generator` unconditionally (admin-post runs in admin context).
  - [x] In legacy `AIDC_Plugin::init_hooks()`, comment/remove `admin_post_aidc_generate` hook registration.
- Testing:
  - [x] Submitting Generate form creates draft and redirects to edit screen.
  - [x] Error cases show admin notices.
- PR checklist:
  - [x] Timeouts, headers, and endpoint selection (`.../v1/` vs `v1/chat/completions`) match current behavior.

### Phase 5 — Admin AJAX (Test API) ✅ **COMPLETED**
- Create file:
  - [x] `admin/class-admin-ajax.php` — namespace `AIDC\Admin`; class `AdminAjax` with `register()` to add `wp_ajax_aidc_test_api`; move `handle_api_test()` here.
- Wiring:
  - [x] In `includes/class-plugin.php::boot()`, instantiate `AdminAjax` only in `is_admin()`.
  - [x] Remove legacy `wp_ajax_aidc_test_api` handler from `AIDC_Plugin`.
- Testing:
  - [x] Test API button flows through AJAX and reports success/error.
- PR checklist:
  - [x] Nonce and capability checks intact; JSON structure unchanged.

### Phase 6 — Helpers / shared utilities
- Create file:
  - [ ] `includes/class-helpers.php` — namespace `AIDC\Includes`; static helpers: `redirect_with_notice( string $msg, string $type='success' )`, `get_option_attr( string $name, string $default='' ): string`.
- Refactor:
  - [ ] Replace legacy utility methods in classes with calls to `Helpers` where appropriate.
- Testing:
  - [ ] Redirect and option helper behavior unchanged.

### Phase 7 — Views cleanup
- Move styling:
  - [ ] Remove leftover inline `<style>` blocks in PHP pages; ensure `assets/css` has equivalent styles.
- Views discipline:
  - [ ] Ensure views contain only echo/escape + minimal conditionals; no DB or HTTP calls.
- Testing:
  - [ ] Visual parity (frontend/admin) vs. pre-refactor.

### Phase 8 — Finalize & switch bootstrap
- Main file changes (`ai-decision-cards/ai-decision-cards.php`):
  - [ ] Define constants (keep).
  - [ ] Require new classes: `includes/class-plugin.php`, `includes/class-activator.php`, `includes/class-deactivator.php`.
  - [ ] Replace legacy `aidc_init()` and `AIDC_Plugin` boot with: `add_action( 'plugins_loaded', fn()=>\AIDC\Includes\Plugin::instance()->boot() );`.
  - [ ] Register activation/deactivation hooks pointing to `Activator::activate` / `Deactivator::deactivate`.
  - [ ] Remove or deprecate legacy class `AIDC_Plugin` (file-level search to ensure no references remain).
- i18n:
  - [ ] Move `aidc_load_textdomain()` into `I18n` and register within `Plugin::boot()`.
- Versioning/docs:
  - [ ] Update changelog and bump to 1.3.0.
- Testing:
  - [ ] Full manual checklist below passes.

---

## Pull request checklist (repeat for each phase)
- [ ] Scope: Only the files and responsibilities of this phase changed.
- [ ] Hooks: No duplicates; removed legacy hooks when replacing.
- [ ] Security: Capabilities and nonces verified; sanitize/escape present.
- [ ] i18n: New user-facing strings wrapped; textdomain correct.
- [ ] Back-compat: Option/meta keys and asset handles unchanged.
- [ ] QA notes: Steps for reviewer to verify included.
