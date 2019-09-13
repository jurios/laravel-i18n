# laravel-i18n

[![Build Status](https://travis-ci.com/jurios/laravel-i18n.svg?branch=master)](https://travis-ci.com/jurios/laravel-i18n)

## Disclaimer
Please, consider fill an issue if you see a bug or an unexpected behaviour. That would be really useful
to make `laravel-i18n` better.

## 1 -  What is `laravel-i18n`?
**laravel-i18n** is a Laravel built-in localization wrapper which adds some features for improving the localization
system.

### 1.1 - Features

* Translatable project texts detection (project files and 3th party exported translations also) and deprecated translations 
detection through a [sync process](#4---sync-process) in order to keep translation files updated.
* Configure languages, currencies and timezones
* Optional web editor to manage locales and translations

## 2 - Installation

First, include the package to your project:

```
composer require kodilab/laravel-i18n
``` 

Once you have `laravel-i18n`, you must publish the config files with:

```
php artisan vendor:publish --provider="Kodilab\LaravelI18n\I18nProvider"
```

You can check the `laravel-i18n` configuration in the `config/i18n.php` configuration file.

### 2.1 - Concepts
In `laravel-i18n` a `locale` is the main entity and contains the information needed in order to show a website localized
in an specific language and region. You will be able to create as many `locales` as you want. 

For example, if you want to create a website with a `Spanish from Spain`, `Spain from Argentina` and `English (general)`
you can do it creating three locales: `es_ES`, `es_AR` and `en`. We cover the `locale` creation in the following sections.

As you can see, for each `Locale` you are able to define the `ISO 639` language code which is a mandatory attribute 
and the region code (optional). Then, `laravel-i18n` will use the "full name" (called locale `reference`) which is a 
concatenation of `iso` and `region` in a particular format: 

For a `Locale` which `iso = en` and `region = GB`, for example, the reference would be `en_GB`.
So, `iso` is always lowercase and `region` uppercase. If a `Locale` does not have a region, then `reference = iso`.


### 2.1 - Migrations
`laravel-i18n` needs to create a new table in your database where it will persist the `locales` you define. 
This table is called `i18n_locales` by default. You can change the table name in the `config/i18n.php` file 
(this change must be done before applying the migration).

In order to create the table, just apply the migrations:

```
php artisan migrate
```

### 2.2 - Install

The install command will create the fallback locale based on the `fallback_locale` parameter 
defined on Laravel configuration in the `config/app.php` file.

The fallback locale in `laravel-i18n` has the same meaning as the `fallback_locale` for the Laravel localization system:
Is the language which will be used when a translation is not defined for a given language.
**As `laravel-i18n` will consider the texts found in the project are written in `fallback_locale`**, is
a good practice define in your `config/app.php` the same `fallback_locale` parameter as your language used in your 
codebase.

So, take a look if the `fallback_locale` defined in your `config/app.php` is the one you want and write this command:

```
php artisan make:i18n
```

This will add a new locale in the `i18n_locale` configured as `fallback` based on the `fallback_locale` parameter in
your `config/app.php` file.

## 3 - First steps

### 3.1 - Create new `Locales`
If you are creating a multilingual site, you would like add more `locales`. 
In order to create new locales, `laravel-i18n` provides some tools to make locales and remove them a easy task:

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
        'currency_number_decimals' => Number of decimals when render currency (ex: 2),
        'currency_decimals_punctuation' => Punctuation for decimals (ex: "."),
        'currency_thousands_separator' => Thousands separator (ex: ","),
        'currency_symbol' => Currency symbol (ex: "€"),
        'currency_symbol_position' => 'after|before',
        'carbon_locale' => "Carbon locale, usually the same as iso (you can leave it blank),
        'tz' => Timezone (ex: "Europe/Madrid", you can leave it blank an UTC will be used),
        'enabled' => true|false,
        'fallback' => false
    ]
```

##### Facade::removeLocale(string $reference);
The `Locale's reference` has been explained in the [Concepts section](#21---concepts).

For example, for removing the `Locale` which `iso="en"` and `region="GB"`, removeLocale should be called like this:

```
Facade::removeLocale('en_GB');
```

## 4 - Sync process 

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

## 5 - Showing translated texts in the request
`laravel-i18n` uses the `Laravel` localization system under the hood for showing the translated texts in each request.
So, in order to show a particular localized version for a request, you should follow the 
[Laravel localization instructions](https://laravel.com/docs/5.8/localization#configuring-the-locale). You must use
the Locale `reference` explained in the [Concepts section](#21---concepts) as the value in the configuration. 


What's more, `laravel-i18n` provides an extensible `middleware` which helps you to set the locale and timezone easily. 
In case you want to create your own, you can use it as a reference. 

If you want to extend the `middleware`, just create a [middleware](https://laravel.com/docs/5.8/middleware) 
which extends `\Kodilab\LaravelI18n\Middleware\SetLocale` and define the `getLocale()` function which must 
return a `Locale` instance which will be used during that request. 

For example, in this case we are going to get the locale from the User model 
(obviously a relationship between locale and user must exist):

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

Apart from `getLocale()`, you can optionally override `setLocale(Locale $locale)` and `setTimezone(Locale $locale)`. 
These methods are responsible of how the locale and the timezone are set into the configuration:

`setLocale(Locale $locale)` where `$locale` is the locale returned by `getLocale()` sets the locale value 
(`reference ` field) into the configuration using the `app.locale` parameter (which is used for the translations). 

`setTimezone(Locale $locale)` where `$locale` is the locale returned by `getLocale()` calls to 
`date_default_timezone_set` using the `timezone` defined in the locale (`tz` field). This timezone will be used 
for time values. 

If you want to modify how this values are set, just override the function you want.

## 6 - Currency
Apart from the translations support, `laravel-i18n` provides a helper function in order to show currency values 
localized:

Each locale has its own currency format configuration where you can define currency symbol, currency symbol position,
number of decimals, decimals separator and thousands separator.

Then, when you want to display a currency value just use the helper:

```
currency(float $value, bool $show_symbol = true, \Kodilab\LaravelI18n\Models\Locale $locale = null)
```

* **value**: The value to be displayed
* **show_symbol**: If the locale has a symbol defined, then show as is defined in the locale configuration
* **locale**: Which locale configuration are being used to display it. If it is `null` then the locale used in the request
                will be used. If it can't find the locale used, then the `fallback_locale` is used.

## 7 - Editor
You can, optionally, install the editor. The editor is a collection of templates and controllers which will be exported
to your project in order to modify them as you want. By default, the templates uses the assets provided by Laravel.

##### Features

* List, search, create and edit and remove locales
* List, search and edit locale translations
* Start a sync process
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
| GET    | i18n/sync                                 | i18n.sync                        | App\Http\Controllers\I18n\I18nController@sync
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

#WIP

## Model attribute translations
`laravel-i18n` provides a clean solution for handle `Eloquent model` translatable attributes, also. In this case,
translations will be persisted in database.

### Requirements
`laravel-i18n` needs an additional table for each `Model` you want to make translatable in order to persist translations. 

By convention, the name of this table will be the name of the model + `_translations` (Therefore, if your model is `Car`, then the table where translations
will be persisted is `car_translations`). Therefore, a migration must be created in order to create the tables.

That new table will contains all translatable attribute of the `Model`. Thus, those attributes won't belong to the `model`
table anymore.

A wizard `Command` is provided in order to make this step easier and faster.


You can generate the migration with this command:

```
php artisan i18n:modeltranslation
```

This command will ask you to choose which `Model` of your project do you want to add translations. Once you choose one,
a customized migration will be generated with the needed relationships between that particular `model` and the `locale`
models. You will need to add the columns which represents translatable attributes of the model only.   

Then, you can migrate your database.
```
php artisan migrate
```

To read and write translations for that `Model` you must add the `HasTranslations` trait to your `Model` and add the
translatable attributes into the model [`casts` array](https://laravel.com/docs/6.0/eloquent-mutators#attribute-casting)
like so:

```
class Car extends Model {
    use HasTranslations;

    protected $casts = [
        'description' => 'translatable'
    ];
}
```

#### Modify your __get($name) method
**Only do this step in case your `Model` is extending the `__get($name)` method. Otherwise, no additional modifications
are needed.**

Add this code to your `__get($name)` method in order to make translated attributes accessible throught your `model``
```
public function __get($name)
{
    ....

    if ($this->isTranslatableAttribute($name)) {
        return $this->getTranslatedAttribute(Locale::getLocaleOrFallback(config('app.locale')), $name);
    }

    return parent::__get($name);
}
```

### Operations with translatable attributes
#### Read translatable attributes

You can access to your translatable attributes as they were attributes of your `model`. When you get a translated attribute
using this way, the locale loaded by `Laravel` will be used (if it does not exist, then the `fallback locale`will be used).

```
    $locale = config('app.locale'); // 'es'
    $car->description // Spanish description
```

Another you to get your translation is using the `getTranslatedAttribute` method. This method allow us to define the
`locale` used:

```
    $locale = Locale::getFallbackLocale(); // en
    $car->getTranslatedAttribute($locale, 'description'); // Ensligh translation
```

#### Creating/Updating translations

You can create/update a translation with `setTranslatedAttribute` or `setTranslatedAttributes` method:

```
    $en = Locale::getLocale('en_GB');
    $es = Locale::getLocale('es_ES');

    $car->setTranslatedAttribute($en, 'description', 'Brief description');
    $car->setTranslatedAttribute($en, 'long_description', 'Long description');

    $car->setTranslatedAttributes($es, [
        'description' => 'Breve descripción', 
        'long_description' => 'Descripción larga'
    ]);
```