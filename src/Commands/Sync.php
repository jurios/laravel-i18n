<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Linguist;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\TranslationsManager;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize laravel translations found in php files with fallback language translations on the database';

    /** @var Linguist */
    protected $linguist;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Linguist $linguist)
    {
        parent::__construct();
        $this->linguist = $linguist;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public function handle()
    {
        $texts = $this->linguist->texts();

        $locales = Locale::all();

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            $manager = new TranslationsManager($locale);

            $manager->sync(array_keys($texts));
        }
    }
}
