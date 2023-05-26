@component('mail::message')
    <div class="container">
        <h2>Hi {{ $data['name'] }},</h2><br>
        <p>Thank You for your message</p> <br>
        <p>I will contact you soon.</p> <br>
    </div>
@endcomponent
