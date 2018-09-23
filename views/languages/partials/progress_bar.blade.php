<div id="progress-bar">
    <div class="clearfix">
        <div class="float-left">
            <strong>{{ $language->perc }}%</strong>
        </div>
        <div class="float-right">
            <small class="text-muted">
                {{ count($language->translations) }} of
                {{ count(\Kodilab\LaravelI18n\Language::getFallbackLanguage()->translations) }}
            </small>
        </div>
    </div>
    <div class="progress progress-xs">
        @if($language->perc === 100)
            @php($color = 'green')
        @elseif($language->perc > 50)
            @php($color = 'yellow')
        @else
            @php($color = 'red')
        @endif
        <div class="progress-bar bg-{{$color}}" role="progressbar"
             style="width: {{ $language->perc }}%"
             aria-valuenow="{{ $language->perc }}" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
</div>

@push('inline-js')
    <script>
        require(['jquery'], function (jquery) {
            var $ = jquery;

            $(document).on('language-updated', function(e, data) {
                $('#progress-bar').replaceWith(data);
            });
        });
    </script>
@endpush