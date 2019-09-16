<?php


namespace Kodilab\LaravelI18n\Commands;


use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Models\Locale;

class Generator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:generate {resource : Element to be generated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a i18n resource';

    protected $aliases = [
        'translatable' => 'modeltranslation'
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public function handle()
    {
        $options = [];

        foreach ($this->options() as $option => $value) {
            $options['--' . $option] = $value;
        }

        $resource = $this->argument('resource');

        if (isset($this->aliases[$resource])) {
            $resource = $this->aliases[$resource];
        }

        $this->call('i18n:' . $resource, $options);
    }
}