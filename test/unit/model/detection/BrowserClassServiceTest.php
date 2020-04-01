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

use oat\generis\model\data\Model;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\TestCase;
use oat\taoClientRestrict\model\detection\BrowserClassService;
use ReflectionClass;
use Sinergi\BrowserDetector\Browser;
use \oat\generis\model\data\Ontology;

class BrowserClassServiceTest extends TestCase
{
    public function testGetRootClass()
    {
        $model = $this->getMockForAbstractClass(Ontology::class, [], '', false, true, true, ['getClass']);
        $model->expects($this->once())
            ->method('getClass')
            ->with(BrowserClassService::ROOT_CLASS)
            ->willReturn('fixture');

        $service = BrowserClassService::singleton();
        $service->setModel($model);
        $this->assertEquals('fixture', $service->getRootClass());
    }

    public function testGetMakeClass()
    {
        $detectedName = 'fixture-os-name';
        $resource = 'fixture-resource';

        $class = $this->getMockBuilder(\core_kernel_classes_Class::class)
            ->disableOriginalConstructor()
            ->setMethods(['searchInstances'])
            ->getMock();
        $class->expects($this->once())
            ->method('searchInstances')
            ->with(
                [ OntologyRdfs::RDFS_LABEL => $detectedName ],
                [ 'like' => false ]
            )
            ->willReturn([$resource]);

        $model = $this->getMockForAbstractClass(Ontology::class, [], '', false, true, true, ['getClass']);
        $model->expects($this->once())
            ->method('getClass')
            ->with(BrowserClassService::MAKE_CLASS)
            ->willReturn($class);

        $detector = $this->getMockBuilder(Browser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();
        $detector->expects($this->once())
            ->method('getName')
            ->willReturn($detectedName);

        $service = BrowserClassService::singleton();
        $reflection = new ReflectionClass($service);
        $reflection_property = $reflection->getProperty('detector');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($service, $detector);
        $service->setModel($model);

        $this->assertEquals($resource, $service->getClientNameResource());
    }

    public function testGetNameProperty()
    {
        $model = $this->getMockForAbstractClass(Ontology::class, [], '', false, true, true, ['getProperty']);
        $model->expects($this->once())
            ->method('getProperty')
            ->with(BrowserClassService::BROWSER_NAME)
            ->willReturn('fixture');

        $service = BrowserClassService::singleton();
        $service->setModel($model);
        $this->assertEquals('fixture', $service->getNameProperty());
    }

    public function testGetVersionProperty()
    {
        $model = $this->getMockForAbstractClass(Ontology::class, [], '', false, true, true, ['getProperty']);
        $model->expects($this->once())
            ->method('getProperty')
            ->with(BrowserClassService::BROWSER_VERSION)
            ->willReturn('fixture');

        $service = BrowserClassService::singleton();
        $service->setModel($model);
        $this->assertEquals('fixture', $service->getVersionProperty());
    }

}
