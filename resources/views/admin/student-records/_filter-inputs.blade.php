{{-- Carries the current list filters into a group action (used when "all matching" is chosen). --}}
@foreach(request()->only(['search','program','year_level','block','internship','archived_as','batch']) as $key => $value)
    @foreach((array) $value as $v)
        @if($v !== null && $v !== '')
            <input type="hidden" name="{{ $key }}{{ is_array($value) ? '[]' : '' }}" value="{{ $v }}">
        @endif
    @endforeach
@endforeach