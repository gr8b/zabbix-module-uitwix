<?php

namespace Modules\UITwix\Services;

use CUrl;
use CControllerResponse;

class FormRedirectFilterService extends CControllerResponse {

    public static function start() {
        class_alias(static::class, '\\CControllerResponseRedirect');
    }

    public function redirect(): void {
        parse_str(strval(parse_url($this->location, PHP_URL_QUERY)), $arguments);

        if (!array_key_exists('action', $arguments) || $arguments['action'] !== 'userprofile.edit') {
            $this->location = 'zabbix.php?action=userprofile.edit';
        }

        parent::redirect();
    }

    /**
     * CControllerResponseRedirect.php code starts below.
     */

    protected $formData = [];

    /**
     * @param string|CUrl $location
     */
    public function __construct($location) {
        if ($location instanceof CUrl) {
            $location = $location->getUrl();
        }

        $this->location = $location;
    }

    public function setFormData(array $formData): void {
        $this->formData = $formData;
    }

    public function getFormData(): array {
        return $this->formData;
    }
}