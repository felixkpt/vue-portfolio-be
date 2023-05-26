<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $about->name }} - Resume</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-end my-4">
            <form action="{{ URL::to('api/client/resume/download') }}" method="post">
                <button class="btn btn-primary">Export to PDF</button>
            </form>
        </div>
    </div>

    <div style="font-size: normal;margin:auto;">
        @include('resume.raw')
    </div>
</body>

</html>
