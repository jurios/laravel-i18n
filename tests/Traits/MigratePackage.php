<?php


namespace Kodilab\LaravelI18n\Tests\Traits;

use Symfony\Component\Finder\SplFileInfo;

trait MigratePackage
{
    /**
     * Install the package
     *
     */
    public function migratePackageSetUp()
    {
        $this->withFactories(__DIR__ . '/../../database/factories');

        //Only publish package migrations in case other test hasn't done it yet.
        if (!$this->areMigrationsPublished()) {
            $this->artisan('i18n:migrations');
        }

        /*
         * Change the fallback locale to a random one.
         * The fallback locale generated during the migration won't be 'en' and the future sync process calls
         * will be faster
         */
        $this->app['config']->set('app.fallback_locale', 'app');

        $this->loadLaravelMigrations();
        $this->artisan('migrate');
    }

    /**
     * Detects whether other test has published the migrations before.
     *
     * @return bool
     */
    private function areMigrationsPublished()
    {
        $exists = false;

        $stubs = array_filter(
            $this->filesystem->files(__DIR__ . '/../../src/Commands/Generators/stubs/Migrations'),
            function (SplFileInfo $file) {
                return $file->getExtension() === 'stub';
            }
        );

        $migrations = array_filter(
            $this->filesystem->files(database_path('migrations')), function (SplFileInfo $file) {
                return $file->getExtension() === 'php';
            }
        );

        $migrations = array_map(function (SplFileInfo $item) {
            return preg_replace('/^[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}/', '', $item->getFilename());
        }, $migrations);

        $stubs = array_map(function (SplFileInfo $item) {
            return preg_replace('/\.stub$/', '', preg_replace(
                    '/^[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}/', '', $item->getFilename())
            );
        }, $stubs);

        foreach ($stubs as $stub) {
            if (in_array($stub, $migrations)) {
                $exists = true;
            }
        }

        return $exists;
    }
}