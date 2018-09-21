<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;
use Kodilab\LaravelI18n\Linguist;

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
    protected $description = 'Syncronize laravel translations found in php files with base language translations on the database';

    /** @var Linguist $linguist */
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
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLanguageException
     */
    public function handle()
    {
        $translationsByFile = $this->linguist->getAllTranslatableStringFromFiles();

        $translations = $this->linguist->getTranslationsWithMd5($translationsByFile);

        $deprecated_count = $this->linguist->deleteDeprecatedTranslations($translations);

        $this->output->writeln("\"<fg=red>{$deprecated_count}</>\" deprecated texts were deleted.");

        $dynamic_count = $this->linguist->countDynamicTranslations($translations);

        $this->output->writeln("\"<fg=yellow>{$dynamic_count}</>\" texts are dynamic and can't be added.");

        $added_count = $this->linguist->addNewTranslations($translations);

        $this->output->writeln("\"<fg=green>{$added_count}</>\" new texts were added.");

        $this->call('cache:clear');
        Session::flush();

    }
}
