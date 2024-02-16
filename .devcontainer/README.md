## How to

Use this repository as template to create your own module repository.\
In *Visual Studio Code* use "Clone repository in container volume" to create local development environment.\
Zabbix versions less than 6.0 are not supported due incompatibility with PHP8.2

## Container software

- Debian 12 bookworm
- Apache 2.4.57
- PHP 8.2.15 *(CLI and apache mod) with xdebug*
- PHPUnit 8.5.36
- Composer 2.6.6
- git 2.43.0
- nvm 0.39.7
- jq 1.6
- starship 1.17.1 *with nerd-font-symbols preset*
- gum 0.13.0 *(a11d1ff)*

## VSC extensions

- [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)
- [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug)
- [MySQL](https://marketplace.visualstudio.com/items?itemName=formulahendry.vscode-mysql)
- [Fira Code Nerd Font / Icons](https://marketplace.visualstudio.com/items?itemName=Entuent.fira-code-nerd-font)
- [ESLint](https://marketplace.visualstudio.com/items?itemName=dbaeumer.vscode-eslint)

## Notes

Git credentials should be set globally on host machine. When not set will required to configure in container console.

```sh
git config --global user.name username
git config --global user.email email@example.com
```

## Todo

- (+) start `install.sh` script only when container is created for first time
- (+) create module boilerplate if `manifest.json` is not present
- (+) if `manifest.json` is present use it version to filter out list of Zabbix branch allowed to checkout
- (+) use include path for inteliphense extension
- (+) init database and `conf/zabbix.conf.php` file
- add start/stop Zabbix server helper, make it as module copied during installation or `.bashrc` helper
- add helper to create action, view, asset file as `.bashrc` helper
