<?php

namespace oat\taoClientRestrict\test\unit\classManager;

use core_kernel_classes_Class;
use oat\generis\test\TestCase;
use oat\taoClientRestrict\model\classManager\OsClassManager;

/**
 * Class OsClassManagerTest
 *
 * @package oat\taoClientRestrict\test\unit\classManager
 */
class OsClassManagerTest extends TestCase
{
    private const PROPERTY_ROOT = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OS';
    private const PROPERTY_NAME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSName';
    private const PROPERTY_VERSION = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSVersion';

    public function testBrowserClassManager(): void
    {
        $osClassManager = new OsClassManager();

        $this->assertEquals(self::PROPERTY_ROOT, $osClassManager->getRootProperty());
        $this->assertEquals(self::PROPERTY_NAME, $osClassManager->getNameProperty());
        $this->assertEquals(self::PROPERTY_VERSION, $osClassManager->getVersionProperty());

        $rootClass = $osClassManager->getRootClass();
        $this->assertInstanceOf(core_kernel_classes_Class::class, $rootClass);
        $this->assertEquals(self::PROPERTY_ROOT, $rootClass->getUri());

        $names = $osClassManager->getNames();
        $this->assertIsArray($names);
        $this->assertNotEmpty($names);
    }
}
