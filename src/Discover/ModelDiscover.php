<?php


namespace Kodilab\LaravelI18n\Discover;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kodilab\LaravelI18n\Models\Translation;
use Symfony\Component\Finder\SplFileInfo;

class ModelDiscover
{
    /** @var Filesystem */
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Returns all classes which extends Eloquent Model class and not extends ModelTranslations
     *
     * @param string $path
     * @param string $basePath
     * @param string $namespace
     * @return Collection
     */
    public function discover(string $path, string $basePath, string $namespace = '')
    {
        if (is_null($path)) {
            $path = app_path();
        }

        /** @var SplFileInfo[] $php_files */
        $php_files = $this->getPHPFilesRecursively($path);
        $model_files = $this->getModelClassFiles($php_files, $basePath, $namespace);

        $result = new Collection();

        /** @var SplFileInfo $model */
        foreach ($model_files as $model)
        {
            $result->put(static::classFromFile($model, $basePath, $namespace), $model->getRealPath());
        }

        return $result;
    }

    /**
     * Returns a guessed model translation name from a class name
     *
     * @param string $class
     * @return string
     */
    public function guessModelTranslationClassName(string $class)
    {
        return basename(str_replace('\\', '/', $class)) . 'Translation';
    }

    /**
     * Returns a guessed model translation full namespace class
     *
     * @param string $class
     * @return string
     */
    public function guessModelTranslationClass(string $class)
    {
        return $class . 'Translation';
    }

    /**
     * Returns the PHP files located inside the path
     *
     * @param string $path
     * @return SplFileInfo[]
     */
    private function getPHPFilesRecursively(string $path)
    {
        $files = array_filter($this->filesystem->allFiles($path), function (SplFileInfo $file) {
            return $file->getExtension() === 'php';
        });

        return $files;
    }

    /**
     * @param array $files
     * @param string $basePath
     * @param string $namespace
     * @return array
     */
    private function getModelClassFiles(array $files, string $basePath, string $namespace = '')
    {
        return array_filter($files, function (SplFileInfo $file) use ($basePath, $namespace) {
            try {
                /** @var \ReflectionClass $instance */
                $instance = new \ReflectionClass(static::classFromFile($file, $basePath, $namespace));
                return $instance->isSubclassOf(Model::class);
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param SplFileInfo $file
     * @param $basePath
     * @param string $namespace
     * @return mixed
     */
    protected static function classFromFile(SplFileInfo $file, $basePath, string $namespace = '')
    {
        $class = trim(str_replace($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        $class = $namespace . ucfirst($class);
        return str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst(Str::replaceLast('.php', '', $class)));
    }
}