# Differ

[![Actions Status](https://github.com/konsttin/php-project-lvl2/workflows/hexlet-check/badge.svg)](https://github.com/konsttin/php-project-lvl2/actions)
[![PHP CI](https://github.com/konsttin/php-project-lvl2/actions/workflows/workflow.yml/badge.svg)](https://github.com/konsttin/php-project-lvl2/actions/workflows/workflow.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/d39302772ccba220f546/maintainability)](https://codeclimate.com/github/konsttin/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/d39302772ccba220f546/test_coverage)](https://codeclimate.com/github/konsttin/php-project-lvl2/test_coverage)


Программа сравнивает два конфигурационных файла. 
Cli-утилита принимает через командную строку два аргумента — пути до этих файлов.

Результат сравнения файлов может выводиться в разных форматах: 
- stylish - "стандартный"
- plain - "плоский"
- json - "JSON-формат"


Помощь
```sh
$  ./bin/gendiff -h
```
Пример использования
```sh
$  ./bin/gendiff tests/fixtures/file1.json tests/fixtures/file2.json -f plain
```


## Установка

```sh
$ git clone https://github.com/konsttin/php-project-lvl2
$ cd php-project-lvl2
$ make install
```

## Запуск тестов

```sh
$ make test
```

## Демонстрация
[![asciicast](https://asciinema.org/a/7f16TH0WqIlSA7a5zcI40xc37.svg)](https://asciinema.org/a/7f16TH0WqIlSA7a5zcI40xc37)
