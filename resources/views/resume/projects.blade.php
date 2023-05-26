<h5 class="text-primary">Projects</h5>
<table class="table table-borderless table-sm m-0">
    @foreach ($projects as $project)
        <tr>
            <td>
                <table class="table table-borderless table-sm m-0">
                    <tr>
                        <td><strong><a class="link-unstyled" href="{{ URL::to($project->project_url) }}">{{ $project->title }}</a></strong>
                            <small style="font-weight: lighter" class="fa fa-circle"></small>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {!! $project->content_short !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endforeach
</table>
