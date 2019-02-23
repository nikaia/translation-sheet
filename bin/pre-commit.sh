#!/bin/sh

PKG_DIR="$( cd "$(dirname "$0")"; cd ..; cd ..; pwd -P )"

phpcsfixer_config="$PKG_DIR/.php_cs"
phpcsfixer="$PKG_DIR/vendor/bin/php-cs-fixer fix --config=$phpcsfixer_config"

echo "┌─┐┬ ┬┌─┐┌─┐┌─┐┌─┐┬─┐ ┬"
echo "├─┘├─┤├─┘│  └─┐├┤ │┌┴┬┘"
echo "┴  ┴ ┴┴  └─┘└─┘└  ┴┴ └─"

git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        $phpcsfixer  "$line";
        git add "$line";
    done

