<?php


namespace Kodilab\LaravelI18n\Tests\Traits;


use Symfony\Component\Finder\SplFileInfo;

trait LaravelOperations
{
    /**
     * Removes generated translations from the Laravel instance
     */
    protected function removePublishedTranslations()
    {
        /** @var SplFileInfo $file */
        foreach ($this->filesystem->files(resource_path('lang')) as $file) {
            if ($file->getExtension() === 'json') {
                $this->filesystem->delete($file->getRealPath());
            }
        }
    }

    /**
     * Removes generated migrations from the Laravel instance
     */
    protected function removePublishedMigrations()
    {
        $files = $this->filesystem->files(__DIR__ . '/../../vendor/orchestra/testbench-core/laravel/database/migrations');
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $this->filesystem->delete($file->getRealPath());
            }
        }
    }

    /**
     * Removes generated factories from the Laravel instance
     */
    protected function removePublishedFactories()
    {
        foreach ($this->filesystem->files(database_path('factories')) as $file) {
            if ($file->getExtension() === 'php') {
                $this->filesystem->delete($file->getRealPath());
            }
        }
    }
}