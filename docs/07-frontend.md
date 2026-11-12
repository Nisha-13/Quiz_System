# 7 — Frontend

## 7.1 Rendering model

QuizSphere is a **server-rendered** application. There is no SPA, no client-side
router and no JSON API. Every page is a Blade template rendered on the server
and returned as HTML.

JavaScript exists for four small interactions only:

| Interaction | File |
|---|---|
| Mobile navigation drawer | `resources/views/components/user-navbar.blade.php` (inline `<script>`) |
| Avatar dropdown menu | `resources/views/components/user-navbar.blade.php` (inline `<script>`) |
| Admin sidebar drawer | `resources/views/components/admin-layout.blade.php` (inline `<script>`) |
| Dismissing the "no MCQs" notice | `resources/views/user-quiz-list.blade.php` (inline `<script>`) |

The first three scripts are written as IIFEs with `var` and `addEventListener`,
so they run deferred-safe and never leak globals. The fourth is a short handler
that uses `const` and an arrow function.

## 7.2 Blade templates

### Public site

| Template | Lines | Purpose |
|---|---|---|
| `welcome.blade.php` | 163 | Landing page: hero, featured categories, newest quizzes |
| `categories-list.blade.php` | 102 | Paginated category grid |
| `user-quiz-list.blade.php` | 91 | Paginated quiz list of one category |
| `quiz-search.blade.php` | 90 | Search results (categories + quizzes) |
| `start-quiz.blade.php` | 93 | Quiz start / continue screen |
| `mcq-page.blade.php` | 83 | The question screen with the progress bar |
| `result-page.blade.php` | 140 | Score ring and answer review |
| `user-details.blade.php` | 141 | Attempt history |
| `user-signup.blade.php` | 90 | Registration form |
| `login.blade.php` | 102 | Unified sign-in form (users and admins) |
| `verify-email.blade.php` | 82 | "Check your inbox" screen + resend form |
| `forgot-password.blade.php` | 64 | Forgot-password form |
| `user-set-forgot-password.blade.php` | 70 | New-password form |
| `certificate.blade.php` | 38 | The on-screen certificate |
| `download-certificate.blade.php` | 30 | The PDF source template |

### Admin panel

| Template | Lines | Purpose |
|---|---|---|
| `admin.blade.php` | 92 | Dashboard |
| `categories.blade.php` | 97 | Category management |
| `quiz-list.blade.php` | 55 | The quizzes of one category |
| `admin-quizzes.blade.php` | 69 | Every quiz |
| `admin-questions.blade.php` | 66 | Every question |
| `admin-users.blade.php` | 58 | Every user |
| `admin-attempts.blade.php` | 73 | Every attempt |
| `add-quiz.blade.php` | 181 | Create a quiz and add its questions |
| `show-quiz.blade.php` | 117 | The questions of one quiz, with an add-question form |

### Mail

| Template | Purpose |
|---|---|
| `mail/verify-user.blade.php` | The verification e-mail |
| `mail/user-forgot-password.blade.php` | The password-reset e-mail |

### Shared

| Template | Purpose |
|---|---|
| `partials/certificate-card.blade.php` | The certificate card — included by both the page and the PDF |
| `components/admin-layout.blade.php` | The admin shell (sidebar, topbar, flash messages) |
| `components/user-navbar.blade.php` | The public navigation bar |
| `components/user-footer.blade.php` | The public footer |
| `components/footer.blade.php` | The admin footer |
| `components/pagination.blade.php` | The shared paginator |
| `components/gradient-bar.blade.php` | Renders a gradient as a row of coloured cells |

## 7.3 Blade components

All components are **anonymous** components (a file in
`resources/views/components/` used as `<x-name>`). There are no class-based
components — the `app/View/` directory is empty.

| Component | Usage | Props |
|---|---|---|
| `<x-admin-layout>` | Wraps every admin page | `$slot`, `$name`, `$stats`, optional `$heading` / `$subheading` / `$title` |
| `<x-user-navbar>` | Public pages | none (reads `Session('user')`) |
| `<x-user-footer>` | Public pages | none |
| `<x-footer>` | Admin pages | none |
| `<x-pagination>` | Admin lists | `$paginator` |
| `<x-gradient-bar>` | The certificate | `$from`, `$via`, `$to`, `$steps`, `$height`, `$width`, `$class` |

### `x-gradient-bar`

```blade
<x-gradient-bar from="#0d9488" via="#10b981" to="#0d9488" :steps="24" height="4" class="cert-accent" />
```

It asks `App\Support\Gradient::stops()` for a list of colours and renders them
as a one-row table of fixed-width cells. The reason is that the PDF renderer
supports neither CSS gradients nor flexbox — a row of coloured cells looks the
same in both the browser and the PDF.

`Gradient::stops()` interpolates in RGB:

```php
Gradient::stops('#0d9488', '#10b981', '#0d9488', 24);
// ['#0d9488', '#0d9787', '#0e9b87', '#0e9e86', … 24 colours …, '#0d9488']
```

With a `$via` colour the ramp is split in two halves (start → via, via → end);
without one it is a single linear ramp.

## 7.4 CSS

### Tailwind CSS 4

