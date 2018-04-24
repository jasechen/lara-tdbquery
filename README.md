# Laravel Traditional DB Query

This Laravel package to query DATA from database with old way.


### \# Installation

1. use `composer` install this package

        $ composer require jasechen/lara-tdbquery

2. edit `config/app.php`

        $ joe config/app.php

        # add
        #
        'providers' => [
            ...
            Jasechen\Tdbquery\TdbqueryServiceProvider::class,
            ...
        ],

3. reload and update packages

        $ composer dump-autoload


### \# Licence
MIT LICENSE [![](https://png.icons8.com/external-link/win/16/2980b9)](https://github.com/jasechen/laravel-jsonponse/blob/master/LICENSE)
