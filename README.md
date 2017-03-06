# jskos-rdf

[![Latest Stable Version](https://poser.pugx.org/gbv/jskos-rdf/v/stable)](https://packagist.org/packages/gbv/jskos-rdf)
[![License](https://poser.pugx.org/gbv/jskos/license)](https://packagist.org/packages/gbv/jskos-rdf)

[![Build Status](https://img.shields.io/travis/gbv/jskos-rdf.svg)](https://travis-ci.org/gbv/jskos-rdf)
[![Coverage Status](https://coveralls.io/repos/gbv/jskos-rdf/badge.svg?branch=master)](https://coveralls.io/r/gbv/jskos-rdf)

# Description

This library extends the [jskos](https://packagist.org/packages/gbv/jskos) PHP
library with RDF capabilities. It provides the following PHP classes:

* **JSKOS\RDF\RDFMapping**: maps maps RDF data to JSKOS based on a set of mapping rules
* **JSKOS\RDF\RDFMappingService.php**: extends JSKOS Service with an RDFMapping from config file

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

# Author and License

Jakob Voß <jakob.voss@gbv.de>

JSKOS-PHP is licensed under the LGPL license - see `LICENSE.md` for details.

# See alse

JSKOS-RDF is created as part of project coli-conc: <https://coli-conc.gbv.de/>.

The current specification of JSKOS is available at <http://gbv.github.io/jskos/>.

The current specification of JSKOS API is available at <http://gbv.github.io/jskos-api/>.
