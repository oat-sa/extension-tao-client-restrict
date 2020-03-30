<?php

namespace oat\taoClientRestrict\test\unit\classManager;

use core_kernel_classes_Class;
use oat\generis\test\TestCase;
use oat\taoClientRestrict\model\classManager\BrowserClassManager;

/**
 * Class BrowserClassManagerTest
 *
 * @package oat\taoClientRestrict\test\unit\classManager
 */
class BrowserClassManagerTest extends TestCase
{
    private const PROPERTY_ROOT = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#WebBrowser';
    private const PROPERTY_NAME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserName';
    private const PROPERTY_VERSION = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserVersion';

    public function testBrowserClassManager(): void
    {
        $browserClassManager = new BrowserClassManager();

        $this->assertEquals(self::PROPERTY_ROOT, $browserClassManager->getRootProperty());
        $this->assertEquals(self::PROPERTY_NAME, $browserClassManager->getNameProperty());
        $this->assertEquals(self::PROPERTY_VERSION, $browserClassManager->getVersionProperty());

        $rootClass = $browserClassManager->getRootClass();
        $this->assertInstanceOf(core_kernel_classes_Class::class, $rootClass);
        $this->assertEquals(self::PROPERTY_ROOT, $rootClass->getUri());

        $names = $browserClassManager->getNames();
        $this->assertIsArray($names);
        $this->assertNotEmpty($names);
    }
}
