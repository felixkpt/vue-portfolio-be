<h5 class="text-primary">Education</h5>
<table class="table table-borderless table-sm m-0">
    @foreach ($qualifications as $qualification)
        <tr>
            <td>
                <table class="table table-borderless table-sm m-0">
                    <tr>
                        <td>
                            <div><strong>{{ $qualification->course }}</strong></div>
                            <div>{{ $qualification->institution }}</div>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                </table>
            </td>
        </tr>
    @endforeach
</table>
