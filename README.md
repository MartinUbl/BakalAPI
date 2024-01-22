# BakalAPI

BakalAPI je minimalistické API pro čtení údajů ze školního systému Bakaláři skrze jeho [API (v3)](https://github.com/bakalari-api/bakalari-api-v3). Knihovna je ve fázi vývoje, je tedy možné, že se objeví chyby nebo nestandardní chování. V případě, že chybu objevíte, můžete ji nahlásit zde v issue trackeru.

Pokud byste chtěli do vývoje přispět, nebojte se repozitář forknout, upravit a otevřít pull request. Jakékoliv příspěvky jsou vítány.

## Závislosti

BakalAPI lze provozovat na PHP 7.1 a vyšší. Přímou závislostí je pak [Guzzle](https://github.com/guzzle/guzzle), který je použitý pro provádění HTTP requestů - composer si jej nainstaluje sám.

## Instalace

Pro instalaci můžete použít například composer:

```
composer require martinubl/bakalapi
```

Mějte na paměti, že zatím neexistuje stabilní verze. Upravte proto prosím svůj `composer.json` tak, aby vlastnost `minimum-stability` byla nastavena na `dev`.

## Licence

TBD, pravděpodobně MIT