<?php

namespace Modules\UITwix;

use APP, CMenu, CMenuItem;
use CController as Action;
use Zabbix\Core\CModule;
use Modules\UITwix\Services\Preferences;

class Module extends CModule {

    /**
     * @var Preferences $preferences
     */
    protected Preferences $preferences;

    public function getAssets(): array {
        $assets = parent::getAssets();
        $action = APP::Component()->router->getAction();
        $preferences = $this->preferences->get();

        if ($action === 'mod.uitwix.form') {
            $assets['js'][] = 'twix-userform.js';
            $assets['css'][] = 'uitwix.css';
        }

        if ($preferences['state']['sticky']) {
            $assets['js'][] = 'twix-sticky.js';
        }

        if ($preferences['state']['windrag'] || $action === 'mod.uitwix.form') {
            $assets['js'][] = 'twix-dragm.js';
        }

        if ($preferences['state']['bodybg']) {
            zbx_add_post_js("document.documentElement.setAttribute('uitwix-coloring-body', 'on')");
        }

        if ($preferences['state']['asidebg']) {
            zbx_add_post_js("document.documentElement.setAttribute('uitwix-coloring-sidebar', 'on')");
        }

        if ($preferences['state']['css'] || $preferences['state']['colortags']
                || $preferences['state']['bodybg'] || $preferences['state']['asidebg']) {
            $assets['css'][] = '../../../../zabbix.php?action=mod.uitwix.css';
        }

        if ($preferences['state']['syntax'] || $action === 'mod.uitwix.form') {
            $assets['js'] = array_merge($assets['js'], [
                'twix-ace.js', 'ace.1.5.0/ace.js', 'ace.1.5.0/ext-language_tools.js', 'ace.1.5.0/worker-base.js',
                'ace.1.5.0/worker-javascript.js', 'ace.1.5.0/mode-javascript.js', 'ace.1.5.0/worker-css.js',
                'ace.1.5.0/mode-css.js', 'ace.1.5.0/theme-twilight.js'
            ]);
        }

        return $assets;
    }

    public function init(): void {
        $this->preferences = new Preferences;
        $this->registerMenuEntry();
    }

    public function onBeforeAction(Action $action): void {
    }

    public function onTerminate(Action $action): void {
    }

    protected function registerMenuEntry() {
        /** @var CMenu $menu */
        $menu = APP::Component()->get('menu.user');
        $menu
            ->find(_('User settings'))
            ->getSubMenu()
                ->insertAfter(_('Profile'), (new CMenuItem(_('UI Twix')))->setAction('mod.uitwix.form'));
    }
}
