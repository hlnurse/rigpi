#!/bin/bash
# phploc: https://github.com/sebastianbergmann/phploc
# phpmd: https://github.com/phpmd/phpmd
# phpcpd: https://github.com/sebastianbergmann/phpcpd
# pdepend: https://github.com/pdepend/pdepend
# php-cs-fixer: https://github.com/fabpot/PHP-CS-Fixer

phploc src/ > METRICS
phpmd src/ text codesize,unusedcode,naming >> METRICS
phpcpd src/ >> METRICS
pdepend src/ >> METRICS
php-cs-fixer fix src/ --dry-run >> METRICS
cat METRICS
rm METRICS

