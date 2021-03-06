<p align="center">
  <img src="https://cdn.jsdelivr.net/gh/unikka/unikka.de/src/assets/unikka_with_background.svg" width="300" />
</p>

[![Packagist](https://img.shields.io/packagist/l/unikka/neos-linkchecker.svg?style=flat-square)](https://packagist.org/packages/unikka/neos-linkchecker)
![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability-percentage/Unikka/neos-linkchecker)
[![Packagist](https://img.shields.io/packagist/v/unikka/neos-linkchecker.svg?style=flat-square)](https://packagist.org/packages/unikka/neos-linkchecker)
[![Twitter Follow](https://img.shields.io/twitter/follow/unikka_de.svg?style=social&label=Follow&style=flat-square)](https://twitter.com/unikka_de)

# Neos.LinkChecker

Neos package that is able to crawl the whole page and check the links for broken links

```IMPORTANT WIP``` 

## Installation

```bash
composer require unikka/neos-linkchecker --no-update
```

We collect the result items in the database and therefore you should execute
the doctrine migration.

```bash
./flow doctrine:migrate
```

## Configuration

We have plenty of configuration options for the link checker.
the most important if you want to use the crawler for your site is maybe
the url. You can also use a parameter in the cli command if you don`t use just
one url for instance.

```yaml
Unikka:
  LinkChecker:
    url: 'unikka.de'
```

More detailed information will follow.
At the moment just check out the ```Settings.yaml```

## Contribution

We'd love you to contribute to Neos.LinkChecker. We try to make it as easy as possible.
Therefore the first rule is to follow the [eslint commit message guideline](https://github.com/conventional-changelog-archived-repos/conventional-changelog-eslint/blob/master/convention.md).
It is really easy, when you always commit via `yarn commit`. Commitizen will guide you.

If you have questions just ping us on twitter or github.

## About

The package is based on the `Noerdisch/LinkChecker` package. We thank the Noerdisch team for
all the efforts.

## License
The GNU GENERAL PUBLIC LICENSE, please see [License File](LICENSE) for more information.
