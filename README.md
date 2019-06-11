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
As `laravel-i18n` will consider the texts found in the project as a translation of the `fallback_locale` of the text, is
a good practice define in your `config/app.php` the same `fallback_locale` as your language used in your codebase.

Once you have the desired `fallback_locale` in your `config/app.php`, let's install the `laravel-i18n` package:

```
php artisan make:i18n
```

This will add a new locale in the `i18n_locale`.

#### Sync process 

Once we have at least one locale (and this locale is the fallback) in our `i18n_locales` table, 
we can start the sync process.

As `laravel-i18n` considers the `locale_fallback` locale as the language used in our codebase, the idea behind `sync` 
is keep updated the `lang/{locale_fallback}.json` file. The `sync` process will detect the new translatable texts 
from our codebase (calls to `_()` function) & all 3th-party translations exported in`lang/{locale}/*.php` files 
and will add them into the `lang/{locale_fallback}.json` file. 
What's more will detect the deprecated translations in the `lang/{locale_fallback}.json` file and will remove them from 
each `lang/{locale}.json` file. 

You can start a sync process with the command: 

```
php artisan i18n:sync
```

This process should fired as frequently as possible. It's recommended execute this process every deployment.

**Important:** New texts are added only in the fallback locale json file. If you want to know why, please take a look
to the next section. 

##### Sync detailed

What the sync process does is:

1. Look for new texts exported into the `lang/{locale}/*.php` files and add them to the **fallback locale translation file (only)**
2. Look for the calls to the translation function (by default, `_()`) in the project codebase and add them to the **fallback locale translation file (only)**
3. Remove deprecated transalations which are not present neither in codebase or exported translations from **all** `{locale}.json` files.

The reason why new texts are only included in the fallback locale is because they need to be translated in order to add
them in a specific language. (They can't be added with an empty ("") translation because Laravel will consider "" as the translation
As a result, you will see only "blank" spaces instead of default text for untranslated texts).

`laravel-i18n` will provide commands in order to list what texts remains untranslated for each locale in order to let you identify them
fastly (WIP). In the editor (WIP) you will be able to list them too.
