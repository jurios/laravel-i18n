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
                                {--views : Only scaffold the authentication views}
                                {--force : Overwrite existing views by default}';

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
        'locales/partials/table.stub' =>        self::BASE_VIEWS_PATH . '/editor/locales/partials/table.blade.php',
        'locales/partials/progress_bar.stub' => self::BASE_VIEWS_PATH . '/editor/locales/partials/progress_bar.blade.php',
        'locales/index.stub' =>                 self::BASE_VIEWS_PATH . '/editor/locales/index.blade.php',
        'locales/form.stub' =>                  self::BASE_VIEWS_PATH . '/editor/locales/form.blade.php',
        'locales/show.stub' =>                  self::BASE_VIEWS_PATH . '/editor/locales/show.blade.php',
        'dashboard/dashboard.stub' =>           self::BASE_VIEWS_PATH . '/editor/dashboard/dashboard.blade.php',
    ];

    protected $controllers = [
        'DashboardController.php' => 'DashboardController.php',
        'LocaleController.php' => 'LocaleController.php',
        'I18nController.php' => 'I18nController.php',
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

        $this->exportViews();

        if (! $this->option('views')) {

            foreach ($this->controllers as $stub => $final) {
                file_put_contents(
                    app_path('Http/Controllers/I18n/'. $final),
                    $this->compileControllerStub($stub)
                );
            }

            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__.'/stubs/editor/routes/web.stub'),
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
        $this->copyLaravelLayout();

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
     * Copy the layout from laravel framework
     */
    private function copyLaravelLayout()
    {
        if (!file_exists($path = resource_path('views/layouts/app.blade.php'))) {
            copy(
                base_path('vendor/laravel/framework/src/Illuminate/Auth/Console/stubs/make/views/layouts/app.stub'),
                $path
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