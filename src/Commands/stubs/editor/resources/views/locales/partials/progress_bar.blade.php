@if($locale->perc === 100)
    @php($color = 'bg-success')
@elseif($locale->perc > 50)
    @php($color = 'bg-warning')
@else
    @php($color = 'bg-danger')
@endif

@php($title = sprintf('%d of %d translations', count($locale->translations),
    count(\Kodilab\LaravelI18n\Models\Locale::getFallbackLocale()->translations)))

<div class="progress" title="{{ $title }}">
    <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $locale->perc }}%;" aria-valuenow="{{ $locale->perc }}"
         aria-valuemin="0" aria-valuemax="100">
        {{ $locale->perc }}% - {{ $title }}
    </div>
</div>