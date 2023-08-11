# typo3-consentbanners

## Installation
In der Root composer des TYPO3-Projekts das Repository angeben:
~~~
   "repositories": [
        {
            "type": "path",
            "url": "local_packages/*"
        },
        {
            "type": "vcs",
            "url":  "https://github.com/berbach/typo3-consentbanners.git"
        }
    ],
~~~
und dann mit 
~~~
composer req bb/consentbanners:v12.x-dev --prefer-dist
~~~
installieren. `prefer-dist` sorgt dafür, dass keine unnötigen Dateien mit installiert werden. Dies ist für TYPO3 v12.
