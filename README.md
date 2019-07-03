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
* Configure language, currency and timezone for each locale
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

### Create new Locales
In order to create new locales, `laravel-i18n` provides some tools to make locale and remove them easily:

```
\Kodilab\LaravelI18n\Facade::createLocale(array $data = []);
\Kodilab\LaravelI18n\Facade::removeLocale(string $reference);
``` 

##### Facade::createLocale(array data = [])
Here is the schema of `$data`: 

```
[
        'iso' => ISO language code like 'ca' or 'en',
        'region' => Country code like 'GB' or 'DE',
        'description' => Brief description,
        'laravel_locale' => Laravel locale, usually the same as iso (you can leave it blank),
        'currency_number_decimals' => Number of decimals when render currency (2),
        'currency_decimals_punctuation' => Punctuation for decimals ("."),
        'currency_thousands_separator' => Thousands separator (","),
        'currency_symbol' => Currency symbol ("€" or "$", for example),
        'currency_symbol_position' => 'after|before',
        'carbon_locale' => "Carbon locale, usually the same as iso (you can leave it blank),
        'tz' => Timezone ("Europe/Madrid", for example),
        'enabled' => true|false,
        'fallback' => false
    ]
```

##### Facade::removeLocale(string $reference);
The `Locale's reference` is a concatenation of `iso` and `region` in a particular format.
For a `Locale` which `iso = en` and `region = GB`, for example, the reference is `en_GB`.
A `iso` is always in lowercase and `region` in uppercase.

If a `Locale` does not have a region, then `reference = iso`.

So, for removing the `Locale` which `iso="en"` and `region="GB"`, removeLocale should be called like this:

```
Facade::removeLocale('en_GB');
```

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
the `getLocale()` function which must return a `Locale` instance which will be used by `laravel-i18n` for the translations. 

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

Apart from `getLocale()`, you can override `setLocale(Locale $locale)` and `setTimezone(Locale $locale)`. These methods
are responsible of how the locale and the timezone are set into the configuration:

`setLocale(Locale $locale)` where `$locale` is the locale returned by `getLocale()` sets the locale value (`iso` field)
into the configuration using the `app.locale` parameter (which is used for the translations). 

`setTimezone(Locale $locale)` where `$locale` is the locale returned by `getLocale()` calls to 
`date_default_timezone_set` using the `timezone` defined in the locale (`tz` field). This timezone will be used 
for time values. 

If you want to modify how this values are set, just override the function you want.

### Currency
`laravel-i18n` provides a helper function in order to show currency values localized:
Each locale has its own currency format configuration where you can define currency symbol, currency symbol position,
number of decimals, decimals separator and thousands separator.

Then, when you want to display a currency value just use the helper:

```
currency(float $value, bool $show_symbol = true, \Kodilab\LaravelI18n\Models\Locale $locale = null)
```

* **value**: The value to be displayed
* **show_symbol**: If the locale has a symbol defined, then show it in the position defined in the locale configuration
* **locale**: Which locale configuration are being used to display it. If it is `null` then the locale used in the request
                will be used. If it can't find the locale used then the `fallback_locale` is used.

### Editor
You can, optionally, install the editor. The editor is a collection of templates and controllers which will be exported
to your project in order to modify them as you want. By default, the templates uses the assets provided by Laravel.

##### Features

* List, search, create and edit and remove locales
* List, search and edit locale translations
* Full integrable with your project

#### Install & Customize your editor
You can install the editor with:

```
php artisan i18n:editor
```

This will add some files into your project:

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

As all editor files are being exported into your project you can modify the templates and also the controllers. Therefore you can
customize the editor as you want. Feel free to change any of those files exported in order to make the editor fit your
project.

If you want to restore all files, just use this command:

```
php artisan i18n:editor --reinstall
```