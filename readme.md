Get current versión and changelog directly from GIT.

## Installation

Add the following to the `require` section of your projects composer.json file:

```php
"cheycron/git-changelog": "1.0.1",
```

Run composer update to download the package

```php
php composer.phar update
```


## Get Current Version
```php
GitChangelog::currentVersion(); // Returns v1.x.x
GitChangelog::append('v2')->preppend(' beta')->currentVersion() // Returns v2.x.x beta
```

## Get Changelog
```php
GitChangelog::parse()->changelog;
```

will return
```php
Illuminate\Support\Collection Object
(
    [items:protected] => Array
        (
            [0] => Array
                (
                    [hash] => 65d8355b98987bc2153ade2a3d111dccb4723e61
                    [email] => author@email.com
                    [author] => Cheycron Blaine
                    [date] => Carbon\Carbon Object
                    [message] => Commit Message
                    [markdown] => Commit Message with Markdown
                    [subject] => First Line of the Commit Message
                    [version] => v1.45
                )
            [1] => Array
                (
                    [hash] => 65d8355b98987bc2153ade2a3d111dccb4723e61
                    [email] => author@email.com
                    [author] => Cheycron Blaine
                    [date] => Carbon\Carbon Object
                    [message] => Commit Message
                    [markdown] => Commit Message with Markdown
                    [subject] => First Line of the Commit Message
                    [version] => v1.44
                )
```