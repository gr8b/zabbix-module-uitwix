<?php

namespace Modules\UITwix;

use APP, CRouter, CMenu, CMenuItem;
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
        $action = version_compare(ZABBIX_API_VERSION, '8.0.0', '<')
            ? APP::Component()->router->getAction()
            : CRouter::getInstance()->getAction();
        $preferences = $this->preferences->get();
        $js = [];
        $css = [];
        $page_url = urlencode($_SERVER['REQUEST_URI']);

        if ($action === 'mod.uitwix.form') {
            $js[] = 'twix-userform.js';
            $css[] = 'uitwix.css';
        }

        if ($preferences['state']['sticky']) {
            $js[] = 'twix-sticky.js';
        }

        if ($preferences['state']['windrag'] || $action === 'mod.uitwix.form') {
            $js[] = 'twix-dragm.js';
        }

        if ($preferences['state']['bodybg']) {
            zbx_add_post_js("document.documentElement.setAttribute('uitwix-coloring-body', 'on')");
        }

        if ($preferences['state']['asidebg']) {
            zbx_add_post_js("document.documentElement.setAttribute('uitwix-coloring-sidebar', 'on')");
        }

        if ($preferences['state']['css'] || $preferences['state']['colortags']
                || $preferences['state']['bodybg'] || $preferences['state']['asidebg']) {
            // $assets['css'][] = '../../../../zabbix.php?action=mod.uitwix.assets&type=css&uri='
            //     .urlencode($_SERVER['REQUEST_URI']);
            $css[] = 'uitwix.css';
        }

        if ($preferences['state']['syntax'] || $action === 'mod.uitwix.form') {
            $js = array_merge($js, [
                'twix-ace.js', 'ace.1.5.0/ace.js', 'ace.1.5.0/ext-language_tools.js', 'ace.1.5.0/worker-base.js',
                'ace.1.5.0/worker-javascript.js', 'ace.1.5.0/mode-javascript.js', 'ace.1.5.0/worker-css.js',
                'ace.1.5.0/mode-css.js', 'ace.1.5.0/theme-twilight.js'
            ]);
        }

        if ($js) {
            $assets['js'][] = '../../../../zabbix.php?action=mod.uitwix.assets&type=js&files='.implode(',', $js);
        }
        if ($css) {
            $assets['css'][] = '../../../../zabbix.php?action=mod.uitwix.assets&type=css&files='.implode(',', $css).'&uri='.$page_url;

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
