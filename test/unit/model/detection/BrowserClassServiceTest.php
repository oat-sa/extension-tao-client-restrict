<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoClientRestrict\test\unit\detection;

use core_kernel_classes_Class;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\TestCase;
use oat\taoClientRestrict\model\detection\BrowserClassService;
use ReflectionClass;
use ReflectionException;
use Sinergi\BrowserDetector\Browser;
use \oat\generis\model\data\Ontology;

class BrowserClassServiceTest extends TestCase
{
    public function testGetRootClass(): void
    {
        $model = $this->createMock(Ontology::class);
        $model->expects($this->once())
            ->method('getClass')
            ->with(BrowserClassService::ROOT_CLASS)
            ->willReturn('fixture');

        $service = new BrowserClassService();
        $service->setModel($model);
        $this->assertEquals('fixture', $service->getRootClass());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetMakeClass(): void
    {
        $detectedName = 'fixture-os-name';
        $resource = 'fixture-resource';

        $class = $this->createMock(core_kernel_classes_Class::class);
        $class->expects($this->once())
            ->method('searchInstances')
            ->with(
                [ OntologyRdfs::RDFS_LABEL => $detectedName ],
                [ 'like' => false ]
            )
            ->willReturn([$resource]);

        $model = $this->createMock(Ontology::class);
        $model->expects($this->once())
            ->method('getClass')
            ->with(BrowserClassService::BROWSER_MAKE)
            ->willReturn($class);

        $detector = $this->createMock(Browser::class);
        $detector->expects($this->once())
            ->method('getName')
            ->willReturn($detectedName);

        $service = new BrowserClassService();
        $reflection = new ReflectionClass($service);
        $reflection_property = $reflection->getProperty('detector');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($service, $detector);
        $service->setModel($model);

        $this->assertEquals($resource, $service->getClientNameResource());
    }

    public function testGetNameProperty(): void
    {
        $model = $this->createMock(Ontology::class);
        $model->expects($this->once())
            ->method('getProperty')
            ->with(BrowserClassService::BROWSER_NAME)
            ->willReturn('fixture');

        $service = new BrowserClassService();
        $service->setModel($model);
        $this->assertEquals('fixture', $service->getNameProperty());
    }

    public function testGetVersionProperty()
    {
        $model = $this->createMock(Ontology::class);
        $model->expects($this->once())
            ->method('getProperty')
            ->with(BrowserClassService::BROWSER_VERSION)
            ->willReturn('fixture');

        $service = new BrowserClassService();
        $service->setModel($model);
        $this->assertEquals('fixture', $service->getVersionProperty());
    }

}
