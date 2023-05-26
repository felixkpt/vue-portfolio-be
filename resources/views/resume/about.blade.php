<div class="mb-4">
    <table class="table table-borderless table-sm m-0">
        <tr>
            @if ($about->featured_image)
                <td class="pr-4">
                    <div style="width:4rem;height: 4rem;border-radius:50%">
                        <img src="{{ $about->featured_image }}" alt=""
                            style="width:4rem;height: 4rem;border-radius:50%">
                    </div>
                </td>
            @endif
            <td class="col-11 p-0" style="vertical-align: middle">
                <h3 class="text-primary my-0">{{ $about->name }}</h3>
            </td>
        </tr>
    </table>
    <h5> <small>{{ $about->slogan }}</small> </h5>
</div>
