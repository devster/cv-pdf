#!/bin/bash

ROOT=$(dirname $0)
PHP="/usr/bin/env php"

FONTS=$(printf "%s," $(ls -d -1 $ROOT/resources/fonts/*) | sed 's/.$//')

$PHP $ROOT/vendor/tecnick.com/tcpdf/tools/tcpdf_addfont.php -i "${FONTS}"
