<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Models\Locale;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:i18n';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install laravel-i18n package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
        //
    }
}
