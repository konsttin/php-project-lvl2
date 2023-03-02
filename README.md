## Differ utility

[![Actions Status](https://github.com/konsttin/php-project-lvl2/workflows/hexlet-check/badge.svg)](https://github.com/konsttin/php-project-lvl2/actions)
[![PHP CI](https://github.com/konsttin/php-project-lvl2/actions/workflows/workflow.yml/badge.svg)](https://github.com/konsttin/php-project-lvl2/actions/workflows/workflow.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/d39302772ccba220f546/maintainability)](https://codeclimate.com/github/konsttin/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d39302772ccba220f546/test_coverage)](https://codeclimate.com/github/konsttin/php-project-lvl2/test_coverage)


Utility compares json or yml files. Output can be displayed in formats:
- stylish
- plain
- json

## Requirements
* PHP >= 8.1
* [Composer](https://getcomposer.org/)

## Install
```sh
$ git clone git@github.com:konsttin/php-project-lvl2.git
$ cd php-project-lvl2/
$ make install
```

## Usage
gendiff (-h | --help)

gendiff (-v | --version)

gendiff [--format <fmt>] <firstFile> <secondFile>

## Options
-h --help &emsp;&emsp; Show this screen

-v --version &emsp; Show version

--format <fmt> &emsp; Report format [default: stylish]

## Examples of usage
```sh
$  ./bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json -f stylish

$  ./bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json -f plain

$  ./bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json -f json
```
[![asciicast](https://asciinema.org/a/7f16TH0WqIlSA7a5zcI40xc37.svg)](https://asciinema.org/a/7f16TH0WqIlSA7a5zcI40xc37)
