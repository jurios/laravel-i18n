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
    | model_translations_suffix: For your model translations, Laravel-i18n will create a table for each translatable model.
    | The name of this table will be the model name + model_translations_suffix.
    | For example, if the model "car" (which table name will be "cars") is a translatable model, then "cars_i18n' will be created.
    |
    | Here you can edit the table's name
    */
    'tables' => [

        'languages' => 'i18n_languages',
        'texts' => 'i18n_texts',
        'translations' => 'i18n_translations',
        'locales' => 'i18n_locales',

        'model_translations_suffix' => '_i18n',
    ],


    /*
    |--------------------------------------------------------------------------
    | session_var_name
    |--------------------------------------------------------------------------
    |
    | Laravel-i18n looks for the user locale in the session thus you have to provide here the 'key' where the user locale
    | is placed. For example, if 'session_var_name' = 'locale', Laravel-i18n will look for the key 'locale' in the session.
    | If the session value is an enabled ISO_639_1 language, all translatable text will be translated to this language.
    */
    'session_var_name' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | logout_route
    |--------------------------------------------------------------------------
    |
    | Laravel-i18n can be integrated in your project as much as it can. If you are using an authentication system, you
    | can provide a logout name route (ex. 'logout' would be the default route in Laravel authentication system)
    | in order to show a logout button (This button is just a link to this url) in the views. The user can
    | logout directly in laravel-i18n views instead of going back to the project's views.
    |
    | If your logout route doesn't work with GET, you can provide a method (GET, POST, PATCH, DELETE)
    */
    'logout_route' => [
        'name' => null,
        'method' => null
    ],

    /*
    |--------------------------------------------------------------------------
    | show_credits
    |--------------------------------------------------------------------------
    |
    | Show credits in the footer of laravel-i18n views
    */
    'show_credits' => true,

    /*
    |--------------------------------------------------------------------------
    | show_translation_info
    |--------------------------------------------------------------------------
    |
    | In the translation editor, show the information button next to a translation allowing to load a modal with
    | further information about the translation (like the templates files which has that text)
    |
    */
    'show_translation_info' => false
];