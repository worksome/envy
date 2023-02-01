
# Envy
Automate keeping your environment files in sync.

[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/worksome/envy/run-tests.yml?branch=main)](https://github.com/worksome/envy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![PHPStan](https://github.com/worksome/envy/actions/workflows/phpstan.yml/badge.svg)](https://github.com/worksome/envy/actions/workflows/phpstan.yml)

How many times have you onboarded a new dev onto your team, only to have to spend ages debugging with them because your project's `.env.example` file is wildly outdated? Too many to count, if you're anything like us. Wouldn't it be nice if there were a way to ensure your environment files stay up to date? That's why we created Envy!

**With a simple Artisan command, you can sync your environment files with your project config to keep everything fresh.**

## Installation

You can install the package via composer:

```bash
composer require worksome/envy --dev
```

We recommend publishing our config file, which allows you to fine-tune Envy to your project's requirements. You can do that using our install command:

```bash
php artisan envy:install
```

That's it! You're now ready to start using Envy.

## Usage

Envy provides two commands: `envy:sync` and `envy:prune`. Let's break down how to use each of them.

### `php artisan envy:sync`

<img width="653" alt="CleanShot 2022-02-01 at 16 18 27@2x" src="https://user-images.githubusercontent.com/12202279/152007083-604c3609-2aef-4bae-b053-e26a4b009fc8.png">

This command combs through your project's config files for calls to Laravel's `env` function. After finding them all, it will compare them against your configured environment files (by default just your `.env.example`) for missing entries. If there are missing entries, you will be given the choice to either:
1. Add the missing keys to your environment file
2. Add the missing keys to Envy's exclusion list

To learn more about configuring environment files, config files and exclusions, see the Configuration documentation.

The `envy:sync` command provides several options you might find helpful.

#### `--path`

If you'd like to run the sync command against a certain environment file, rather than your configured environment files, you may pass the path to the specified environment file using this option.

#### `--dry`

The `--dry` option will prevent the command from actually performing any updates. This is useful if you want to run Envy as part of a CI check without actually making updates. If missing entries were found, the command will fail, which would in turn fail the check in CI.

#### `--force`

If you want to automatically make changes to your configured environment files without being asked to confirm, you may pass the `--force` option. This is useful for CI bots, where you want to automate changes to your `.env.example` file, as no user input will be requested.

### `php artisan envy:prune`

<img width="580" alt="CleanShot 2022-02-02 at 12 03 51@2x" src="https://user-images.githubusercontent.com/12202279/152150299-59909889-8e18-4c19-9387-7f8d11019847.png">

This command will search your project's configured environment files (by default just your `.env.example`) for entries that could not be found in any of the configured config files. If there are additional entries, you will be given the choice to either:
1. Remove the additional entries from your environment file
2. Add the missing keys to Envy's inclusion list

To learn more about configuring environment files, config files and inclusions, see the Configuration documentation.

The `envy:prune` command provides several options you might find helpful.

#### `--path`

If you'd like to run the prune command against a certain environment file, rather than your configured environment files, you may pass the path to the specified environment file using this option.

#### `--dry`

The `--dry` option will prevent the command from actually pruning any environment variables. This is useful if you want to run Envy as part of a CI check without actually making updates. If additional entries were found, the command will fail, which would in turn fail the check in CI.

#### `--force`

If you want to automatically make changes to your configured environment files without being asked to confirm, you may pass the `--force` option. This is useful for CI bots, where you want to automate changes to your `.env.example` file, as no user input will be requested.

## Configuration

You can customise Envy to suit your project's requirements using our `envy.php` config file. Here is a breakdown of the available options.

### `environment_files`

Out of the box, Envy will only make changes to your `.env.example` file. If you want to add additional `.env` files, such as `.env.testing` or `.env.dusk`, you can append them to this array.

> ⚠️ We do not recommend adding your `.env` file to this array as it could cause unwanted side effects, particularly in CI.

### `config_files`

By default, Envy will recursively scan all files in your project's `config` directory. For most projects, this will suffice. If your project makes `env` calls *outside* of config files (which is an anti-pattern), you should add the relevant files or directories to this array.

This is also useful if you make use of a package for which you have *not* published its config file. You may instead add an entry to this array that points to the base config file in the `vendor` directory.

Note that if you reference a directory instead of a file, it will include all files in that directory recursively.

### `display_comments`

<img width="867" alt="CleanShot 2022-02-01 at 16 28 42@2x" src="https://user-images.githubusercontent.com/12202279/152009032-093cb464-9e69-4fdc-9869-291907b7b001.png">

Some config keys, such as the one pictured above, may include comments. If you set `display_comments` to `true`, we will convert the config comment to an environment comment and place it above the relevant environment variable when inserting it into your configured environment files. This can be helpful in certain projects for remembering the purpose of various environment variables.

### `display_location_hints`

When combing your config files, we make a note of the file and line where you called `env`. If you set `display_location_hints` to `true`, we will create a comment with this location information and place it above the relevant environment variable when inserting it into your configured environment files. This can be helpful in certain projects for locating the usage of various environment variables.

### `display_default_values`

When combing your config files, we make a note of any default parameter set in the `env` call. For example, given a call for `env('APP_NAME', 'Laravel')`, the default would be `'Laravel'`. If `display_default_values` is set to `true`, we will insert the default value as the value for the relevant environment variable when updating your configured environment files.

For obvious reasons, we will only insert scalar (primitive) types when copying default values. 

> ⚠️ If you have `exclude_calls_with_defaults` set to `true` (which is the default), this option will have no effect because calls with defaults will be ignored.

### `exclude_calls_with_defaults`

If you copy over every single call to `env`, your environment files will quickly become difficult to read. To help alleviate this, we provide the option to ignore calls to `env` that have default values provided. By default this is enabled, but setting this to `false` will let Envy sync environment variables that have defaults too.

### `exclusions`

This array is a collection of environment keys that should never be synced to your environment files. By default, we include all Laravel environment variables that aren't included in the default `.env` file created when you first create a new Laravel project. Removing values from this array will cause them to be picked up again whilst syncing. Of course, if you have custom variables or variables provided by packages that you want to ignore, you may add them here.

If you select the `Add to exclusions` option when running `php artisan envy:sync`, this array will be updated with the environment variables listed by that command.

> 💡 You may still manually insert keys from your exclusions into your `.env` file. We won't remove them.

### `inclusions`

This array is a collection of environment keys that we should never prune from your configured environment files, even if we cannot find reference to those variables in your configured config files. This can be useful for JS variables used by Laravel Mix, for example.

If you select the `Add to inclusions` option when running `php artisan envy:prune`, this array will be updated with the environment variables listed by that command.

## Advanced

Once you're familiar with the basics of Envy, you may find these advanced features useful.

### Filters

Sometimes, you'll want a more powerful way to represent items in the `exclusions` and `inclusions` lists than basic strings. For example, imagine you want to add all environment variables beginning with `STRIPE_` to the 
exclusions list. Rather than manually inserting them all individually, you can use the `Worksome\Envy\Support\Filters\Filter` class.

```php
/**
 * Any environment variables that are added to exclusions will never be inserted
 * into .env files. Our defaults are based on the base Laravel config files.
 * Feel free to add or remove variables as required by your project needs.
 */
'exclusions' => [
    Filter::wildcard('STRIPE_*'),
],
```

Now, any environment variable starting with `STRIPE_` will automatically be excluded when syncing to your configured environment files. We also offer `Filter::regex`, which is an even more powerful 
filter that allows you to match environment variables against regular expression you provide. In fact, the `exclusions` and `inclusions` lists will accept a `string` or *any* class which implements
the `Worksome\Envy\Contracts\Filter` contract, so you can even implement your own filters if that's your style.

## Testing

We pride ourselves on a thorough test suite and strict static analysis. You can run all of our checks via a composer script:

```bash
composer test
```

To make it incredibly easy to contribute, we also provide a docker-compose file that will spin up a container
with all the necessary dependencies installed. Assuming you have docker installed, just run:

```bash
docker-compose run --rm composer install # Only needed the first time
docker-compose run --rm composer test # Run tests and static analysis 
```

Support for XDebug is baked into the Docker image, you just need to configure the `XDEBUG_MODE` environment variable:

```bash
docker-compose run --rm -e XDEBUG_MODE=debug php
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Luke Downing](https://github.com/lukeraymonddowning)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
