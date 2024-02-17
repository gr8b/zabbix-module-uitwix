<?php

namespace Modules\UITwix;

use CView;
use CController as Action;
use CCookieHelper;
use CProfile;
use Zabbix\Core\CModule;

class Module extends CModule {

    /**
     * @var CView $tmpl
     */
    protected $tmpl;

    /**
     * @var array $preferences
     */
    protected $preferences = [
        'sticky' => 0,
        'windrag' => 0
    ];

    public function init(): void
    {
        $this->preferences = array_merge($this->preferences, $this->getUserPreferences());
    }

    public function onBeforeAction(Action $action): void
    {
        if ($action->getAction() === 'userprofile.edit') {
            $this->tmpl = new CView('configuration.form', $this->preferences);
        }
    }

    public function onTerminate(Action $action): void
    {
        if ($this->tmpl) {
            echo $this->tmpl->getOutput();
        }
    }

    /**
     * Get user preferences.
     * Updates user profile 'uitwix' when cookie 'uitwix' value differs from profile value.
     */
    protected function getUserPreferences(): array
    {
        $profile = CProfile::get('uitwix');
        $cookie = CCookieHelper::get('uitwix');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie('uitwix', $profile);
        }

        return array_fill_keys(explode('-', $cookie?:$profile), 1);
    }
}