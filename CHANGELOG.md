# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [1.4.4](https://github.com/nikaia/translation-sheet/compare/v1.4.3...v1.4.4) (2020-09-14)


### Bug Fixes

* laravel 7 support ([623e04b](https://github.com/nikaia/translation-sheet/commit/623e04bad7cf018dea49260816e873417dd4eaf8)), closes [nikaia/translation-sheet#60](https://github.com/nikaia/translation-sheet/issues/60)

### [1.4.3](https://github.com/nikaia/translation-sheet/compare/v1.4.2...v1.4.3) (2020-09-14)


### Bug Fixes

* load publish command ([3dc1280](https://github.com/nikaia/translation-sheet/commit/3dc1280b962e167d92bff2e439785ad68b1791b2))

### [1.4.2](https://github.com/nikaia/translation-sheet/compare/v1.4.1...v1.4.2) (2020-09-14)

### [1.4.1](https://github.com/nikaia/translation-sheet/compare/v1.4.0...v1.4.1) (2020-09-14)


### Features

* Add json support ([b9ae229](https://github.com/nikaia/translation-sheet/commit/b9ae229731a47f82db44e858c2155f05f1db60c6))
* **json:** add json support ([06430bd](https://github.com/nikaia/translation-sheet/commit/06430bddc85b4987517cb7e382a8e5ef6dd0af6b)), closes [nikaia/translation-sheet#34](https://github.com/nikaia/translation-sheet/issues/34)
* **writer:** json format unescaped unicode ([c38547c](https://github.com/nikaia/translation-sheet/commit/c38547c8d9e6cec4db3e49f3555bb451fd18bbfe))

## [1.4.0](https://github.com/nikaia/translation-sheet/compare/v1.3.7...v1.4.0) (2020-09-07)


### Features

* Add translation_sheet:publish command. ([e5ed9dd](https://github.com/nikaia/translation-sheet/commit/e5ed9ddbe1bf49c368e81813c5602fc8f0dfe0e6))
* Add Laravel 8 support ([df2fe4f](https://github.com/nikaia/translation-sheet/commit/df2fe4fd9505de7bffbe77cae48f75867810ce7c))
* Remove meta sheet (not used) ([a1480b4](https://github.com/nikaia/translation-sheet/commit/a1480b40db541b20393d5bd23d7e6e2a77113392)), closes [#56](https://github.com/nikaia/translation-sheet/issues/56)
* Remove meta sheet (not used) ([373a541](https://github.com/nikaia/translation-sheet/commit/373a5413a792a46e21f1eee1624d9fdaaccc0ef4))


### Bug Fixes

* issue with translation array with key strings ("0", "1", ...) ([fe0f7d1](https://github.com/nikaia/translation-sheet/commit/fe0f7d15c22d56612de3f8f5daf55c1afc04ea54)), closes [#49](https://github.com/nikaia/translation-sheet/issues/49)
* issue with translations indexed with string keys when excluding files. ([d959544](https://github.com/nikaia/translation-sheet/commit/d9595443f29c295f03e61e50ccd5a31d4adb12fe))
* **excludes:** sends indexed array when excluding files to google api ([5aa1437](https://github.com/nikaia/translation-sheet/commit/5aa143733ad60e803096efb65384c9a38c7f4965))

### [1.3.7](https://github.com/nikaia/translation-sheet/compare/v1.3.6...v1.3.7) (2020-03-04)

### [1.3.6](https://github.com/nikaia/translation-sheet/compare/v1.3.5...v1.3.6) (2020-03-04)


### Features

* Add Laravel 7 support ([dd4bb1f](https://github.com/nikaia/translation-sheet/commit/dd4bb1f))

### [1.3.5](https://github.com/nikaia/translation-sheet/compare/v1.3.4...v1.3.5) (2019-10-10)


### Bug Fixes

* update laravel/framework constraints ([4caf5a0](https://github.com/nikaia/translation-sheet/commit/4caf5a0))

### [1.3.4](https://github.com/nikaia/translation-sheet/compare/v1.3.3...v1.3.4) (2019-10-10)


### Features

* support Laravel 6.* versions ([5da95ef](https://github.com/nikaia/translation-sheet/commit/5da95ef))

### [1.3.3](https://github.com/nikaia/translation-sheet/compare/v1.3.2...v1.3.3) (2019-09-04)


### Bug Fixes

* move codedungeon/phpunit-result-printer to dev section ([f095e71](https://github.com/nikaia/translation-sheet/commit/f095e71))

### [1.3.2](https://github.com/nikaia/translation-sheet/compare/v1.3.1...v1.3.2) (2019-09-04)


### Features

* Add Laravel 6.0 support ([f71470b](https://github.com/nikaia/translation-sheet/commit/f71470b))

<a name="1.3.1"></a>
## [1.3.1](https://github.com/nikaia/translation-sheet/compare/v1.3.0...v1.3.1) (2019-04-21)


### Bug Fixes

* Remove usage of deprecated Google client `setAuthConfigFile` method. ([7799f6f](https://github.com/nikaia/translation-sheet/commit/7799f6f)), closes [#40](https://github.com/nikaia/translation-sheet/issues/40)


### Features

* add a way to exclude some translations via the exclude config option. ([89313fe](https://github.com/nikaia/translation-sheet/commit/89313fe)), closes [#29](https://github.com/nikaia/translation-sheet/issues/29)



<a name="1.3.0"></a>
# [1.3.0](https://github.com/nikaia/translation-sheet/compare/v1.2.9...v1.3.0) (2019-02-28)


### Features

* Add Laravel 5.8 support ([257e4f3](https://github.com/nikaia/translation-sheet/commit/257e4f3))



<a name="1.2.9"></a>
## 1.2.9 (2019-02-23)


### Bug Fixes

* Avoid directory scan stopping after encountring vendor dir. ([a624e1e](https://github.com/nikaia/translation-sheet/commit/a624e1e))


<a name="1.2.8"></a>
## 1.2.8 (2019-01-16)

- Allow empty translations.


<a name="1.2.7"></a>
## 1.2.7 (2018-09-06)

- Change licence & Add L 5.7 support.


<a name="1.2.6"></a>
## 1.2.6 (2018-09-06)

- Add support for columns above Z.


<a name="1.2.5"></a>
## 1.2.5 (2018-04-23)

- Add package auto-discovery support.


<a name="1.2.4"></a>
## 1.2.4 (2018-03-05)

- Add Laravel 5.6 support.


<a name="1.2.3"></a>
## 1.2.3 (2017-08-28)

- Add L5.5 support.


<a name="1.2.2"></a>
## 1.2.2 (2017-07-21)

- Fix translation_sheet:push and google/apiclient v=2.2.0.

<a name="1.2.1"></a>
## 1.2.1 (2017-06-26)

- Fix translation_sheet:push and google/apiclient v=2.2.0


<a name="1.2.0"></a>
# 1.2.0 (2017-06-26)

- Update goole/apiclient dependency to ^2.1
