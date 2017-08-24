# jskos-rdf

[![Latest Version](https://img.shields.io/packagist/v/gbv/jskos-rdf.svg?style=flat-square)](https://packagist.org/packages/gbv/jskos)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/gbv/jskos-rdf.svg?style=flat-square)](https://travis-ci.org/gbv/jskos-rdf)
[![Coverage Status](https://img.shields.io/coveralls/gbv/jskos-rdf/master.svg?style=flat-square)](https://coveralls.io/r/gbv/jskos-rdf)
[![Quality Score](https://img.shields.io/scrutinizer/g/gbv/jskos-rdf.svg?style=flat-square)](https://scrutinizer-ci.com/g/gbv/jskos-rdf)
[![Total Downloads](https://img.shields.io/packagist/dt/gbv/jskos-rdf.svg?style=flat-square)](https://packagist.org/packages/gbv/jskos)

# Description

This library extends the [jskos](https://packagist.org/packages/gbv/jskos) PHP
library with RDF capabilities. It provides the following PHP classes:

* **JSKOS\RDF\RDFMapping**: maps maps RDF data to JSKOS based on a set of mapping rules
* **JSKOS\RDF\RDFMappingService**: extends JSKOS Service with an RDFMapping from config file

# Requirements

JSKOS-RDF requires PHP 7, the [jskos](https://packagist.org/packages/gbv/jskos)
PHP library, and [easyRDF](http://www.easyrdf.org/).

# Installation

Install the latest version with composer:

~~~bash
composer require gbv/jskos-rdf
~~~

This will automatically create `composer.json` for your project (unless it already exists) and add jskos as dependency. Composer also generates `vendor/autoload.php` to get autoloading of all dependencies: 

~~~php
require_once __DIR__ . '/vendor/autoload.php';

$mapping = new JSKOS\RDF\RDFMapping($rules);
~~~

# Contributung

Bugs and feature request are [tracked on GitHub](https://github.com/gbv/jskos-rdf/issues).

See `CONTRIBUTING.md` of repository [jskos-php](https://packagist.org/packages/gbv/jskos) for guidelines.

# Author and License

Jakob Voß <jakob.voss@gbv.de>

JSKOS-PHP is licensed under the LGPL license (see `LICENSE` for details).

# See alse

JSKOS-RDF is created as part of project coli-conc: <https://coli-conc.gbv.de/>.

The current specification of JSKOS is available at <http://gbv.github.io/jskos/>.
