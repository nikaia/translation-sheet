#!/usr/bin/env bash

set -e

ILLUMINATE_VERSION=${1?param missing illuminate version ("5.5.*")}

composer require "illuminate/support:${ILLUMINATE_VERSION}" --no-update -v
composer require "illuminate/console:${ILLUMINATE_VERSION}" --no-update -v
composer require "illuminate/filesystem:${ILLUMINATE_VERSION}" --no-update -v


if [ "$ILLUMINATE_VERSION" == "5.5.*" ]; then composer require  "orchestra/testbench:^3.5" --dev --no-update -v; fi
if [ "$ILLUMINATE_VERSION" == "5.6.*" ]; then composer require  "orchestra/testbench:^3.6" --dev --no-update -v; fi
if [ "$ILLUMINATE_VERSION" == "5.7.*" ]; then composer require  "orchestra/testbench:^3.7" --dev --no-update -v; fi

composer update --no-interaction --prefer-source -vvv

