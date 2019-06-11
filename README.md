# laravel-i18n

[![Build Status](https://travis-ci.com/jurios/laravel-i18n.svg?branch=master)](https://travis-ci.com/jurios/laravel-i18n)

## What is this?
**laravel-i18n** is a Laravel built-in localization wrapper which adds some features for improving the localization
system.

### Features

* Automatically new translatable text detection (project texts and 3th party exported translations)
* Automatically removes deprecated translations in order to keep it clean
* Web editor to manage languages and translations from your templates (WIP)


### Installation

First, include the package to your project:

```
composer require kodilab/laravel-i18n dev-master
``` 

Once you have `laravel-i18n` in your project, you must publish the config files with:

```
php artisan vendor:publish --provider="Kodilab\LaravelI18n\I18nProvider"
```

You can check the `laravel-i18n` configuration in the `config/i18n.php` configuration file.

#### Migrations
`laravel-i18n` needs to create a new table in your database where it will persist the languages. This table is called
`i18n_locales` by default. You can change the table name in the `config/i18n.php` file (this change must be done before
applying the migration).

In order to create the table, just launch the migrations:

```
php artisan migrate
```

#### Install

```
php artisan make:i18n
```

This will create the default fallback locale. The fallback locale is the language will be used by default (recommended use
 the same language used in the template texts). This value is taken from the `config/app.php` file (`fallback_locale` parameter).

For example, considering my template text are written in `english`, my `fallback_locale` parameter should be `en`. 
Therefore, the `locale` persisted will be the `en` locale. As `english` is considered the fallback locale, when a translation
does not exist for a given language, the `english` translation will be used.

#### Sync process
Once we have at least one locale in our `i18n_locales` table, we can start the sync process. This process will re-build
our `lang/{locale}.json` files with the existing translatable texts from our codebase (templates files, controllers etc..) 
& all 3th-party translations exported in`lang/{locale}/*.php` files. 

Only translated texts will appear in the  `lang/{locale}.json` files.  As `laravel-i18n` considers the `fallback_locale` 
the language used on the codebase, `lang/{fallback_locale}.json` will contain all texts found.

This process should fired frequently (specially when a text has changed/add/removed). It's recommended execute this process
every deployment. In order to start it:

```
php artisan i18n:sync
```

##### Sync detailed
