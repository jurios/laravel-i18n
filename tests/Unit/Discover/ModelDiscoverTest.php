<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Discover;


use Kodilab\LaravelI18n\Discover\ModelDiscover;
use Kodilab\LaravelI18n\Tests\Unit\Discover\Fixtures\Models\ModelOne;
use Kodilab\LaravelI18n\Tests\Unit\Discover\Fixtures\Models\ModelTwo;
use Kodilab\LaravelI18n\Tests\TestCase;

class ModelDiscoverTest extends TestCase
{
    /** @var string  */
    protected $fixtures_path = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures';

    /** @var ModelDiscover */
    protected $modelDiscover;

    /** @var string */
    protected $basePath;

    /** @var string */
    protected $namespace;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->modelDiscover = new ModelDiscover();

        $this->basePath = dirname(dirname(dirname(__DIR__)));
        $this->namespace = 'Kodilab/LaravelI18n/';

    }

    public function test_discover_should_returns_an_array_of_models_and_its_path()
    {
        $models = $this->modelDiscover->discover($this->fixtures_path, $this->basePath, $this->namespace);

        $this->assertEquals([
            ModelOne::class => $this->fixtures_path . '/Models/ModelOne.php',
            ModelTwo::class => $this->fixtures_path . '/Models/ModelTwo.php'
        ], $models->toArray());
    }

    public function test_guessModelTranslationClass_should_return_full_namespace_class()
    {
        $this->assertEquals(
            ModelOne::class . 'Translation',
            $this->modelDiscover->guessModelTranslationClass(ModelOne::class)
        );
    }

    public function test_guessModelTranslationClassName_should_return_the_class_name()
    {
        $this->assertEquals(
            'ModelOne' . 'Translation',
            $this->modelDiscover->guessModelTranslationClassName(ModelOne::class)
        );
    }
}