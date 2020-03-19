<?php

namespace oat\taoClientRestrict\model\import;

use oat\taoClientRestrict\model\detection\OsClassService;

/**
 * Class ImportOsService
 *
 * @package oat\taoClientRestrict\model\import
 */
class ImportOsService extends Importer
{
    public const SERVICE_ID = 'taoClientRestrict/ImportOsService';

    /** @var OsClassService */
    private $classService;

    /**
     * @return OsClassService
     */
    protected function getClassService()
    {
        if (!$this->classService) {
            $this->classService = OsClassService::singleton();
        }

        return $this->classService;
    }

    /**
     * @return string
     */
    protected function getPropertyName(): string
    {
        return OsClassService::PROPERTY_NAME;
    }

    /**
     * @return string
     */
    protected function getPropertyVersion(): string
    {
        return OsClassService::PROPERTY_VERSION;
    }
}
