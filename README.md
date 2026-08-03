# typo3-consentbanners

Consent-Banner-Extension für TYPO3 (Legacy-Generation).

> **Hinweis:** Dieses Repository enthält die **alte** Generation der Extension
> (Composer-Name `bb/consentbanners`, Namespace `Bb\Consentbanners\`, Extension-Key
> `consentbanners`, Tabellen `tx_consentbanners_*`).
>
> Die **neue** Generation liegt in einem eigenen Repository:
> [berbach/typo3-consent-banner](https://github.com/berbach/typo3-consent-banner)
> (`bb/consent_banner`, Namespace `Bb\ConsentBanner\`, Extension-Key `consent_banner`).
> Beide Generationen lassen sich nicht parallel betreiben — unterschiedliche
> Datenbanktabellen und Extension-Keys.

## Installation

Repository in der Root-`composer.json` des TYPO3-Projekts eintragen:

~~~json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/berbach/typo3-consentbanners.git"
    }
]
~~~

Dann die zur TYPO3-Version passende Variante installieren:

~~~bash
composer require bb/consentbanners:v13.x-dev --prefer-dist
~~~

`--prefer-dist` sorgt dafür, dass keine unnötigen Dateien mit installiert werden.

Alternativ ohne manuelles Editieren der `composer.json`:

~~~bash
composer config repositories.consentbanners vcs https://github.com/berbach/typo3-consentbanners.git
composer require bb/consentbanners:v13.x-dev --prefer-dist
~~~

## Verfügbare Versionen

Dieses Repository führt keine Tags — installiert wird immer über den Branch-Alias.

| Constraint         | Branch         | TYPO3                | PHP                 |
|--------------------|----------------|----------------------|---------------------|
| `v13.x-dev`        | `v13`          | ^13.4                | >= 8.2.0 <= 8.5.99  |
| `v12.x-dev`        | `v12`          | ^11.5.24 \|\| ^12    | >= 8.1 < 8.3        |
| `v11.x-dev`        | `v11`          | ^11                  | >= 8.1 < 8.3        |
| `dev-fix-deps-v13` | `fix-deps-v13` | ^13.4                | >= 8.2 <= 8.4       |
| `dev-fix-deps-v12` | `fix-deps-v12` | ^11.5.24 \|\| ^12    | >= 8.1 < 8.3        |

Die beiden `fix-deps-*`-Branches enthalten behobene Dependabot-Alerts der Build-Toolchain
und sind noch nicht in `v12`/`v13` gemergt.

`v13.x-dev` und `dev-v13` sind gleichwertig — Composer leitet den Alias `v13.x-dev`
automatisch aus dem Branchnamen `v13` ab.

## Voraussetzungen

Die PHP- und TYPO3-Angaben der Tabelle oben werden von Composer erzwungen. Passt die
PHP-Version der Umgebung nicht, bricht die Auflösung mit einer `requires php ...`-Meldung
ab.

Für `v13` ist PHP 8.2 bis 8.5 zulässig. Die tatsächliche Untergrenze setzt allerdings
TYPO3 selbst: `typo3/cms-core` in der Version 13.4 verlangt `php ^8.2`.

Weicht die von Composer angenommene PHP-Version von der tatsächlichen Laufzeitversion ab,
lässt sie sich im Projekt festlegen:

~~~json
"config": {
    "platform": {
        "php": "8.3.0"
    }
}
~~~
