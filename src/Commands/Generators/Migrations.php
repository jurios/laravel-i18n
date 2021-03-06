<?php


namespace Kodilab\LaravelI18n\Commands\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class Migrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:migrations
                            {--filename= : Name of the generated migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates i18n migration files';

    /**
     * Path where the config is placed once its generated
     *
     * @var string
     */
    protected $to;

    /**
     * Path where the config sample is placed
     *
     * @var string
     */
    protected $from;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Index in order to create migrations with different timestamps
     * @var int
     */
    protected $index = 0;

    public function __construct()
    {
        parent::__construct();

        $this->to = database_path('migrations');
        $this->from = __DIR__.'/stubs/Migrations';

        $this->filesystem = app(Filesystem::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     */
    public function handle()
    {
        $this->output->title('Generating i18n migration files');

        $this->publishMigrations();

        $this->output->success('Migration files generated');
    }

    private function publishMigrations()
    {
        /** @var SplFileInfo $migration */
        foreach ($this->filesystem->files($this->from) as $index => $migration) {
            $filename = $migration->getFilename();

            if (preg_match('/\.php\.stub$/', $filename)) {

                $name = preg_replace('/\.stub$/', '', $migration->getFilename());
                $name = preg_replace('/^[0-9]{4}_[0-9]{2}_[0-9]{2}_[0-9]{6}_/', '', $name);

                $this->filesystem->copy(
                    $migration->getRealPath(),
                    $this->to . '/' . $this->generateFilename($name)
                );
            }
        }
    }

    private function generateFilename(string $name)
    {
        if (!is_null($filename = $this->option('filename'))) {
            return $filename;
        }

        $filename = date('Y_m_d_His', time() + $this->index). '_' . $name;
        $this->index++;

        return $filename;
    }
}