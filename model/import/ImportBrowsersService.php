<?php

namespace oat\taoClientRestrict\model\import;

use oat\taoClientRestrict\model\detection\BrowserClassService;

/**
 * Class ImportBrowsersService
 *
 * @package oat\taoClientRestrict\model\import
 */
class ImportBrowsersService extends Importer
{
    public const SERVICE_ID = 'taoClientRestrict/ImportBrowsersService';

    /** @var BrowserClassService */
    private $classService;

    /**
     * @return BrowserClassService
     */
    protected function getClassService()
    {
        if (!$this->classService) {
            $this->classService = BrowserClassService::singleton();
        }

        return $this->classService;
    }

    /**
     * @return string
     */
    protected function getPropertyName(): string
    {
        return BrowserClassService::PROPERTY_NAME;
    }

    /**
     * @return string
     */
    protected function getPropertyVersion(): string
    {
        return BrowserClassService::PROPERTY_VERSION;
    }
}
