<?php

namespace oat\taoClientRestrict\scripts\tools\import;

use oat\taoClientRestrict\model\import\ImportBrowsersService;

/**
 * Class ImportBrowsersScript
 *
 * @package oat\taoClientRestrict\scripts\tools\import
 */
class ImportBrowsersScript extends ImportScript
{
    /**
     * @return string
     */
    protected function getServiceId(): string
    {
        return ImportBrowsersService::SERVICE_ID;
    }
}
