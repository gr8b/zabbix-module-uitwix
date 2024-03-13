## Manual builds

To create release manually `GH_TAG_PUSH` secret should be created on repository level. `GH_TAG_PUSH` should contain personal access token with permissions:

- `repo`:
  - `repo:status`
  - `repo_deployment`
  - `public_repo`

To create PAT token go to *Settings ⇒ Developers ⇒ Personal access tokens ⇒ Tokens (classic)*. \
Once the token is generated, copy it and go to repository *Settings ⇒ Secrets and variables ⇒ Actions* and create new repository secret with name `GH_TAG_PUSH` with value of generated PAT. \
When secret is added can go to repository *Actions* and run *Create tag*, action will check main branch commits and push new release tag.

Tag will be generated using module version from `manifest.json` and checking commits till previous tag: \
`MINOR` version number will be increased when at least one commit with prefix `feat:` exists, also `PATCH` number will be set to 0. \
`PATCH` version number will be increased when at least one commit with prefix `fix:` exists.

## Automatic builds

An automatic build will be triggered upon the push of a tag.
A module `.zip` file for Zabbix 6.2 and older will be generated when the `manifest.5.0.json` file is present in the root of the repository.
A module `.zip` file for Zabbix 6.4 and newer will be generated when the `manifest.6.4.json` file is present in the root of the repository.

To invoke a module-specific build step, a `composer.json` script named '**build**' should be defined. Script is called for each release version build.
```json
  "scripts": {
    "build": [
      "echo 'hello from build task'",
      "composer --dump-autoload"
    ]
  }
```

## Versioning

The module version uses only the MINOR and PATCH parts of semantic versioning.
The build tag should contain only `minor.patch` parts; it is allowed to be prefixed with the `v` character.

## Release notes generation

Release notes are generated only when the `RELEASE_NOTES.md` file is present in the root of the repository. An example can be found in the `.github/templates` directory.
Commits with message prefixes `feat:` and `fix:` will be included in the automatically generated release notes changes list.
