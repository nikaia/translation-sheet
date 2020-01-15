# Release



## Version system

Versions are handled using `standard-version` node package.
This allows generating automatically version and changelog.

> This package has version named after the laravel main version.
(ie. 5.7). This implies that all versions that will be published in
a branch will be minor versions (x.x.X).

## Commit / style

- Commit the changes to the current branch. 
- When commit use the standard version commit styles. This is required and helps generating the changelog.
    - prefix with `feat:` for a feature.
    - prefix with `fix:` for a fix.
    - prefix with `test:` for a test.

More info here : https://www.conventionalcommits.org/en/v1.0.0-beta.3/

## Publishing a new path version

- run `yarn release-patch-dryrun` and check if the result is ok.
- run `yarn release-patch`
- run `git push --follow-tags origin HEAD`


## Publishing a new minor version
- run `yarn release-minor`


## Publishing a new minor version
- run `yarn release-major`

