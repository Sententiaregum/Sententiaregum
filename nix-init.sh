#!/bin/sh

# bootstrap vagrant/ruby environment and avoid an interactive shell
nix-shell --command ''

# bootstrap nodejs environment and create symlink for build commands
nix-shell npm.nix -A shell --command "ln -s '$NODE_PATH' node_modules"
