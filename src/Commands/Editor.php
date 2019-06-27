<?php


namespace Kodilab\LaravelI18n\Commands;


use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class Editor extends Command
{
    use DetectsApplicationNamespace;

    const BASE_VIEWS_PATH = 'vendor/i18n';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:editor
                                {--views : Only scaffold the editor views}
                                {--controllers : Only scaffold the controllers views}
                                {--force : Overwrite existing items by default}
                                {--reinstall : Only scaffold the controllers views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install laravel-i18n editor';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'locales/partials/table.blade.php' =>                        self::BASE_VIEWS_PATH . '/editor/locales/partials/table.blade.php',
        'locales/partials/progress_bar.blade.php' =>                 self::BASE_VIEWS_PATH . '/editor/locales/partials/progress_bar.blade.php',
        'locales/index.blade.php' =>                                 self::BASE_VIEWS_PATH . '/editor/locales/index.blade.php',
        'locales/form.blade.php' =>                                  self::BASE_VIEWS_PATH . '/editor/locales/form.blade.php',
        'locales/show.blade.php' =>                                  self::BASE_VIEWS_PATH . '/editor/locales/show.blade.php',
        'dashboard/dashboard.blade.php' =>                           self::BASE_VIEWS_PATH . '/editor/dashboard/dashboard.blade.php',
        'locales/translations/index.blade.php' =>                    self::BASE_VIEWS_PATH . '/editor/locales/translations/index.blade.php',
        'locales/translations/partials/table/table.blade.php' =>     self::BASE_VIEWS_PATH . '/editor/locales/translations/partials/table/table.blade.php',
    ];

    protected $controllers = [
        'DashboardController.php' => 'DashboardController.php',
        'LocaleController.php' => 'LocaleController.php',
        'I18nController.php' => 'I18nController.php',
        'TranslationController.php' => 'TranslationController.php'
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     */
    public function handle()
    {
        $this->createDirectories();

        if ($this->option('reinstall')) {
            $this->input->setOption('force', true);
            $this->input->setOption('controllers', false);
            $this->input->setOption('views', false);
        }

        if (! $this->option('controllers')) {
            $this->exportViews();
        }

        if (! $this->option('views')) {

            foreach ($this->controllers as $stub => $final) {
                file_put_contents(
                    app_path('Http/Controllers/I18n/'. $final),
                    $this->compileControllerStub($stub)
                );
            }

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__.'/stubs/editor/routes/web.php'),
                FILE_APPEND
            );
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = $this->getViewPath($value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__.'/stubs/editor/resources/views/'.$key,
                $view
            );
        }
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor/locales'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor/dashboard'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor/locales/partials'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor/locales/translations'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor/locales/translations/partials'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath(self::BASE_VIEWS_PATH . '/editor/locales/translations/partials/table'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = app_path('Http/Controllers/i18n'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Get full view path relative to the app's configured view path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getViewPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'), $path,
        ]);
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub(string $stub)
    {
        return str_replace(
            '{{namespace}}',
            $this->getAppNamespace(),
            file_get_contents(__DIR__.'/stubs/editor/Controllers/' . $stub)
        );
    }

}