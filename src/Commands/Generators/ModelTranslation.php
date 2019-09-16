<?php


namespace Kodilab\LaravelI18n\Commands\Generators;


use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kodilab\LaravelI18n\Discover\ModelDiscover;

class ModelTranslation extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:modeltranslation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new model translation';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /** @var ModelDiscover */
    protected $discover;

    public function __construct()
    {
        parent::__construct();

        $this->discover = new ModelDiscover();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     */
    public function handle()
    {
        $this->output->title('Generating a model translation');

        $files = $this->discover->discover(app_path(), base_path());

        $class = $this->choice(
            'Please, choose the model which you would like to add translation?', array_keys($files->toArray())
        );

        $this->generateModelTranslation($class, dirname($files[$class]));

    }

    private function generateModelTranslation(string $class, string $path)
    {
        $this->generateModelTranslationMigrationFor($class, $path);
    }

    private function generateModelTranslationMigrationFor(string $class, string $path)
    {
        try {

            /** @var \ReflectionClass $reflection */
            $reflection = new \ReflectionClass($class);

            /** @var Model $instance */
            $instance = new $class();

            $translation_class_name = $this->discover->guessModelTranslationClassName($class);
            $translation_table_name = Str::snake(Str::pluralStudly($translation_class_name));
            $migration_file_name = 'create_' . $translation_table_name .'_table';
            $migration_class_name = ucfirst(Str::camel($migration_file_name));

            $replacements = [
                '{{class}}'                 => $migration_class_name,
                '{{translation_table}}'     => $translation_table_name,
                '{{table}}'                 => $instance->getTable()
            ];

            $content = file_get_contents(__DIR__ . '/stubs/ModelTranslation/migration.php.stub');

            foreach ($replacements as $replacement => $value) {
                $content = str_replace($replacement, $value, $content);
            }

            file_put_contents(
                database_path('migrations/'.date('Y_m_d_His', time()). '_' . $migration_file_name . '.php'),
                $content
            );

            //Sleep for 1 second in order to avoid the chance to create multiple migration files with the same timestamp
            sleep(1);

            $this->output->success(
                database_path('migrations/'.date('Y_m_d_His', time()). '_' . $migration_file_name . '.php generated')
            );

        } catch (\Exception $exception) {
            return;
        }
    }
}