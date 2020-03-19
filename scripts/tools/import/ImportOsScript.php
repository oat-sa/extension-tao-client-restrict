<?php

namespace oat\taoClientRestrict\scripts\tools\import;

use oat\taoClientRestrict\model\import\ImportOsService;

/**
 * Class ImportOsScript
 *
 * @package oat\taoClientRestrict\scripts\tools\import
 */
class ImportOsScript extends ImportScript
{
    /**
     * @return string
     */
    protected function getServiceId(): string
    {
        return ImportOsService::SERVICE_ID;
    }
}
