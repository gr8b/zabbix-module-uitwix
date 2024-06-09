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
        ],
        'colortags' => [
            "class\n1\n#ff0000"
        ]
    ];

    public function getAssets(): array
    {
        $assets = parent::getAssets();

        if (($_GET['action']??'') === 'userprofile.edit') {
            $assets['js'][] = 'twix-userform.js';
        }

        return $assets;
    }

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
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim(substr($path, 0, strrpos($path, '/')), '/');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie('uitwix', $profile, 0, $path);
        }

        $preferences['state'] = array_merge($preferences['state'], array_fill_keys(explode('-', $cookie?:$profile), 1));

        $profile = CProfile::get('uitwix-coloring', '');
        $cookie = CCookieHelper::get('uitwix-coloring');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix-coloring', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie('uitwix-coloring', $profile, 0, $path);
        }

        $colors = [];

        foreach (explode('-', $cookie?:$profile) as $color) {
            [$key, $value] = explode(':', $color) + ['', ''];
            $colors[$key] = $value;
        }

        $preferences['color'] = array_merge($preferences['color'], $colors);

        // Color tags.
        $profile = CProfile::get('uitwix-colortags', '');
        $cookie = CCookieHelper::get('uitwix-colortags');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix-colortags', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie('uitwix-colortags', $profile, 0, $path);
        }

        $preferences['colortags'] = [];
        $colortags = explode("\n", $cookie?:$profile);

        foreach (array_chunk($colortags, 3) as $colortag) {
            [$string, $match, $color] = $colortag + ['', '', ''];

            if ($string !== '' && $match && $color !== '') {
                $preferences['colortags'][] = compact('string', 'match', 'color');
            }
        }

        if (!$preferences['colortags']) {
            $preferences['colortags'][] = ['string' => '', 'match' => 1, 'color' => '#ff0000'];
        }

        return $preferences;
    }
}