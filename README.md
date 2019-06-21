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

* Automatically adds the new translatable text (detected by parsing the project texts and 3th party exported translations)
* Automatically removes deprecated translations in order to keep it clean
* Web editor to manage languages and translations from your templates (WIP)
* Utils for list remaining translations for each locale (WIP)

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

#### Install

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
