<?php

namespace Modules\UITwix\Services;

use CProfile;
use CCookieHelper;

class Preferences {

    const PROFILE_KEY_FORMAT = 'uitwix-%1$s';
    const PROFILE_COOKIE = 'uitwix';

    const MATCH_BEGIN = 1;
    const MATCH_CONTAIN = 2;
    const MATCH_END = 3;

    public function get(): array {
        $preferences = $this->getDefault();
        $profile = CProfile::get('uitwix', '');
        $cookie = CCookieHelper::get(static::PROFILE_COOKIE);
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rtrim(substr($path, 0, strrpos($path, '/')), '/');

        if ($cookie !== null && $cookie !== $profile) {
            CProfile::update('uitwix', $cookie, PROFILE_TYPE_STR);
        }

        if ($cookie === null) {
            setcookie(static::PROFILE_COOKIE, $profile, 0, $path);
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

        // Color tags
        $preferences['colortags'] = $this->getProfileArray('colortags', $preferences['colortags']);

        // Custom .css
        $preferences['css'] = $this->getProfileArray('css', $preferences['css']);

        $key = sprintf(static::PROFILE_KEY_FORMAT, 'noredirect');
        $preferences[$key] = CProfile::get($key, 0);

        return $preferences;
    }

    public function set(array $preferences) {
        // TODO: tweaks enabled/disabled state.

        // TODO: Body background color.

        // TODO: Navigation background color.

        // Custom styles.
        $css = array_filter($preferences['uitwix-css']??[], fn ($css) => trim(implode('', $css)) !== '');
        $this->setProfileArray('css', array_values($css));

        // Color tags.
        $tags = array_filter($preferences['uitwix-colortag']??[], fn ($tag) => trim($tag['value']??'') !== '');
        $this->setProfileArray('colortags', array_values($tags));

        // Prevent redirect after user profile form successfull update.
        $key = sprintf(static::PROFILE_KEY_FORMAT, 'noredirect');
        ($preferences[$key] ?? 0)
            ? CProfile::update($key, $preferences[$key], PROFILE_TYPE_STR) : CProfile::deleteIdx($key);
    }

    public function getDefault(): array {
        return [
            'state' => [
                'sticky' => 0,
                'windrag' => 0,
                'bodybg' => 0,
                'asidebg' => 0,
                'css' => 0,
                'colortags' => 0
            ],
            'color' => [
                'bodybg' => '#000000',
                'asidebg' => '#403030'
            ],
            'colortags' => [
                ['value' => '', 'match' => Preferences::MATCH_BEGIN, 'color' => '#ff0000']
            ],
            'css' => [['action' => '', 'css' => '']]
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
