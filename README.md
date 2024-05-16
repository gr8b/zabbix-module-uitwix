## UI Twix

![](doc/user-profile.png)

[![Latest Release](https://img.shields.io/github/v/release/gr8b/zabbix-module-uitwix)](https://github.com/gr8b/zabbix-module-uitwix/releases)

### Tweaks

- Enable filters to remain visible within the viewing area when scrolling through tables with extensive data.
- Allow users to manually reposition modal windows within the body area.\
  _Please note that manually changing the position of the modal window is not saved._
- Modify main navigation color scheme and background color.

### Development

Clone repository, run `composer install` to initialize composer packages, then can use `composer run dev-watch --timeout 0` to rebuild `.css` automatically when `assets/less/uitwix.less` file is changed.

### Composer packages

- **wikimedia/less.php**: ^4.2

### Installation

- Download the zip file and extract it in your Zabbix module path for RedHat this is /usr/share/zabbix/modules
- Extrac the zip file into a new folder
  - Ex: zabbix-modules-uitwix
- Add proper permissions to the folder Ex:
  - Ex: chown -R apache: zabbix-modules-uitwix
  - chmod -R 744 zabbix-modules-uitwix
- Go to the Zabbix UI to Administration - General - Modules
  - Press ```Scan directory``` in the upper right corner of the screen
  - There should now be a new module with the name UITwix press enable so the module becomes active
- Go to User settings - Profile
  - There is now an extra tab UI Twix where you can change the settings you like 
