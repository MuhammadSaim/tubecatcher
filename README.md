# TubeCatcher

A wordpress plugin to download videos from the youtube.


## Requirments

1. PHP 7+
2. Composer 2+
3. YouTube Data API v3

## YouTube Data API v3
Generate the API key from the Google Console after that rename the file <kbd>tubecatcher-constants.dist.php</kbd> to <kbd>tubecatcher-constants.php</kbd>, paste the API key in the file.
```php
// YouTube api key
define('TUBECATCHER_YOUTUBE_API', 'YOUR_YOUTUBE_V3_API_KEY');
```
## Configuration
1. Clone the repo
```shell
git clone git@github.com:MuhammadSaim/tubecatcher.git
```

2. Composer install
```shell
composer install
```
## Instalation
1. Create a zip file of the main folder and install it through Wordpress Plugin install.
2. Just simply copy the folder into the <kbd>wp-content/plugins</kbd> directory.

## Note
Feel free to create a PR if you like to add more functionality.