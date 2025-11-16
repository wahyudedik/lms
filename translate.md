# Translation Progress & Checklist

This document tracks which parts of the LMS UI have been updated to use
Laravel’s localization helpers (`__()`, `@lang`, `trans_choice`) and whether
the corresponding keys exist in `resources/lang/en.json` and
`resources/lang/id.json`.

## Status Summary

| Area / Feature                              | Blade Path(s)                                           | Status | Notes |
|---------------------------------------------|---------------------------------------------------------|:------:|-------|
| Public landing page / welcome               | `resources/views/welcome.blade.php`                     | ✅     | All guest navigation, hero, footer, and fallback page strings localized. |
| Guest exam token entry                      | `resources/views/guest/exams/enter-token.blade.php`     | ✅     | Form labels, placeholders, CTA, helper text localized. |
| Guest exam info sheet                       | `resources/views/guest/exams/info.blade.php`            | ✅     | Stats, rules, instructions, SweetAlert text localized. |
| Guest exam taking experience                | `resources/views/guest/exams/take.blade.php`            | ✅     | Question chrome, navigation, anti-cheat warnings localized (JS i18n map). |
| Guest exam review / summary                 | `resources/views/guest/exams/review.blade.php`          | ✅     | Score cards, violation alerts, question-by-question breakdown fully localized (EN/ID keys added). |
| Guest exam list (`guest.exams.index`)       | `resources/views/guest/exams/index.blade.php`           | ✅     | Token-entry flow already fully localized (forms, helper text, CTA, SweetAlert). |
| Guest exam results / review details         | `resources/views/guest/exams/review.blade.php`          | ✅     | Action buttons, answer feedback, and localized placeholders verified. |
| Guest offline exam views                    | `resources/views/offline/exams/*.blade.php`             | ✅     | Index & take flows localized (UI + SweetAlert/JS strings, cache stats, buttons). |
| Auth views (login/register/etc.)            | `resources/views/auth/*.blade.php`                      | ✅     | Login/Register/Reset flows confirmed localized (labels, placeholders, CTAs, helpers). |
| Layouts & shared navigation                 | `resources/views/layouts/*.blade.php`                   | ✅     | Guest footer, AI chat widget, notification bell, and shared components localized (final sweep done). |
| Components (buttons, modals, etc.)          | `resources/views/components/*.blade.php`                | ✅     | Components reviewed; AI widget & notification bell localized, other components are slot-only (no strings). |
| Admin panel views                           | `resources/views/admin/**`                              | ✅     | Core modules localized: AI settings, backups, authorization logs, question bank, landing editor, analytics; no remaining strings. | 
| Guru dashboard/views                        | `resources/views/guru/**`                               | ✅     | Dashboard, courses (index/show/create/edit), materials, exams, questions, reports localized. |
| Siswa dashboard/views                       | `resources/views/siswa/**`                              | ✅     | Dashboard, course listings/progress, exams (index/show/take/review/my-attempts), reports localized. |

Legend: ✅ done, ⏳ in progress / not started.

## Translation Checklist

When localizing a view:

1. **Wrap strings**  
   Use `__('Text')` or `@lang('Text')` for plain strings.  
   Use `trans_choice()` for plural-sensitive strings.

2. **Provide translation keys**  
   Add entries to both `resources/lang/en.json` and `resources/lang/id.json`.  
   Keep keys identical (English source string) to match Laravel’s JSON lookup.

3. **Contextual placeholders**  
   Prefer descriptive strings, e.g. `__('Exam duration: :minutes minutes')`
   so translators understand usage. Document dynamic replacements with `:name`
   style tokens.

4. **JavaScript strings**  
   Build a small `const i18n = { ... }` map using `@json(__('...'))` when
   messages appear in inline scripts (SweetAlert, toasts, etc.).

5. **Validation / flash messages**  
   Ensure controller/validation messages reference translation keys (usually
   in `lang/en/*.php`). Add missing entries there instead of hardcoding.

6. **Testing**  
   - Switch `APP_LOCALE` between `id` and `en`.  
   - Run `php artisan view:clear && php artisan config:clear` if cache is enabled.  
   - Manually navigate flows to confirm no untranslated strings remain.

## Open Tasks

- [x] Sweep offline exam templates for hardcoded Indonesian copy.
- [x] Audit shared layouts and navigation for untranslated helper text (guest layout, AI widget, notification bell).
- [x] Plan phased approach for admin/guru/siswa sections (e.g., module-by-module).
- [x] After each batch, update this document so contributors know what’s left.
- [x] Admin Phase 1: AI settings, backups, authorization logs, question bank UI.
- [x] Admin Phase 2: Landing page editor, AI statistics, remaining modals.
    - [x] Landing page editor views localized
    - [x] AI statistics views
    - [x] Remaining admin modals (confirmations/toasts)
        - [x] Batch 1 (10 views): enrollments, siswa courses show, notifications, guru courses (index/show), guru exams index, guru questions index, guru materials (index/show), qbank import modal
        - [x] Batch 2 (admin UI core): admin courses (index/show/create/edit), admin materials (index/show/create/edit), admin exams (index/show/create/edit), admin questions (index/create/edit), forum categories (index)
        - [x] Batch 3: schools, themes, certificate-settings, backups, authorization-logs, analytics, users (index/show/create/edit) — audited and localized; no remaining Indonesian keys found
        - [x] Batch 4: remaining shared components (final sweep)
- [x] Guru Phase: dashboard, course list, grading/review flows.
    - [x] Batch 1: courses (index/show/create/edit), materials (index/show/create/edit), exams (index/show/create/edit), questions (index/create/edit), reports (index)
- [x] Siswa Phase: dashboard, course progress, exam list/results.
    - [x] Batch 1: courses (index/show/my-courses), exams (index/show/take/review/my-attempts), reports (transcript + PDF)
    - [x] Final audit (profile, emails, certificates, guest, offline) — no remaining Indonesian strings

## Tips for Contributors

- **Granularity:** Commit translations per feature to keep MR/PR reviews focused.
- **Consistency:** Reuse existing keys whenever possible to avoid duplicates.
- **Fallbacks:** If a string contains HTML, consider using Blade components or
  `@lang` with placeholders instead of concatenating strings.
- **Review:** Ask bilingual teammates to verify tone/accuracy of Indonesian copy.

Maintaining this checklist alongside translation work helps ensure we reach full
coverage without overlooking smaller views or embedded scripts. Update the table
and open tasks whenever you finish a section or notice new untranslated areas.

