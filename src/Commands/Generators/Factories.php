<?php


namespace Kodilab\LaravelI18n\Commands\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class Factories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:factories
                            {--force : Replace the config file if it exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates i18n factory files';

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

    public function __construct()
    {
        parent::__construct();

        $this->to = database_path('factories');
        $this->from = __DIR__.'/../../../database/factories';

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
        $this->output->title('Generating i18n factory files');

        $this->publishFactories();
    }

    private function publishFactories()
    {
        /** @var SplFileInfo $factory */
        foreach ($this->filesystem->files($this->from) as $factory) {
            $this->filesystem->copy($factory->getRealPath(), $this->to . '/' . $factory->getFilename());
        }
    }
}