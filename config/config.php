<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    |
    | Laravel-i18n needs create 3 tables:
    | Language table: Table used to persist all languages that Laravel-i18n supports
    | Text: Table used to persis every string "extracted" from your views/source that uses Laravel-i18n translation system
    | Translation: Information about every translation
    |
    | Here you can edit the table's name
    */
    'tables' => [
        'languages' => 'i18n_languages',
        'texts' => 'i18n_texts',
        'translations' => 'i18n_translations'
    ],

    /*
    |--------------------------------------------------------------------------
    | session_var_name
    |--------------------------------------------------------------------------
    |
    | When a text has to be translated, by default Laravel-i18n will try to translate
    | to the Language defined in this session var.
    */
    'session_var_name' => 'locale'


];