`resources/css/app.css` is the only Tailwind entry point:

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif, …;
    --color-brand-50:  #f0fdfa;
    /* … brand-100 … brand-900 … */
    --color-ink-800:   #111a30;
    --color-ink-900:   #0b1120;
    --color-ink-950:   #070b19;
}
```

Tailwind 4 is configured **CSS-first**: there is no `tailwind.config.js`. The
`@theme` block defines the design tokens, which generate the utilities
`text-brand-600`, `bg-ink-900`, and so on.

The `@source` directives teach Tailwind where to look for class names. The two
non-obvious ones point at Laravel's pagination views and at the compiled views
in `storage/framework/views`, so that pagination markup keeps its styling.

Beyond the theme the file adds a few global touches: smooth scrolling, a
teal `::selection` and a custom scrollbar.

### Brand palette

| Token | Value | Used for |
|---|---|---|
| `--color-brand-50` … `--color-brand-900` | teal ramp (`#f0fdfa` → `#134e4a`) | Buttons, links, highlights, active states |
| `--color-ink-800/900/950` | `#111a30` / `#0b1120` / `#070b19` | Dark navbar, footer, admin sidebar, quiz screen |
| `--font-sans` | Inter, then the system stack | Body text everywhere |

The site also loads two external resources from CDNs:

* **Bootstrap Icons** — every icon (`<i class="bi bi-…">`). The 15 site views
  pin version `1.11.3`; the two mail templates pin `1.11.1`.
* **Google Fonts — Inter** (weights 400–900).

Both are optional: the layout degrades gracefully if they fail to load.

### Certificate CSS

The certificate uses two **plain CSS** files that live in `public/css/` and are
served directly:

| File | Purpose |
|---|---|
| `certificate.css` | The card layout — tables only, no flexbox, no grid, no gradients, no box shadows |
| `certificate-fonts.css` | `@font-face` rules for Great Vibes and Playfair Display |

They are loaded with a cache-busting query string:

```blade
<link href="{{ asset('css/certificate-fonts.css') }}?v={{ filemtime(public_path('css/certificate-fonts.css')) }}" rel="stylesheet">
```

Because the certificate page links these files directly from `public/`, the
certificate renders correctly **even if the front end has never been built**.

## 7.5 JavaScript

`resources/js/bootstrap.js`:

```js
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```

`resources/js/app.js`:

```js
import './bootstrap';
```

That is the entire JS bundle. Axios is registered globally so that any future
AJAX call can use `axios.get(…)` without an import.

## 7.6 Build pipeline

`vite.config.js`:

```js
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

| Command | Effect |
|---|---|
| `npm run dev` | Vite dev server with hot reloading; writes `public/hot` |
| `npm run build` | Production build into `public/build` (manifest + hashed assets) |

`public/build` is **git-ignored** — it must be rebuilt on every deployment.

### Asset resolution in Blade

```blade
@vite('resources/css/app.css')
@vite('resources/js/app.js')
```

`@vite` reads `public/build/manifest.json` in production and points at the
Vite dev server in development. A page that uses `@vite` therefore fails to
render if the assets were never built and no dev server is running.

The certificate templates deliberately avoid `@vite` for the certificate
stylesheets (they use plain `<link>` tags) so the certificate is independent of
the build.

## 7.7 Fonts and images

### `public/fonts/`

| File | Family | Weight / style |
|---|---|---|
| `great-vibes-400.ttf` | Great Vibes | 400 |
| `playfair-display-400.ttf` | Playfair Display | 400 |
| `playfair-display-400-italic.ttf` | Playfair Display | 400 italic |
| `playfair-display-600.ttf` | Playfair Display | 600 |
| `playfair-display-700.ttf` | Playfair Display | 700 |
| `playfair-display-800.ttf` | Playfair Display | 800 |

These are served from the application itself (not a CDN) so that Dompdf can
embed the very same files in the PDF.

### `public/images/`

| File | Purpose |
|---|---|
| `cert-logo.svg` | The QuizSphere mark in the certificate header |
| `cert-award.svg` | The award icon next to "This certifies that" |
| `cert-check.svg` | The tick in the score chip |
| `cert-calendar.svg` | The calendar in the date chip |
| `cert-edge.png` | The 8 × 400 px gradient strip used as the vertical border |

`cert-edge.png` is generated, not hand-drawn — see
[Certificates](./08-certificates.md).

## 7.8 Layout conventions

* The public pages use a sticky/fixed navbar (`<x-user-navbar>`) and a footer
  (`<x-user-footer>`), with `pt-24` / `pt-28` spacing so the content clears
  the fixed header.
* The admin pages use `<x-admin-layout>`: a fixed 288 px (`w-72`) sidebar on
  large screens and an off-canvas drawer below `lg`, plus a sticky topbar with
  the page heading and the flash-message stack.
* The quiz screen (`mcq-page.blade.php`) uses a dark background
  (`bg-slate-950`) with blurred colour blobs and a glassmorphism card
  (`bg-white/[0.07] backdrop-blur-2xl`).
* Forms that create data always send `@csrf`; the `/submit-next/{id}` form posts
  to a URL that carries the question id and additionally includes a redundant
  hidden `id` field with the same value.
