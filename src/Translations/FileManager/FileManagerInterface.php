<?php


namespace Kodilab\LaravelI18n\Translations\FileManager;


interface FileManagerInterface
{
    public function add(string $original, string $translation);

    public function update(string $original, string $translation);

    public function remove(string $original);

    public function save();



}