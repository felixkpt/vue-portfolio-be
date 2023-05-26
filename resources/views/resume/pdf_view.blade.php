<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $about->name }} - Resume</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }
    </style>
    <div style="font-size:12px;margin:0 auto;">
        @include('resume.raw')
    </div>
</body>

</html>

