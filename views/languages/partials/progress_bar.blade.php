<div class="clearfix">
    <div class="float-left">
        <strong>{{ $language->translations_perc }}%</strong>
    </div>
    <div class="float-right">
        <small class="text-muted">
            {{ count($language->translations) }} of
            {{ count(\Kodilab\LaravelI18n\Language::getBaseLanguage()->translations) }}
        </small>
    </div>
</div>
<div class="progress progress-xs">
    <div class="progress-bar bg-yellow" role="progressbar"
         style="width: {{ $language->translations_perc }}%"
         aria-valuenow="{{ $language->translations_perc }}" aria-valuemin="0" aria-valuemax="100">
    </div>
</div>