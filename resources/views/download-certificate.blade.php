<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement — QuizSphere</title>
    <style>
        @page {
            size: 297mm 210mm;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        body {
            padding: 8mm 0 0 0;
            font-family: 'Inter', Helvetica, Arial, sans-serif;
        }

        {!! $certificateCss !!}
    </style>
</head>
<body>
    @include('partials.certificate-card', ['data' => $data, 'assetBase' => ''])
</body>
</html>
