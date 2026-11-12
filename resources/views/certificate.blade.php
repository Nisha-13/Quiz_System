<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement — QuizSphere</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite('resources/css/app.css')
    <link href="{{ asset('css/certificate-fonts.css') }}?v={{ filemtime(public_path('css/certificate-fonts.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/certificate.css') }}?v={{ filemtime(public_path('css/certificate.css')) }}" rel="stylesheet">
</head>
<body class="bg-slate-200 min-h-screen">

    @if(session('message-success'))
    <div class="max-w-4xl mx-auto pt-5 px-4">
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium">
            <i class="bi bi-check-circle-fill text-emerald-500"></i>
            <span>{{ session('message-success') }}</span>
        </div>
    </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 pt-5 flex flex-wrap gap-3 items-center justify-between">
        <a href="/user-details"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-full shadow-sm hover:border-teal-400 hover:text-teal-700 transition text-sm">
            <i class="bi bi-arrow-left"></i> My Attempts
        </a>
        <a href="/download-certificate/{{ $data['id'] }}/certificate.pdf"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold px-5 py-2.5 rounded-full shadow-md shadow-teal-600/25 hover:shadow-teal-600/45 hover:-translate-y-0.5 transition text-sm">
            <i class="bi bi-download"></i> Download PDF
        </a>
    </div>

    <div class="flex justify-center items-center px-4 py-8">
        @include('partials.certificate-card', ['data' => $data, 'assetBase' => asset('')])
    </div>
</body>
</html>
