<?php

namespace Modules\{ZBX_NAMESPACE};

use CController as Action;
use Zabbix\Core\CModule;

class Module extends CModule {

    public function init(): void
    {
        // Module initialization actions.
    }

    public function onBeforeAction(Action $action): void
    {
        // Action to take before any action is called.
    }

    public function onTerminate(Action $action): void
    {
        // Action to take after content is sent to browser.
    }
}