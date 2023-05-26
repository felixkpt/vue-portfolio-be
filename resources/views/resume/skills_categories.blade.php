<h5 class="text-primary">Skills</h5>
<table class="table table-borderless table-sm m-0">
    @foreach ($skills_categories as $skills_category)
        <tr>
            <td>
                <table class="table table-borderless table-sm m-0">
                    <tr>
                        <td>
                            <strong>{{ $skills_category->name }}</strong>
                            @include('resume.skills', ['skills_category' => $skills_category])
                        </td>
                    </tr>
                </table>
            </td>
    @endforeach
</table>
