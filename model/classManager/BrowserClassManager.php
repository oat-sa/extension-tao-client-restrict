<?php

namespace oat\taoClientRestrict\model\classManager;

use oat\taoClientRestrict\model\detection\BrowserClassService;

/**
 * Class BrowserClassManager
 *
 * @package oat\taoClientRestrict\model\classManager
 */
class BrowserClassManager extends ClassManager
{
    public const PROPERTY_ROOT = BrowserClassService::ROOT_CLASS;
    public const PROPERTY_NAME = BrowserClassService::PROPERTY_NAME;
    public const PROPERTY_VERSION = BrowserClassService::PROPERTY_VERSION;

    /**
     * @return string
     */
    public function getRootProperty(): string
    {
        return self::PROPERTY_ROOT;
    }

    /**
     * @return string
     */
    public function getNameProperty(): string
    {
        return self::PROPERTY_NAME;
    }

    /**
     * @return string
     */
    public function getVersionProperty(): string
    {
        return self::PROPERTY_VERSION;
    }
}
