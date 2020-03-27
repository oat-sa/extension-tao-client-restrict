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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoClientRestrict\test\unit\import;

use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use oat\taoClientRestrict\model\import\Importer;
use oat\taoClientRestrict\model\import\ImportOsService;
use oat\taoClientRestrict\model\import\ImportDataProcessor;
use oat\taoClientRestrict\model\import\ImportBrowsersService;

/**
 * Class ImportDataProcessorTest
 *
 * @package oat\taoClientRestrict\test\unit\import
 */
class ImportDataProcessorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param string $importerClass
     * @param array $mockedData
     * @param array $data
     * @param array $expected
     *
     * @throws \common_exception_Error
     */
    public function testImportDataProcessor(
        string $importerClass,
        array $mockedData,
        array $data,
        array $expected
    ): void {
        /** @var Importer|MockObject $importerMock */
        $importerMock = $this->createMock($importerClass);
        $importerMock->method('nameExists')->willReturn($mockedData['nameExists']);
        $importerMock->method('getNameUri')->willReturn($mockedData['nameUri']);

        $processedData = (new ImportDataProcessor())->process($data, $importerMock);

        $this->assertArrayHasKey('data', $processedData);
        $this->assertEquals($expected['data'], $processedData['data']);

        $this->assertArrayHasKey('errors', $processedData);
        $this->assertEquals($expected['errors'], $processedData['errors']);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            'testValidBrowser' => [
                'importerClass' => ImportBrowsersService::class,
                'mockedData' => [
                    'nameExists' => true,
                    'nameUri' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserChrome'
                ],
                'data' => [
                    $this->createInstanceData('Chrome'),
                ],
                'expected' => [
                    'data' => [
                        $this->createInstanceData('http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserChrome'),
                    ],
                    'errors' => [],
                ],
            ],
            'testValidOs' => [
                'importerClass' => ImportOsService::class,
                'mockedData' => [
                    'nameExists' => true,
                    'nameUri' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OsOSX'
                ],
                'data' => [
                    $this->createInstanceData('OS X'),
                ],
                'expected' => [
                    'data' => [
                        $this->createInstanceData('http://www.tao.lu/Ontologies/TAODelivery.rdf#OsOSX'),
                    ],
                    'errors' => [],
                ],
            ],
            'testInvalidBrowserName' => [
                'importerClass' => ImportBrowsersService::class,
                'mockedData' => [
                    'nameExists' => false,
                    'nameUri' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserChrome'
                ],
                'data' => [
                    $this->createInstanceData('test_invalidBrowser'),
                ],
                'expected' => [
                    'data' => [],
                    'errors' => [
                        'Property `name` for item 0 is invalid. The item will not be imported...',
                    ],
                ],
            ],
            'testInvalidOsName' => [
                'importerClass' => ImportOsService::class,
                'mockedData' => [
                    'nameExists' => false,
                    'nameUri' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OsOSX'
                ],
                'data' => [
                    $this->createInstanceData('test_invalidOs'),
                ],
                'expected' => [
                    'data' => [],
                    'errors' => [
                        'Property `name` for item 0 is invalid. The item will not be imported...',
                    ],
                ],
            ],
            'testMissedBrowserLabel' => [
                'importerClass' => ImportBrowsersService::class,
                'mockedData' => [
                    'nameExists' => true,
                    'nameUri' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserChrome'
                ],
                'data' => [
                    $this->createInstanceData('Chrome', true),
                ],
                'expected' => [
                    'data' => [],
                    'errors' => [
                        'Required property `label` for item 0 is missing. The item will not be imported...',
                    ],
                ],
            ],
            'testMissedOsLabel' => [
                'importerClass' => ImportOsService::class,
                'mockedData' => [
                    'nameExists' => true,
                    'nameUri' => 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OsOSX'
                ],
                'data' => [
                    $this->createInstanceData('OS X', true),
                ],
                'expected' => [
                    'data' => [],
                    'errors' => [
                        'Required property `label` for item 0 is missing. The item will not be imported...',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @param bool $removeLabel
     *
     * @return array
     */
    private function createInstanceData(string $name, bool $removeLabel = false): array
    {
        $data = [
            'classMap' => ['Test class 1'],
            'label' => 'Test label 1',
            'name' => $name,
            'version' => '1.0.0',
        ];

        if ($removeLabel) {
            unset($data['label']);
        }

        return $data;
    }
}
