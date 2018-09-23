# laravel-i18n

## What is this?
**laravel-i18n** is an alternative solution for deal with localization in Laravel
framework. It uses database as a way to store the translated texts and cache to
improve the efficiency.

### Features

* Identify your original text when is rendered and looks for the apropiate translation
* Your translations are persisted in database and uses cache to retrieve it faster
* Automatically new translatable lines detections statically (parsing php files) and dynamically (when they are going to be rendered)
* Web platform to manage languages and translations
* Fallback language when a translation doesn't exists
* Integrated with Laravel configuration

![Laravel-i18n](image.png)


### Installation
```
composer require kodilab/laravel-i18n dev-master
``` 

This will install `laravel-i18n` and `laravel-filters` which is a dependency used. 

Once you have `laravel-i18n` in your project, you must publish the assets/config files with:

```
php artisan vendor:publish --provider="Kodilab\LaravelI18n\I18nProvider"
php artisan vendor:publish --provider="Kodilab\LaravelFilters\QueryFilterProvider"
```

This will add `i18n.php` and `filters.php` config files to your `config` directory. At this point, ignore `filters.php`
config file. However, `i18n.php` should be check over it before continue:

#### laravel-i18n configuration
This config file have very useful comments about the meaning of each variable. As `larave-i18n` create three tables in your
database, it is recommended checking over the name of the tables in order to avoid conflict.

Now that your config it's ready, it's time to migrate your database.

#### Migrate database
```
php artisan migrate
``` 

#### Add the laravel-i18n routes to your `web.php` file
You can place the `laravel-i18n` routes where you want in your routes. No matters group, prefix... Just add the call to
the routes with:

```
i18n::routes();
```

This allows you to add middlewares to authorization and authentification befores giving acces to the `laravel-i18n` 
management pages.

This is an example:

```php
Route::group(['middleware' => 'somemiddleware'], function() {
    i18n::routes();
});
```

Now, if you call to `php artisan route:list` will see new routes added to your list that they will be used by `laravel-i18n`.

#### Start using the translation system

Now, every time your code calls to the function `t()`, the text provided will be a translatable text. It be part of the
`laravel-i18n` translations and they will be managed through the `laravel-i18n` pages.

So, for example, if in your view has:
```
t('Hello world!')
```

This will be added as a translatable text and you could provide a translation for each language enabled in your platform.

The function `t()` has the a similar signature that laravel localization function `_()`:

```
function t(string $text, $replace = [], $locale = null, $honestly = false) 
```

* **$text**: Well, the text that will be translatable
* **$replace**: Equals as Laravel `_()`, you can use variables in your $text which it will be replaced by the values in 
$replaces. For example, this call `t('Hi, :name', ['name' => 'Jordi'])` will be translated as `Hi, Jordi` 
or `Hola, Jordi`....
* **$locale**: The language to be translated (using the ISO_639_1) When this value is `null`, 
then this text is translated dynamically depending on the request language.
* **honestly**: When `honestly` is `true` then `laravel-i18n` will be, well... honest. That means that when a 
translation can't be found for the request language, then it will return `""`. When `honestly` is `false` then it will 
try to return a configured `fallback` language translation. We'll se more details about that soon.

Well, it's time to replace your texts in your code by call to `t()`.

#### Automatically translatable texts detection
The main reason why I created this package is because I didn't want to deal with translations manually. It's hard to 
maintain your translations when you have to add a new entry in your `language.json` when you add a new text (specially 
when you are developing a project).

With this package you can automatically detect new texts in your code in order to create language translations. And there
are two ways to do it (and is recommended using both):

##### Static text detection
When you call to `php artisan i18n:sync`, it will syncronize your translatable texts in your code with your translations
in your database. It will delete the entries in your database which correspond to removed texts and it will add the new
texts. This is really useful specially when you are developing a view and you are modifying your texts frequently

Is recommendend that you call this functions as frequently as you can just because it keeps your translations table up
to date.

Yes, I know that are you thinking. Calling a artisan command doesn't look something automatically. 
Yes, that's true. But, come on, part of the process is done automatically.

##### Dynamic text detection
Calling to `php artisan i18n:sync` is something that you can forget some times. In order to fix this, when someone 
renders a text that it should be translatable but is not added to your translation table, then `laravel-i18n` 
do it for you. As a result, you can enter to the manage web page and you'll see this text to translate it. The down side
of this method is that information about the files where this text appears can't be established. But if you call to 
`php artisan i18n:sync` after that, this information will be added.

So, using static and dynamic detection you will have your database ready for use always without manual interaction.

### How it deals with locales
When the `locale` argument is null when you call to `t()`, it will translate the text to the Laravel config locale
defined in `config/app.php` file.

If your project is multi language website, then you can change this value every request depending in the user configuration
or browser preferences. For example, a way to deal with this is using this middleware sample (TODO)

This middleware just look for the user's browser language preferences and it tries to look for the corresponding enabled
language. If it exists, then it changes the Laravel locale to this new value. If it's not, then use the fallback language
locale.

### Honestly mode
As it is explained above, when you call `t()` with honestly mode activated it will return a blank text when a
translation doesn't exists for the `locale` defined.

To avoid having `blank` spaces in your website, by default this mode is desactivated. When it is desactivated, it will
return the fallback language translation. 

How can I define my `fallback_language`?  Another configuration variable in your Laravel app config (`config/app.php`) 
is the `fallback_locale`. This is the language that will be used as a `fallback`. The idea here is that your 
`fallback_locale` setting should be the same language used in your views texts. So, for example, if your code texts 
are written in English, your `fallback_locale` should be `en` (`en` is the ISO 639-1 for the English language ). 
`Laravel-i18n` will consider the original text as the translation for the language defined in  `fallback_locale` 
the first time is detected (you can change the translation after that).

Thats why you will see that your `fallback_locale` language is always 100% translated.

**Be careful!:** your `fallback_locale` should be the ISO 639-1 of an **enabled** language.
