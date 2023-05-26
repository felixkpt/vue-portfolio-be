@if (count($contacts) > 0)    
<h5 class="text-primary">Contacts</h5>
<table class="table table-borderless table-sm m-0">
    @foreach ($contacts as $contact)
        <tr>
            <td>
                <table class="table table-borderless table-sm m-0">
                    <tr>
                        <td>
                            <div><strong>{{ $contact->type }}</strong></div>
                            <div>{{ $contact->link }}</div>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                </table>
            </td>
        </tr>
    @endforeach
</table>
@endif
