# 8 — Certificates

The certificate is the centrepiece of the application: it is what a user works
towards, and it is the most technically involved part of the code base.

## 8.1 The two renderings

| | On screen | PDF |
|---|---|---|
| Route | `GET /certificate/{id?}` | `GET /download-certificate/{id?}/{file?}` |
| Action | `UserController@certificate()` | `UserController@downloadCertificate()` |
| Template | `resources/views/certificate.blade.php` | `resources/views/download-certificate.blade.php` |
| Engine | The browser | Dompdf |
| Paper | Screen | A4 landscape (297 × 210 mm) |

Both templates include **the same file**:

```blade
{{-- certificate.blade.php --}}
@include('partials.certificate-card', ['data' => $data, 'assetBase' => asset('')])

{{-- download-certificate.blade.php --}}
@include('partials.certificate-card', ['data' => $data, 'assetBase' => ''])
```

The only difference is `$assetBase`: the page prefixes the image paths with the
asset URL, the PDF uses relative paths because Dompdf resolves them against
`public_path()`.

and both are styled by **the same stylesheet**, `public/css/certificate.css`:

* the page links it with a plain `<link>` tag;
* the PDF inlines its content into the `<style>` block of
  `download-certificate.blade.php`.

A test (`CertificateDownloadTest` — *"renders the certificate page and the pdf
from the same card"*) asserts that the markup of the card is present in both
renderings, so the two cannot drift apart silently.

## 8.2 The card

`resources/views/partials/certificate-card.blade.php` renders:

| Part | Content |
|---|---|
| Header | The QuizSphere logo, the wordmark and "Official Certificate" |
| Accent | A `<x-gradient-bar>` 4 px tall in the brand colours |
| Kicker | "This certifies that" |
| Name | The user's name in Playfair Display 800 (34 px if the name is longer than 26 characters) |
| Name rule | A 160 px gradient bar under the name |
| Body | "has successfully completed the quiz" + the quiz name in quotes |
| Chips | `Score: 80% (8/10)` and the completion date |
| Quote | "Learning is the key to success, and success begins with achievement." |
| Signatures | The admin signature ("Nisha"), the certificate id and the date |
| Footer | An 8 px gradient bar |

The certificate id is built as:

```php
'ID: QZ-'.date('Y').'-'.str_pad($data['id'], 5, '0', STR_PAD_LEFT)
// → ID: QZ-2026-00042
```

The downloadable file name follows the same numbering:

```php
$filename = 'certificate-quizsphere-'.str_pad($record->id, 5, '0', STR_PAD_LEFT).'.pdf';
// → certificate-quizsphere-00042.pdf
```

## 8.3 Eligibility

`UserController::loadCertificateRecord($id)` decides whether a certificate may
be issued. It returns a two-element array — `[?Record, ?string]` — holding
either the record or an error message; every caller destructures it:

```php
[$record, $error] = $this->loadCertificateRecord($id);
if (! $record) {
    return redirect('user-details')->with('message-error', $error);
}
```

The checks, in order:

```php
1. No signed-in user?            → 'Please login to view your certificate.'
2. No id given?                  → look up the newest completed attempt of the
                                    quiz in the `currentQuiz` session key;
                                    without that session key →
                                    'Certificates are available for completed
                                    quizzes only.'
3. Record not found, or it       → 'Attempt not found.'
   belongs to another user?
4. Record status is not 2?       → 'Certificates are available for completed
                                    quizzes only.'
5. Score below 70 %?             → 'You scored 62%. You need at least 70% to
                                    earn a certificate.'
6. Otherwise                     → the certificate is issued
```

The threshold is a class constant:

```php
public const CERTIFICATE_THRESHOLD = 70;
```

The same value is passed to the views as `$passMark`, so the result page and
the attempt history use exactly the same rule.

## 8.4 The PDF pipeline

```
downloadCertificate()
  │
  ├─ loadCertificateRecord()            eligibility check
  │
  ├─ certificateData()                  → ['quiz', 'name', 'id', 'correct',
  │                                        'total', 'score', 'date']
  │
  ├─ certificateCss()                   reads public/css/certificate-fonts.css
  │                                    + public/css/certificate.css
  │                                    and rewrites:
  │                                      url('/fonts/…')  →  url('file:///…/public/fonts/…')
  │                                      url('/images/…') →  url('file:///…/public/images/…')
  │
  ├─ view('download-certificate')       → HTML with the card and the inlined CSS
  │
  ├─ Dompdf\Options
  │    isRemoteEnabled = false          no external requests
  │    defaultFont    = Helvetica
  │    fontDir        = storage/app/fonts   (created if missing)
  │    fontCache      = storage/app/fonts
  │    chroot         = base_path()
  │
  ├─ Dompdf::setBasePath(public_path())
  ├─ Dompdf::loadHtml($html)
  ├─ Dompdf::setPaper('a4', 'landscape')
  ├─ Dompdf::render()
  │
  └─ FileDownloadResponse::make(request(), $dompdf->output(), $filename, 'application/pdf')
```

### Why the CSS is rewritten

Dompdf refuses to fetch remote resources (`isRemoteEnabled = false`) and cannot
resolve `url('/fonts/…')` because that is a server path, not a file path.
`certificateCss()` therefore replaces

```
url('/fonts/playfair-display-700.ttf')
```

with

```
url('file:///D:/Laravel/quiz-system/public/fonts/playfair-display-700.ttf')
```

The `chroot` option is set to `base_path()` so the renderer is only allowed to
read files inside the project.

The font cache in `storage/app/fonts` is populated on the first export; the
`GreatVibes` and `PlayfairDisplay` names appear inside the generated PDF, which
is what the test *"embeds the certificate fonts so the pdf matches the
typography of the page"* checks.

## 8.5 CSS features Dompdf cannot do, and the workarounds

| Feature | Problem | Workaround in this project |
|---|---|---|
| CSS gradients | Not supported | `App\Support\Gradient` computes the colour stops; `<x-gradient-bar>` renders them as a row of coloured cells |
| Flexbox | Not supported | The card uses `<table>` layout with `display: table` / `table-cell` |
| Grid | Not supported | Never used in the certificate CSS |
| Box shadows | Not supported | Replaced with flat borders and background colours |
| Web fonts from a CDN | `isRemoteEnabled = false` | The fonts are read from `public/fonts` and embedded |
| `@page` size | — | Set in the PDF template: `size: 297mm 210mm; margin: 0` |

The vertical border of the card is the one element that *is* an image: the
8 × 400 px `public/images/cert-edge.png` is stretched over the full height of
the card with `background-size: 100% 100%`. It is a PNG rather than drawn cells
because the card is 768 px wide and its height is not fixed, so a row of
coloured cells would not line up with the border.

## 8.6 Regenerating the border image

```bash
php artisan certificate:edge-image
```

The command (`app/Console/Commands/CertificateEdgeImage.php`):

1. Checks that the GD extension is available and aborts with a clear message
   if not.
2. Creates an 8 × 400 transparent true-colour image.
3. Asks `Gradient::stops('#0d9488', '#10b981', '#0d9488', 200)` for 200 colours.
4. Draws each colour as a horizontal band.
5. Writes the PNG to `public/images/cert-edge.png`.

Run it whenever the brand colours of the certificate change.

## 8.7 Downloading the PDF

`app/Http/Responses/FileDownloadResponse.php` builds the response. A plain
`200` response is enough for a browser, but download managers such as IDM,
wget or aria2 probe the file first (usually with `HEAD` or `Range: bytes=0-0`)
to learn its size and to decide whether the transfer can be resumed or split
into segments. Without a `Content-Length` header and byte-range support they
cannot size the file and give up with an error instead of saving it.

The response therefore sets:

| Header | Value |
|---|---|
| `Content-Type` | `application/pdf` |
| `Content-Length` | The exact byte count |
| `Accept-Ranges` | `bytes` |
| `Content-Disposition` | `attachment; filename="…"; filename*=UTF-8''…` |
| `Cache-Control` | `private, no-cache, must-revalidate` |
| `X-Content-Type-Options` | `nosniff` |

### Range handling

| Request | Response |
|---|---|
| No `Range` header | `200` with the whole file |
| `Range: bytes=0-99` | `206` with `Content-Range: bytes 0-99/<size>` and 100 bytes |
| `Range: bytes=99999999-` (beyond the end) | `416` with `Content-Range: bytes */<size>` |
| `Range: bytes=abc` (malformed) | `200` with the whole file |

Suffix ranges (`Range: bytes=-500`) are also supported and return the last
500 bytes.

The `Content-Disposition` header carries both an ASCII-safe fallback name and
the RFC 5987 encoded name, so non-ASCII file names survive.

## 8.8 Caching

The certificate page is sent with

```
Cache-Control: no-store, no-cache, must-revalidate, max-age=0
Pragma: no-cache
```

because it contains the user's name and score.

## 8.9 Changing the certificate

The rule is simple: **change the shared files, never one of the two
renderings.**

| To change… | Edit |
|---|---|
| The wording or the layout | `resources/views/partials/certificate-card.blade.php` |
| The colours, spacing, type sizes | `public/css/certificate.css` |
| The fonts | `public/css/certificate-fonts.css` + `public/fonts/` |
| The brand gradient | `app/Console/Commands/CertificateEdgeImage.php` (then re-run the command) |
| The pass mark | `UserController::CERTIFICATE_THRESHOLD` |
| The file name | `UserController::downloadCertificate()` |
| The certificate id format | `resources/views/partials/certificate-card.blade.php` |
