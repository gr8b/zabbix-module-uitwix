<?php

namespace Modules\UITwix;

use APP;
use CView;
use CController as Action;
use CCsrfTokenHelper;
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

        if ($action === 'userprofile.edit') {
            $assets['js'][] = 'twix-userform.js';
        }

        if ($preferences['state']['css']) {
            $assets['css'][] = '../../../../zabbix.php?action=uitwix.css';
        }

        return $assets;
    }

    public function init(): void {
        $this->preferences = new Preferences;
    }

    public function onBeforeAction(Action $action): void {
        if ($action->getAction() === 'userprofile.update'
                && CCsrfTokenHelper::check($_POST['uitwix-csrf']??'', 'uitwix.form')) {
            $this->preferences->set($_POST);
        }
    }

    public function onTerminate(Action $action): void {
        if ($action->getAction() === 'userprofile.edit') {
            $data = ['uitwix-csrf' => CCsrfTokenHelper::get('uitwix.form')] + $this->preferences->get();
            echo (new CView('configuration.form', $data))->getOutput();
        }
    }
}
