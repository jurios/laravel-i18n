# laravel-i18n

[![Build Status](https://travis-ci.com/jurios/laravel-i18n.svg?branch=master)](https://travis-ci.com/jurios/laravel-i18n)

## Disclaimer
This project is still in a pre-release stage. It should work but could contains unexpected bugs.

Please, consider fill an issue if you see a bug or an unexpected behaviour. That would be really useful
to make `laravel-i18n` better.

## What is this?
**laravel-i18n** is a Laravel built-in localization wrapper which adds some features for improving the localization
system.

### Features

* Translatable texts detection (project files and 3th party exported translations also) and deprecated translations detection
through a [sync process](#sync-process)
* Configure language, currency (WIP) and timezone for each locale
* Optional web editor to manage locales and translations

### Installation

First, include the package to your project:

```
composer require kodilab/laravel-i18n dev-master
``` 

Once you have `laravel-i18n`, you must publish the config files with:

```
php artisan vendor:publish --provider="Kodilab\LaravelI18n\I18nProvider"
```

You can check the `laravel-i18n` configuration in the `config/i18n.php` configuration file.

#### Migrations
`laravel-i18n` needs to create a new table in your database where it will persist the languages. This table is called
`i18n_locales` by default. You can change the table name in the `config/i18n.php` file (this change must be done before
applying the migration).

In order to create the table, just apply the migrations:

```
php artisan migrate
```

### Install

The install command will create the fallback locale based on the `fallback_locale` parameter 
defined on Laravel configuration in the `config/app.php` file.

The fallback locale in `laravel-i18n` has the same meaning as the `fallback_locale` for the Laravel localization system:
Is the language which will be used when a translation is not defined for a given language.
As `laravel-i18n` will consider the texts found in the project written in `fallback_locale`, is
a good practice define in your `config/app.php` the same `fallback_locale` parameter as your language used in your codebase.

Once you have the desired `fallback_locale` in your `config/app.php`, let's install the `laravel-i18n` package:

```
php artisan make:i18n
```

This will add a new locale in the `i18n_locale` configured as `fallback` based on the `fallback_locale` parameter in
your `config/app.php` file.

#### Sync process 

Once we have a `fallback_locale` (at least, one `locale` must be defined and must be defined as `fallback`) in our 
`i18n_locales` table, we can start the sync process.

The idea behind `sync` is keep updated all `lang/{locale}.json` files (one for each locale). The `sync` process will 
detect the translatable texts from our codebase (calls to `__()` function) & all 3th-party translations exported in 
`lang/{locale}/*.php` files and add them into the `lang/{locale}.json` file. 
What's more will detect the deprecated translations already defined in the `lang/{locale}.json` files and will remove 
them. 

As explained before, `laravel-i18n` considers `fallback_locale` as the language used in our codebase. For that reason,
all new translatable texts detected during the sync process will be translated with the same text in 
the `lang/{fallback_locale}.json` file.

You can start a sync process with the command: 

```
php artisan i18n:sync
```

This process should fired as frequently as possible. It's recommended execute this process every deployment.

##### Sync detailed

What the sync process does is:

1. Look for new texts exported into the `lang/{locale}/*.php`
2. Look for the calls to the translation function (by default, `__()`) in the project codebase
3. Add the results into the JSON files with an empty (`null`) translation except in the `fallback_locale` JSON file
where the translation is the same as the original text.
3. Remove deprecated transalations which are not present neither in codebase or exported translations.

#### Set the locale for each request
You should follow the [Laravel localization instructions](https://laravel.com/docs/5.8/localization#configuring-the-locale)

`laravel-i18n` provides an extensible `middleware` which helps you to set the locale and timezone easily. Just create a
[middleware](https://laravel.com/docs/5.8/middleware) which extends `\Kodilab\LaravelI18n\Middleware\SetLocale` and define
the `getLocale()` function which must return a `Locale` instance

For example, in this case we are going to get the locale from the User model (we must create a relationship between locales and users first):

```
class Example extends \Kodilab\LaravelI18n\Middleware\SetLocale
{
    protected function getLocale()
    {
        //You can access the request using $this->request
        
        return Auth::user()->locale;
    }
}
```

For each request, it will load the locale translations, timezone and currency configuration of the locale returned.

### Editor
You can, optionally, install the editor. The editor is a collection of templates and controllers which will be exported
to your project in order to modify them as you want. By default, the templates uses the assets provided by Laravel.
 
Modify the template as you want in order to integrate the editor into your project.

You can install the editor with:

```
php artisan i18n:editor
```

This will make some changes in your project:

1. The web editor controllers will be added in `app\Http\Controllers\I18n`
2. The web editor templates will be added in `resources/vendor/i18n`
3. A new entry in your `routes/web.php` will be added with all the routes needed by the editor. You can put the call to
the routes wherever you want in your `routes/web.php`.

These are the default routes created by `php artisan i18n:editor`:

| Method | Path                                      | Name                             | Controller
| -------| ----------------------------------------- | -------------------------------- | -------------------------------------------------------
| GET    | i18n                                      | i18n.dashboard                   | App\Http\Controllers\I18n\DashboardController@dashboard
| GET    | i18n/locales                              | i18n.locales.index               | App\Http\Controllers\I18n\LocaleController@index
| POST   | i18n/locales                              | i18n.locales.store               | App\Http\Controllers\I18n\LocaleController@store
| GET    | i18n/locales/create                       | i18n.locales.create              | App\Http\Controllers\I18n\LocaleController@create
| DELETE | i18n/locales/{locale}                     | i18n.locales.destroy             | App\Http\Controllers\I18n\LocaleController@destroy
| PATCH  | i18n/locales/{locale}                     | i18n.locales.update              | App\Http\Controllers\I18n\LocaleController@update
| GET    | i18n/locales/{locale}                     | i18n.locales.show                | App\Http\Controllers\I18n\LocaleController@show
| GET    | i18n/locales/{locale}/edit                | i18n.locales.edit                | App\Http\Controllers\I18n\LocaleController@edit
| GET    | i18n/locales/{locale}/translations        | i18n.locales.translations.index  | App\Http\Controllers\I18n\TranslationController@index
| PATCH  | i18n/locales/{locale}/translations/update | i18n.locales.translations.update | App\Http\Controllers\I18n\TranslationController@update

Fel free to modify all you need in order to full integrate the editor to your needs.