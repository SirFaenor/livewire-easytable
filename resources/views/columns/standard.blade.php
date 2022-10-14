<div>
    <span>{!! $content !!}</span>
    @error('value')
        {{$message}}
    @enderror
</div>