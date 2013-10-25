Google Visualization PHP
========================

[![Build Status](https://travis-ci.org/vube/google-visualization-php.png?branch=master)](https://travis-ci.org/vube/google-visualization-php)
[![Coverage Status](https://coveralls.io/repos/vube/google-visualization-php/badge.png?branch=master)](https://coveralls.io/r/vube/google-visualization-php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/vube/google-visualization-php/v/stable.png)](https://packagist.org/packages/vube/google-visualization-php)
[![Dependency Status](https://www.versioneye.com/user/projects/526595aa632bac5e560019b6/badge.png)](https://www.versioneye.com/user/projects/526595aa632bac5e560019b6)

Google Visualization DataSource implemented in PHP

This PHP library allows you to implement Google Visualization DataSource queries in PHP.


Features
--------

- Use PHP endpoints in Apache or Nginx as a DataSource for Google Charts
- Limited query language support
    - select columns
    - pivot


Limitations
-----------

- The full query language is not yet supported.



Installation
------------

Load google-visualization-php into your project by adding the following lines to your `composer.json`

``` json
{
    "require": {
        "vube/google-visualization-php": ">=0.2"
    }
}
```


Dependencies
------------

- PHP 5.3.2+
- Composer


References
----------

* Based very closely on [google-visualization-java](https://code.google.com/p/google-visualization-java/source/browse/trunk/src/main/java/com/google/visualization/datasource/) with some modifications to make it work in PHP 5.3.2+
