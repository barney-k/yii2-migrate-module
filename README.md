# yii2-migration-module
A module for Yii2 framework to create and manage migration files without CLI

Installation
============

Two-step installation with **composer**.

> This instructions assumes that you have **composer** installed and **db** configured for your Yii2 application.

Step 1: Download using composer
-------------------------------

Add **yii2-migration-module** to the require section of your **composer.json** file:

```js
{
    "require": {
        "barney-k/yii2-migration-module": "dev-master"
    }
}
```

And run following command to download extension using **composer**:

```bash
$ php composer.phar update
```

Step 2: Configure your application
----------------------------------

Add migration module to both web config files (or backend config if you're using advanced templating) as follows:

```php
...
'modules' => [
    ...
    'migration' => [
        'class' => 'barneyk\migration\MigrationModule',
    ],
    ...
],
...
```

Configuration
=============

You can configure the module by adding additional parameters to the config file after the **class** key:

- admins:
	- this is an array usernames, that can access this module
	- default: `[]`
- migrationPath:
	- path of the migration files
	- default: `'@vendor/barney-k/yii2-migration-module/migrations'`
- dateFormat:
	- php date format string for displaying the create and apply dates.
	- default: `'Y.m.d. H:i:s'`
- migrationTable:
	- name of the database table for the migrations
	- default: `'migration'`

Example:
--------

```php
...
'migration' => [
	'class' => 'barneyk\migration\MigrationModule',
	'admins' => ['root','admin','johndoe'],
	'migrationPath' => '@app/migrations',
	'dateFormat' => 'd/m/Y H:i:s',
	'migrationTable' => 'my_migrations',
],
...
```