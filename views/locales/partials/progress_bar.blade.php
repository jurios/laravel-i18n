<div id="progress-bar">
    <div class="clearfix">
        <div class="float-left">
            <strong>{{ $locale->perc }}%</strong>
        </div>
        <div class="float-right">
            <small class="text-muted">
                {{ count($locale->translations) }} of
                {{ count(\Kodilab\LaravelI18n\Models\Locale::getFallbackLocale()->translations) }}
            </small>
        </div>
    </div>
    <div class="progress progress-xs">
        @if($locale->perc === 100)
            @php($color = 'green')
        @elseif($locale->perc > 50)
            @php($color = 'yellow')
        @else
            @php($color = 'red')
        @endif
        <div class="progress-bar bg-{{$color}}" role="progressbar"
             style="width: {{ $locale->perc }}%"
             aria-valuenow="{{ $locale->perc }}" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
</div>