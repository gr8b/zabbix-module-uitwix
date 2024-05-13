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
