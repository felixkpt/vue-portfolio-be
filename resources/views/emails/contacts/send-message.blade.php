@component('mail::message')
    <div class="container">
        From {{ $data['name'] }},
        <br>
        <h3>{{ $data['subject'] }}</h3>
        <br>
        {!! $data['message'] !!}
    </div>
@endcomponent
