# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 4.2.4 (2023-11-12)

### Fixed

- Fix: certain reCAPTCHAs not working correctly in Firefox.

## 4.2.3 (2022-11-20)

### Added

- New: PrestaShop 8 compatibility.

### Fixed

- Fix: redirect to wishlist ajax page when validations would fail for certain forms.
- Fix: validation error message not being displayed after submitting the product review form.

### Changed

- Change: The validation error messages will be displayed using the native notification system for PS 1.7/8.

## 4.2.2 (2022-09-07)

### Fixed

- Fix: Browser library namespace & compatibility

# Legacy changelog

## 4.2.1 (2022-08-31)

- Improved reset password form detection
- Improved ajax newsletter detection
- Improved ajax newsletter form detection
- Updated some translations

## 4.2.0 (2021-10-04)

- Updated module for PS 1.7.8 compatibility
- Updated admin bootstrap and layout
- Added small but important option: load reCAPTCHA when needed
- Updated some settings' texts for better understanding
- Updated logs layout width for better readability
- Added colors to form names for easier identification
- Added Swedish translations
- Fixed validation rejection redirect bug on older PS versions

## 4.1.2 (2021-03-17)

- Fixed rare bug for PS 1.5
- Updated translations

## 4.1.1 (2021-01-21)

- Fix reCAPTCHA bug with the checkout login form (1.7.7)

## 4.1.0 (2020-12-10)

- Updated for PrestaShop 1.7.7
- Updated to be compatible with the ajax newsletter change
- Updated to be compatible with the official GDPR module
- Fixed issue where reCAPTCHA v3 would not load for some themes
- Added the option to log only failed validations
- Added the option to disable logs entirely
- Implemented link to delete logs periodically using scheduled tasks/cron jobs
- Updated translations
- General bug fixes and performance improvements

## 4.0.0 (2020-08-30)

- Updated to v4
- Updated the admin UI, moved all the validation settings into one section for ease of use
- Removed a lot of unnecessary on/off switches for different validations
- Moved all the form specific validation settings into one section for ease of use
- Added two new forms to reCAPTCHA validations: login and reset password
- Added new setting: hide the reCAPTCHA widget and display the legal links in the footer instead
- Added new setting: enable/disable email validation against temporary email providers like mail.ru, qq.com etc.
- Updates message validations: validations are now case insensitive
- Revamped validation logs, successful validations will now be logged as well
- Added the option to delete individual logs
- Implemented proper pagination and filters for the logs section
- reCAPTCHA API responses are now saved in the logs
- Logs are now multi-shop compliant, previous logs will be associated with the default shop
- Reworked front-end and back-end logic for a much more easier integration with custom forms
- Improved compatibility with 3rd party modules that also use the reCAPTCHA library
- The reCAPTCHA widget will now apply to forms regardless of the page
- The reCAPTCHA widget will now apply to multiple forms of the same type on the same page
- Added preview mode setting to allow admins to safely test the module integration with their shop
- Added new setting: enable/disable email validation against disposable email providers like mail.ru, qq.com etc.

## 3.0.3 (2019-12-18)

- Fix forbidden char error

## 3.0.2 (2019-11-01)

- Update compatibility with new product comments/reviews module (for PS 1.7.6 or greater)
- Add compatibility for new PS email alerts module (old mail alerts module)

## 3.0.1 (2019-07-04)

- Updated translations

## 3.0.0 (2019-06-15)

- Added reCAPTCHA v3 as an option
- Added feedback error messages
- Performance improvements

## 2.1.3 (2019-05-01)

- Updated translations

## 2.1.2 (2019-02-14)

- Fixed JS null bug

## 2.1.1 (2019-02-04)

- Updated translations

## 2.1.0 (2019-01-22)

- Added support for mailalerts' "Availability alert" form
- Fixed reCAPTCHA v2 not refreshing for ajax forms
- UI improvement and updated translations

## 2.0.14 (2019-01-09)

- Fixed very rare JS bug

## 2.0.13 (2019-01-04)

- Fixed email logs display in BO

## 2.0.12 (2018-12-18)

- Maintenance

## 2.0.11 (2018-12-17)

- Updated compatibility for "product comments" & "send to a friend" modules (maintained by mypresta)
- Removed padding added by Invisible reCAPTCHA badge
- Improved performance

## 2.0.10 (2018-12-12)

- Updated some translations

## 2.0.9 (2018-12-08)

- Improved compatibility with custom themes (Zro Market Theme)
- Disabled newsletter mobile submit button for reCAPTCHA validation (1.7 themes)
- Fixed and updated BO translations

## 2.0.8 (2018-10-17)

- Maintenance

## 2.0.7 (2018-06-02)

- Added more languages
- Minor bugfixes

## 2.0.6 (2018-05-27)

- More languages were added

## 2.0.5 (2018-05-27)

- Spanish translation was added

## 2.0.4 (2018-05-20)

- Small js bugfix
- Bugfix for legacy PHP versions

## 2.0.3 (2018-05-16)

- Bugfix for 1.5

## 2.0.2 (2018-05-08)

- Bug fixes

## 2.0.1 (2018-05-02)

- Added mail.ru aliases: inbox.ru, list.ru, bk.ru
- Fixed missing translations in back-office for a few form messages
- Fixed module admin controllers not showing in permissions table in back-office
- Added mail.ru aliases to email guard: inbox.ru, list.ru and bk.ru
- Fixed some missing translations in back-office form confirmation messages

## 2.0.0 (2018-04-29)

- Massive update: version 2.0
- Now compatible with PrestaShop 1.5 (yay!)
- Fresh new admin UI
- Added message guard for contact and product review messages
- Added Google reCAPTCHA
- Added Google Invisible reCAPTCHA
- Added reCAPTCHA customization options
- Improved logger: better details + pagination
- Added logger for each of the 3 protection methods: Email guard, Message guard and reCAPTCHA

## 1.0.2 (2018-03-03)

- Bug fixes & improvements
- Translations

## 1.0.1 (2018-02-22)

- Released
