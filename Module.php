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
        'state' => [
            'sticky' => 0,
            'windrag' => 0,
            'bodybg' => 0,
            'asidebg' => 0
        ],
        'color' => [
            'bodybg' => '#000000',
            'asidebg' => '#403030'
        ]
    ];

    public function init(): void
    {
        $this->preferences = $this->getUserPreferences($this->preferences);
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
    protected function getUserPreferences(array $preferences): array
    {
        $profile = CProfile::get('uitwix', '');
        $cookie = CCookieHelper::get('uitwix');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie('uitwix', $profile, 0, '/');
        }

        $preferences['state'] = array_merge($preferences['state'], array_fill_keys(explode('-', $cookie?:$profile), 1));

        $profile = CProfile::get('uitwix-coloring', '');
        $cookie = CCookieHelper::get('uitwix-coloring');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix-coloring', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie('uitwix-coloring', $profile, 0, '/');
        }

        $colors = [];

        foreach (explode('-', $cookie?:$profile) as $color) {
            [$key, $value] = explode(':', $color) + ['', ''];
            $colors[$key] = $value;
        }

        $preferences['color'] = array_merge($preferences['color'], $colors);

        return $preferences;
    }
}