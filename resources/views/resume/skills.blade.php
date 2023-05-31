    <div class="d-flex flex-wrap p-0">
        <div class="col p-0">
            @foreach ($skills_category->skills as $key => $skill)
                {{ $skill->name }}@if (isset($skills_category->skills[$key+1])), @endif 
            @endforeach
        </div>
    </div>
