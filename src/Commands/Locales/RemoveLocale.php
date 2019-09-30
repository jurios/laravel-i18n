<?php


namespace Kodilab\LaravelI18n\Commands\Locales;


use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Models\Locale;

class RemoveLocale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locale:remove
                            {reference : Locale reference to be removed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a locale';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->output->title('Removing an i18n locale');

        $locale = Locale::getLocale($this->argument('reference'));

        if (is_null($locale)) {
            $this->output->error("Locale {$this->argument('reference')} does not exists");
            return;
        }

        try {
            $locale->delete();
            $this->output->success("Locale {$this->argument('reference')} removed successfully");
        } catch (\Exception $exception) {
            $this->output->error("Error: " . $exception->getMessage());
        }
    }
}