<?php


namespace Kodilab\LaravelI18n\Commands\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Config extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:config
                            {--force : Replace the config file if it exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new i18n config file';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

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

        $this->to = config_path('i18n.php');
        $this->from = __DIR__.'/../../../config/config.php';

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
        $this->output->title('Generating i18n config file');

        if (!$this->isAlreadyPublished()) {
            $this->publishConfiguration();
            return;
        }
        if ($this->isAlreadyPublished() && $this->option('force')) {
            $this->publishConfiguration();
            return;
        }
        if ($this->confirm("The i18n configuration file already exists. Do you want to replace it?")) {
            $this->publishConfiguration();
            return;
        }
    }

    private function publishConfiguration()
    {
        $this->filesystem->copy($this->from, $this->to);
        $this->output->success($this->to . ' published.');
    }

    private function isAlreadyPublished()
    {
        return $this->filesystem->exists($this->to);
    }

}