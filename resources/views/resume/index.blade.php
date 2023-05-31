@extends('resume.layout')
@section('content')
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
@endsection
