# laravel-i18n

## What is this?
**laravel-i18n** is an alternative solution for deal with localization in Laravel
framework. It uses database as a way to store the translated texts and cache to
improve the efficiency.

### Features

#### No files, no keys
**laravel-i18n** works with the original text in order to find the translated one thus you don't need to use keys like `messages.welcome`. Every text is persisted
on database thus you don't have to deal with multiple `php` or `json` files.
So easy like this:

```
{{ t('Hi world!') }}

// Hola món!
```

What's more, you can ask for a specific translations:

```
{{ t('Hi world!', 'es') }}

// Hola mundo!
```

#### Automatically add new translations and remove the deprecated ones
With `artisan i18n:sync` it will detect all the texts to be translated. Then it
will add those which doesn't exists on database and remove the deprecated ones
in order to keep your database synchronized with your project.


#### Cached translations to be more efficient
When a translation is required, it will look for it in cache first. If it doesn't
exists there, then it look for it in database. When a translation is found, it is
cached in memory in order to be faster next time.

It uses the cache Laravel system thus it is *Memcached and Redis* compatible.


## Getting started
To start using **laravel-i18n** you can should add this repository to your `composer.json` file:

```
"repositories": [
  {
      "type": "vcs",
      "url": "https://github.com/jurios/laravel-i18n"
  }
],
```

Then you should require the **laravel-i18n** package:

```
composer require kodilab/laravel-i18n
```

It will be discovered to your Laravel project automatically. Just we should
publish the configuration files with:

```
php artisan vendor:publish --provider="Kodilab\LaravelI18n\I18nProvider"
```

## Configuration

### Configuring the tables
**laravel-i18n** needs two tables in your database:

1. Languages (`I18n_languages`): The languages that **laravel-i18n** supports. It will be filled in by a migration.

2. Translations (`I18n_translations`): The translations of each language enabled in your project

**laravel-i18n** provides the migrations to create this tables. You can modify the table name editing the configuration file (`configs/i18n.php`).

Once everything is ready, just run this in order to migrate your database:

```
php artisan migrate
```

### Configuring your languages
During request lifecycle, there are 3 roles that a language can take on:

1. Base language: This is the language which has been used in your view texts. This **must** be the same language which is defined in your Laravel configuration file (`config/app.php`) in the key `locale`.

So, for example, if `locale` is `es` my view texts should be written in `Spanish`:

```
<div>{{ t('Hola mundo') }}
```

2. Default language: (Work in progress) If the language request by the user doesn't exists, or it isn't enabled, then **laravel-i18n** will try to translate it using the default language. If it isn't translated too, then base language is used (this will be translated because is the language used in your views).

3. Requested language: **larave-i18n** looks into the `session` for the key `locale`.
If it exists and it's a valid `ISO_639_1` code, then it will use this language for translations. If it isn't a valid or is not defined then default language is used.

### Start using **laravel-i18n**

** laravel-i18n** can be called in your views with:

```
{{ t($text, $replace, $locale, $honestly) }}
```
The interface is very similar to `_()` function:

1. **$text** is the text to be translated
2. **$replace** is an array of values to be bound in your text (same way as Laravel)
3. **$locale (optional)** Language used for translation. If is `null`, requested language is used
4. **$honestly (optional)** Translation can be perform in two modes:

  * **$honestly = true** If translation doesn't exist for this specific locale, **null** is returned and a blank space is rendered
  * **$honestly = false** If translation doesn't exist for this specific locale, it looks for the default language translation. If it doesn't exists neither, then it will render the base language translation (it must exist).

  When `$honestly` is not defined, then `$honestly = false`.

This is a real example:

```
{{ t('Tengo :count manzanas!', ['count' => 3], 'en')}}
// I have 3 apples!

{{ t('Tengo :count manzanas!', ['count' => 3], 'ca')}}
// Tinc tres pomes!

// Translation doesn't exists in catalan and my default language is 'en'
{{ t('Tengo :count manzanas en la nevera!', ['count' => 3], 'ca')}}
// I have 3 apples!

{{ t('Tengo :count manzanas en la nevera!', ['count' => 3], 'ca', true)}}
//
```

### Adding new translations lines to the database
Every time that a translated text is requested using the `t()` function, **laravel-i18n** will look for it in database. If it doesn't exists, it will create a translation using *base language*.

If you want to **syncronize** your database with the texts used in your views (and `app/` directory), you can use the artisan command `i18n:sync`. This will delete all the translated texts in your database which are not present in your files anymore and add the new ones.

## Work in progress
This is a work in progress. A web editor, for edit your translated texts and add other language's translated texts base on your base language texts is necessary in order to make this package really functional.
