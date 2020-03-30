<?php

namespace oat\taoClientRestrict\model\classManager;

use oat\taoClientRestrict\model\detection\OsClassService;

/**
 * Class OsClassManager
 *
 * @package oat\taoClientRestrict\model\classManager
 */
class OsClassManager extends ClassManager
{
    public const PROPERTY_CLASS = OsClassService::ROOT_CLASS;
    public const PROPERTY_NAME = OsClassService::PROPERTY_NAME;
    public const PROPERTY_VERSION = OsClassService::PROPERTY_VERSION;

    /**
     * @return string
     */
    public function getRootProperty(): string
    {
        return self::PROPERTY_CLASS;
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
