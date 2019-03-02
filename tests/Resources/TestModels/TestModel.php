<?php

namespace Kodilab\LaravelI18n\Tests\Resources\TestModels;

use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelI18n\Traits\Translatable;

class TestModel extends Model
{
    use Translatable;

    protected $table = 'test_models';
}