#!/usr/bin/env bash
BIN_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )
PKG_DIR=$(dirname $BIN_DIR)
SRC_DIR="$PKG_DIR/src"

# PHP
phpcsfixer_config="$PKG_DIR/.php_cs"
phpcsfixer="$PKG_DIR/vendor/bin/php-cs-fixer fix --config=$phpcsfixer_config"

echo "[ PHP  Linting] target : $PKG_DIR/src"
$phpcsfixer $PKG_DIR/src

echo "[ PHP  Linting] target : $PKG_DIR/config"
$phpcsfixer $PKG_DIR/config