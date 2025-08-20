# Changelog

## [1.3.0](https://github.com/timber/teak/compare/v1.2.2...v1.3.0) (2025-08-20)


### Features

* Add Hooked tag compilation ([#18](https://github.com/timber/teak/issues/18)) ([55d1e33](https://github.com/timber/teak/commit/55d1e333287ce5b9e0c686dea09a7785e6ebc01e))

## [1.2.2](https://github.com/timber/teak/compare/v1.2.1...v1.2.2) (2024-10-07)


### Bug Fixes

* Fix for function generator not working because class is not defined ([#16](https://github.com/timber/teak/issues/16)) ([09a1603](https://github.com/timber/teak/commit/09a1603eceb86e6f202304a962851f7254a64762))

## [1.2.1](https://github.com/timber/teak/compare/v1.2.0...v1.2.1) (2024-09-19)


### Bug Fixes

* Add line with info about inherited method ([cb963ee](https://github.com/timber/teak/commit/cb963ee900b57dc42632de0c22cbdbfb18d5f752))
* Add logic for adding inherited method info ([e47b91e](https://github.com/timber/teak/commit/e47b91ea43fe4d97d929d4b9b9dfe833f979092e))
* Fix broken tables ([59549b2](https://github.com/timber/teak/commit/59549b25e2339a009368f53315d703e1395c58d4))
* Fix hook reference ([533b163](https://github.com/timber/teak/commit/533b163b4efce6410609fe6af7d7f9a067f5c53c))
* Fix whitespace for table wrappers ([03f93aa](https://github.com/timber/teak/commit/03f93aa076a1d8f7447f9d8e46d8b8f77a272c6c))
* Only include parent methods that are not already present ([6d3ab73](https://github.com/timber/teak/commit/6d3ab7395dc07b917514303400cc2814fca8c34f))

## [1.2.0](https://github.com/timber/teak/compare/v1.1.0...v1.2.0) (2024-09-13)


### Features

* Add .table-responsive CSS class for reference tables ([#11](https://github.com/timber/teak/issues/11)) ([8e04e6e](https://github.com/timber/teak/commit/8e04e6e7da966c22a7a2706b649309f6a961bce2))

## [1.1.0](https://github.com/timber/teak/compare/1.0.6...v1.1.0) (2024-07-03)


### Features

* Include parent methods in class reference ([2165a5e](https://github.com/timber/teak/commit/2165a5e928dfc16405eb53a2e430927b773cc723))
* Update dependencies ([92255a1](https://github.com/timber/teak/commit/92255a1bc9e69aabb0004ebb12d3bad606ee4f98))


### Bug Fixes

* Add all ancestor class methods ([200df60](https://github.com/timber/teak/commit/200df60a4396e797ff041996d09c26c606f1267b))
* Add ancestor properties for class reference ([dd5b5d6](https://github.com/timber/teak/commit/dd5b5d6c4d0074b83b559c924e87ca57aa07281d))
* Add inherited methods to ApiTable ([f740ab0](https://github.com/timber/teak/commit/f740ab0844463038ee9b4b084c8960bda8384547))
* Fix a bug when hook reference returned empty ([4a07ec6](https://github.com/timber/teak/commit/4a07ec6cac054d3c5c91e24c92fbf7c10bc78f8e))
* Fix a bug when hook reference returned empty ([6df2121](https://github.com/timber/teak/commit/6df2121c69e36b5c54210008e6688653f144d021))
* Fix some issues with PHP 8 ([c71f53e](https://github.com/timber/teak/commit/c71f53ed56bb8d02ff6468becc23a283fc89076a))
* Rename class ([704ede7](https://github.com/timber/teak/commit/704ede7fc49142aa92f7a0079c77a9e35c0e1dd3))
* Sort properties and methods by name ([34a94d8](https://github.com/timber/teak/commit/34a94d8a0d1572a499bf441ef3ac86b96e760cf9))


### Miscellaneous Chores

* Add Release Please ([89e3ecb](https://github.com/timber/teak/commit/89e3ecb754c6d99bba4a4cadbe4edd97b67e6709))
* Update Composer dependencies ([17bfa64](https://github.com/timber/teak/commit/17bfa64a01ff1aee97f251130634d018aae45bb3))

## 1.0.5 - 2022-02-07

- Updated reflection dependencies.

## 1.0.4 - 2020-11-19

- Fix regression bug with whitespace before headings.

## 1.0.3 - 2020-11-14

- Add some spans to property and method tables to make it easier to style them.

## 1.0.2 - 2020-05-10

- Improved how references to other classes are handled.
- Removed anchor sanitization that’s specific to a certain markdown compiler.
- Removed `--front_matter_parent` option.
- Added basis for tests.

## 1.0.1 - 2019-05-09

Various improvements and bugfixes.

## 1.0.0 - 2019-04-05

Initial release.
