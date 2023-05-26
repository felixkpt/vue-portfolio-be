    <div class="row">
        <div class="col">
            @foreach ($skills_category->skills as $key => $skill)
                {{ $skill->name }} @if (isset($skills_category->skills[$key+1])), @endif 
            @endforeach
        </div>
    </div>
