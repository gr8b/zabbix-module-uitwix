<?php

namespace Modules\UITwix\Services;

use CProfile;

class Preferences {

    const PROFILE_KEY_FORMAT = 'uitwix-%1$s';
    const PROFILE_COOKIE = 'uitwix';

    const MATCH_BEGIN = 1;
    const MATCH_CONTAIN = 2;
    const MATCH_END = 3;

    public function get(): array {
        $preferences = $this->getDefault();

        // Enabled UI Twix preferences.
        $enabled_keys = array_filter(explode('-', CProfile::get('uitwix', '')), 'strlen');
        $enabled_keys = array_fill_keys($enabled_keys, 1);
        $enabled_keys = array_intersect_key($enabled_keys, $preferences['state']);
        $preferences['state'] = array_merge($preferences['state'], $enabled_keys);

        // Colors for aside and background.
        $colors = [];
        foreach (explode('-', CProfile::get('uitwix-coloring', '')) as $color) {
            if (strpos($color, ':') === false) {
                continue;
            }

            [$key, $value] = explode(':', $color) + ['', ''];
            $colors[$key] = $value;
        }

        $preferences['color'] = array_merge($preferences['color'], $colors);

        // Custom styles.
        $preferences['css'] = $this->getProfileArray('css', $preferences['css']);

        // Color tags.
        $preferences['colortags'] = $this->getProfileArray('colortags', $preferences['colortags']);

        return $preferences;
    }

    public function set(array $preferences) {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim(substr($path, 0, strrpos($path, '/')), '/');

        // Enabled UI Twix preferences.
        $state = implode('-', array_keys(array_filter($preferences['state']??[], 'boolval')));
        CProfile::update('uitwix', $state, PROFILE_TYPE_STR);
        setcookie(static::PROFILE_COOKIE, $state, 0, $path);

        // Colors for aside and background.
        $colors = [];
        foreach ($preferences['color']??[] as $name => $value) {
            $colors[] = sprintf('%s:%s', $name, $value);
        }
        CProfile::update('uitwix-coloring', implode('-', $colors), PROFILE_TYPE_STR);
        setcookie('uitwix-coloring', implode('-', $colors), 0, $path);

        // Custom styles.
        $css = array_filter($preferences['css']??[], fn ($css) => trim(implode('', $css)) !== '');
        $this->setProfileArray('css', array_values($css));

        // Color tags.
        $tags = array_filter($preferences['colortag']??[], fn ($tag) => trim($tag['value']??'') !== '');
        $this->setProfileArray('colortags', array_values($tags));
    }

    public function getDefault(): array {
        return [
            'state' => [
                'sticky' => 0,
                'windrag' => 0,
                'bodybg' => 0,
                'asidebg' => 0,
                'css' => 0,
                'colortags' => 0,
                'syntax' => 0
            ],
            'color' => [
                'bodybg' => '#000000',
                'asidebg' => '#403030'
            ],
            'colortags' => [
                ['value' => '', 'match' => Preferences::MATCH_BEGIN, 'color' => '#ff0000']
            ],
            'css' => [['action' => '', 'css' => '']],
            'syntax' => [
                'theme' => 'auto'
            ]
        ];
    }

    protected function getProfileArray(string $key, array $default_value = []): array {
        $key = sprintf(static::PROFILE_KEY_FORMAT, $key);
        $profile = (array) json_decode(CProfile::get($key, json_encode($default_value)), true);

        return $profile;
    }

    protected function setProfileArray(string $key, array $value): void {
        $key = sprintf(static::PROFILE_KEY_FORMAT, $key);
        $value ? CProfile::update($key, json_encode($value), PROFILE_TYPE_STR) : CProfile::delete($key);
    }
}
