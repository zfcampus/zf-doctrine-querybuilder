# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.8.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.8.0 - 2019-02-02

### Added

- [#52](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/52) adds support for PHP 7.3.

- [#46](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/46) adds support for DoctrineModule 2.1
  and DoctrineORMModule 2.1.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.7.0 - 2018-12-11

### Added

- [#50](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/50) adds support for zend-hydrator version 3 releases, while retaining
  compatibility with previous versions.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.6.0 - 2018-01-17

### Added

- [#42](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/42) adds
  `leftjoin` ORM query type.

- [#44](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/44) adds
  support for PHP 7.2.

### Changed

- [#38](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/38) changes
  methods visibility in Query Providers to `protected`:
    - `getConfig()`
    - `getFilterManager()`
    - `getOrderByManager()`

### Deprecated

- Nothing.

### Removed

- [#44](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/44) removes
  support for HHVM.

### Fixed

- [#40](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/40) fixes
  ODM `isnull` and `isnotnull` query filters to give correct result with
  nullable fields.

## 1.5.1 - 2016-11-14

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#35](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/35) fixes
  an issue with `DefaultOdmFactory` and `DefaultOrmFactory` when used with
  ZF2 with context of `AbstractPluginManager`.

## 1.5.0 - 2016-11-10

### Added

- [#32](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/32) adds
  support for PHP 7.
- [#32](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/32) adds
  support for v3 releases of Zend Framework components, while retaining
  compatibility for v2 releases.
- [#32](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/32) exposes
  the module to [zendframework/zend-component-installer](https://github.com/zendframework/zend-component-installer).

### Deprecated

- Nothing.

### Removed

- [#32](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/32) removes
  support for PHP 5.4 and PHP 5.5.

### Fixed

- [#32](https://github.com/zfcampus/zf-doctrine-querybuilder/pull/32) adds a
  ton of tests to the module.
