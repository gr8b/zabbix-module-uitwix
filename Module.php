<?php

namespace Modules\UITwix;

use APP;
use CView;
use CController as Action;
use CCookieHelper;
use CProfile;
use CCsrfTokenHelper;
use Zabbix\Core\CModule;

use CTag;

class Module extends CModule {

    /**
     * @var CView $tmpl
     */
    protected $tmpl;
    protected $css = '';

    /**
     * @var array $preferences
     */
    protected $preferences = [
        'state' => [
            'sticky' => 0,
            'windrag' => 0,
            'bodybg' => 0,
            'asidebg' => 0,
            'css' => 0
        ],
        'color' => [
            'bodybg' => '#000000',
            'asidebg' => '#403030'
        ],
        'colortags' => [
            "class\n1\n#ff0000"
        ],
        'css' => [['action' => '', 'css' => '']]
    ];

    public function getAssets(): array {
        $assets = parent::getAssets();
        $action = APP::Component()->router->getAction();

        if ($action === 'userprofile.edit') {
            $assets['js'][] = 'twix-userform.js';
        }

        if ($this->preferences['state']['css']) {
            $assets['css'][] = '../../../../zabbix.php?action=uitwix.css';
        }

        return $assets;
    }

    public function init(): void {
        $this->preferences = $this->getUserPreferences($this->preferences);
    }

    public function onBeforeAction(Action $action): void {
        switch ($action->getAction()) {
            case 'userprofile.edit':
                $data = ['uitwix-csrf' => CCsrfTokenHelper::get('uitwix.form')] + $this->preferences;
                $this->tmpl = new CView('configuration.form', $data);

                break;

            case 'userprofile.update':
                if (CCsrfTokenHelper::check($_POST['uitwix-csrf']??'', 'uitwix.form')) {
                    $this->updateUserPreferences($_POST);
                }

                break;
        }
    }

    public function onTerminate(Action $action): void {
        if ($this->tmpl) {
            echo $this->tmpl->getOutput();
        }

        echo $this->css;
    }

    /**
     * Get user preferences.
     * Updates user profile 'uitwix' when cookie 'uitwix' value differs from profile value.
     */
    protected function getUserPreferences(array $preferences): array {
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
        $preferences['css'] = $this->getProfileArray('css', $preferences['css']);

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

    protected function updateUserPreferences(array $input): void {
        // TODO: tweaks enabled/disabled state.

        // TODO: Body background color.

        // TODO: Navigation background color.

        // Custom styles.
        $css = array_filter($input['uitwix-css']??[], fn ($css) => trim(implode('', $css)) !== '');
        $this->setProfileArray('css', array_values($css));

        // TODO: Color tags.
    }

    protected function getProfileArray(string $key, array $default_value = []): array {
        $profile = (array) json_decode(CProfile::get("uitwix-{$key}", json_encode($default_value)), true);

        return $profile;
    }

    protected function setProfileArray(string $key, array $value): void {
        $key = "uitwix-{$key}";
        $value ? CProfile::update($key, json_encode($value), PROFILE_TYPE_STR) : CProfile::delete($key);
    }
}