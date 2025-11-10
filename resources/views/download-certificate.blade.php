<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 font-serif">

    <div class="flex justify-center items-center min-h-screen px-4 py-10">
        <div class="bg-white border-[10px] border-teal-600 rounded-2xl shadow-2xl w-full max-w-3xl p-10 relative overflow-hidden">

            <!-- Watermark / Background Pattern -->
            <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>

            <!-- Header -->
            <div class="text-center relative z-10">
                <h1 class="text-4xl font-extrabold text-teal-700 uppercase tracking-wider">Certificate of Achievement</h1>
                <p class="mt-2 text-gray-500 italic">This certifies that</p>
            </div>

            <!-- User Name -->
            <div class="text-center mt-8 relative z-10">
                <h2 class="text-3xl font-bold text-gray-800 underline decoration-teal-400 underline-offset-8">
                    {{$data['name']}}
                </h2>
                <p class="mt-4 text-gray-600">has successfully completed the quiz</p>
            </div>

            <!-- Quiz Title -->
            <div class="text-center mt-6 relative z-10">
                <h3 class="text-3xl font-semibold text-teal-600 italic drop-shadow-sm">
                    “{{$data['quiz']}}”
                </h3>
            </div>

            <!-- Motivational Line -->
            <div class="text-center mt-6 relative z-10">
                <p class="mt-4 text-gray-600">“Learning is the key to success, and success begins with achievement.”</p>
            </div>

            <!-- Certificate Details -->
            <div class="text-center mt-8 relative z-10">
                <p class="text-gray-500">Certificate ID: 
                    <span class="font-medium text-gray-700">#QZ-2025-00123</span>
                </p>
            </div>

            <!-- Signature Section -->
            <div class="flex justify-between items-center mt-12 px-10 relative z-10">
                <div class="text-center">
                    <p class="signature">Nisha</p>
                    <div class="h-px w-40 bg-gray-400 mx-auto mb-2"></div>
                    <p class="font-semibold text-gray-700">Admin Signature</p>
                </div>
                <div class="text-center">
                    <p class="date-style">{{ date('F d, Y') }}</p>
                    <div class="h-px w-40 bg-gray-400 mx-auto mb-2"></div>
                    <p class="font-semibold text-gray-700">Date</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
