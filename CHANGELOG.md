# Changelog

All notable changes to `envsync` will be documented in this file.

## v0.3.1 - 2022-02-21

### Fixed
- Two missing environment variables in the default configuration for Laravel 9 projects [#6](https://github.com/worksome/envy/pull/6)

## v0.3.0 - 2022-02-19

### Added
- Support for `Filter` objects for more complex inclusions and exclusions [#5](https://github.com/worksome/envy/pull/5)

## v0.2.2 - 2022-02-14

### Fixed
- Boolean values will now be copied over as defaults [#2](https://github.com/worksome/envy/pull/2)

## v0.2.1 - 2022-02-07

### Added
- Added `MYSQL_ATTR_SSL_CA` to `exclusions`

## v0.2.0 - 2022-02-07

### Changed
- Renamed `blacklist` and `whitelist` to `exclusions` and `inclusions` respectively [#1](https://github.com/worksome/envy/pull/1)
