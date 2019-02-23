#!/bin/bash

GIT_DIR="$( cd "$(dirname "$0")"; cd ..; pwd -P )"
GIT_HOOKS_DIR="$( cd "$GIT_DIR"; cd .git/hooks ; pwd -P )"


echo "╔═╗╔═╗╔╗╔╔╦╗╦═╗╦╔╗   ╔═╗╔═╗╔╦╗╦ ╦╔═╗"
echo "║  ║ ║║║║ ║ ╠╦╝║╠╩╗  ╚═╗║╣  ║ ║ ║╠═╝"
echo "╚═╝╚═╝╝╚╝ ╩ ╩╚═╩╚═╝  ╚═╝╚═╝ ╩ ╚═╝╩"
echo " "

# Make scripts executable
chmod +x "$GIT_DIR"/bin/lint-all.sh

# Pre Commit
cp "$GIT_DIR/bin/pre-commit.sh" "$GIT_HOOKS_DIR/pre-commit"
chmod +x "$GIT_HOOKS_DIR"/pre-commit
echo "  - Copied pre-commit hook. [$GIT_HOOKS_DIR/pre-commit]"
echo "  -> Done."

