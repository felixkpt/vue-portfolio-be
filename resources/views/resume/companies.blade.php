<h5 class="text-primary">Experience</h5>
<table class="table table-borderless table-sm m-0">
    @foreach ($companies as $company)
        <tr>
            <td>
                <table class="table table-borderless table-sm m-0">
                    <tr>
                        <td><strong><a class="link-unstyled" href="{{ URL::to($company->url) }}">{{ $company->name }}</a></strong>
                            <small style="font-weight: bolder;margin:auto;font-size:22px;line-height:0">.</small>
                            <span>{{ $company->position }}</span>
                            <small class="text-black-50">{{ \Carbon\Carbon::parse($company->start_date)->format('M Y') }}
                                —
                                {{ $company->end_date ? \Carbon\Carbon::parse($company->end_date)->format('M Y') : 'Present' }}
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {!! $company->roles !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endforeach
</table>
