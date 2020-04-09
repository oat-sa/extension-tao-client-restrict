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

namespace oat\taoClientRestrict\test\unit\useCase\import;

use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use oat\taoClientRestrict\model\detection\OsClassService;
use oat\taoClientRestrict\model\useCase\import\ImportClientRestrictionsHandler;
use oat\taoClientRestrict\model\useCase\import\DataValidator;
use oat\taoClientRestrict\model\detection\BrowserClassService;
use oat\taoClientRestrict\model\detection\DetectorClassService;
use oat\taoClientRestrict\model\useCase\import\ClientRestrictionsImporter;

/**
 * Class ImportHandlerTest
 *
 * @package oat\taoClientRestrict\test\integration\useCase\import
 */
class ImportHandlerTest extends TestCase
{
    private const BROWSER_PROPERTY_ROOT = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#WebBrowser';
    private const BROWSER_PROPERTY_NAME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserName';
    private const BROWSER_PROPERTY_VERSION = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#BrowserVersion';

    private const OS_PROPERTY_ROOT = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OS';
    private const OS_PROPERTY_NAME = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSName';
    private const OS_PROPERTY_VERSION = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#OSVersion';

    /**
     * @dataProvider dataProvider
     *
     * @param array $data
     * @param array $expected
     */
    public function testImportHandler(array $data, array $expected): void
    {
        $dataValidatorMock = $this->createDataValidatorMock($data['dataValidator']);
        $importerMock = $this->createImporterMock($data['importer']);

        $serviceLocatorMock = $this->getServiceLocatorMock([
            DataValidator::class => $dataValidatorMock,
            ClientRestrictionsImporter::class => $importerMock,
        ]);

        /** @var DetectorClassService|MockObject $classServiceMock */
        $classServiceMock = $this->createClassServiceMock($data['classService']);

        $importHandler = new ImportClientRestrictionsHandler();
        $importHandler->setServiceLocator($serviceLocatorMock);
        $errors = $importHandler->handle($data['data'], $classServiceMock);

        $this->assertEquals($expected['errors'], $errors);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        $validName = 'validName';
        $lowercaseValidName = strtolower($validName);

        return [
            'Browser valid data' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'name' => $validName,
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => true,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => [],
                            'expects' => 0,
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [
                                $lowercaseValidName => 'Valid name',
                            ],
                        ],
                        'rootProperty' => self::BROWSER_PROPERTY_ROOT,
                        'nameProperty' => self::BROWSER_PROPERTY_NAME,
                        'versionProperty' => self::BROWSER_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 1,
                    'errors' => [],
                ],
            ],
            'Browser missed label' => [
                'data' => [
                    'data' => [
                        [
                            'name' => $validName,
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Required property `label` is missing.'],
                            'expects' => 1,
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [],
                        ],
                        'rootProperty' => self::BROWSER_PROPERTY_ROOT,
                        'nameProperty' => self::BROWSER_PROPERTY_NAME,
                        'versionProperty' => self::BROWSER_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Required property `label` is missing.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'Browser missed name' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Required property `name` is missing.'],
                            'expects' => 1,
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [],
                        ],
                        'rootProperty' => self::BROWSER_PROPERTY_ROOT,
                        'nameProperty' => self::BROWSER_PROPERTY_NAME,
                        'versionProperty' => self::BROWSER_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Required property `name` is missing.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'Browser missed version' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'name' => $validName,
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Required property `version` is missing.'],
                            'expects' => 1,
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [],
                        ],
                        'rootProperty' => self::BROWSER_PROPERTY_ROOT,
                        'nameProperty' => self::BROWSER_PROPERTY_NAME,
                        'versionProperty' => self::BROWSER_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Required property `version` is missing.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'Browser invalid name' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'name' => 'invalidName',
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Property `name` is invalid.'],
                            'expects' => 1,
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [
                                $lowercaseValidName => 'Valid name',
                            ],
                        ],
                        'rootProperty' => self::BROWSER_PROPERTY_ROOT,
                        'nameProperty' => self::BROWSER_PROPERTY_NAME,
                        'versionProperty' => self::BROWSER_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Property `name` is invalid.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'Browser nothing to import' => [
                'data' => [
                    'data' => [],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => true,
                            'expects' => 0,
                        ],
                        'getErrors' => [
                            'result' => [],
                            'expects' => 0,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 0,
                        'item' => [
                            'label' => '',
                            'name' => '',
                            'version' => '',
                        ],
                    ],
                    'importer' => [
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 0,
                            'result' => [
                                $lowercaseValidName => 'Valid name',
                            ],
                        ],
                        'rootProperty' => self::BROWSER_PROPERTY_ROOT,
                        'nameProperty' => self::BROWSER_PROPERTY_NAME,
                        'versionProperty' => self::BROWSER_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [],
                ],
            ],
            'OS valid data' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'name' => $validName,
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => true,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' =>  [],
                            'expects' => 0,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 1,
                        'item' => [
                            'label' => 'Test label',
                            'name' => 'Valid name',
                            'version' => 'Test version',
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [
                                $lowercaseValidName => 'Valid name',
                            ],
                        ],
                        'rootProperty' => self::OS_PROPERTY_ROOT,
                        'nameProperty' => self::OS_PROPERTY_NAME,
                        'versionProperty' => self::OS_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 1,
                    'errors' => [],
                ],
            ],
            'OS missed label' => [
                'data' => [
                    'data' => [
                        [
                            'name' => $validName,
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Required property `label` is missing.'],
                            'expects' => 1,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 0,
                        'item' => [
                            'label' => '',
                            'name' => '',
                            'version' => '',
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [],
                        ],
                        'rootProperty' => self::OS_PROPERTY_ROOT,
                        'nameProperty' => self::OS_PROPERTY_NAME,
                        'versionProperty' => self::OS_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Required property `label` is missing.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'OS missed name' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Required property `name` is missing.'],
                            'expects' => 1,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 0,
                        'item' => [
                            'label' => '',
                            'name' => '',
                            'version' => '',
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [],
                        ],
                        'rootProperty' => self::OS_PROPERTY_ROOT,
                        'nameProperty' => self::OS_PROPERTY_NAME,
                        'versionProperty' => self::OS_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Required property `name` is missing.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'OS missed version' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'name' => $validName,
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Required property `version` is missing.'],
                            'expects' => 1,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 0,
                        'item' => [
                            'label' => '',
                            'name' => '',
                            'version' => '',
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [],
                        ],
                        'rootProperty' => self::OS_PROPERTY_ROOT,
                        'nameProperty' => self::OS_PROPERTY_NAME,
                        'versionProperty' => self::OS_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Required property `version` is missing.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'OS invalid name' => [
                'data' => [
                    'data' => [
                        [
                            'label' => 'Test label',
                            'name' => 'invalidName',
                            'version' => 'Test version',
                        ],
                    ],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => false,
                            'expects' => 1,
                        ],
                        'getErrors' => [
                            'result' => ['Property `name` is invalid.'],
                            'expects' => 1,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 0,
                        'item' => [
                            'label' => '',
                            'name' => '',
                            'version' => '',
                        ],
                    ],
                    'importer' => [
                        'expects' => 1,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getExistingNames' => [
                            'expects' => 1,
                            'result' => [
                                $lowercaseValidName => 'Valid name',
                            ],
                        ],
                        'rootProperty' => self::OS_PROPERTY_ROOT,
                        'nameProperty' => self::OS_PROPERTY_NAME,
                        'versionProperty' => self::OS_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [
                        'Item 0 is invalid (Property `name` is invalid.).',
                        'Item 0 will not be imported.',
                    ],
                ],
            ],
            'OS nothing to import' => [
                'data' => [
                    'data' => [],
                    'dataValidator' => [
                        'isValid' => [
                            'result' => true,
                            'expects' => 0,
                        ],
                        'getErrors' => [
                            'result' => [],
                            'expects' => 0,
                        ],
                    ],
                    'dataProcessor' => [
                        'expects' => 0,
                        'item' => [
                            'label' => '',
                            'name' => '',
                            'version' => '',
                        ],
                    ],
                    'importer' => [
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => OsClassService::class,
                        'getExistingNames' => [
                            'expects' => 0,
                            'result' => [
                                $lowercaseValidName => 'Valid name',
                            ],
                        ],
                        'rootProperty' => self::OS_PROPERTY_ROOT,
                        'nameProperty' => self::OS_PROPERTY_NAME,
                        'versionProperty' => self::OS_PROPERTY_VERSION,
                    ],
                ],
                'expected' => [
                    'numberOfImportedItems' => 0,
                    'errors' => [],
                ],
            ],
        ];
    }

    /**
     * @param array $dataValidator
     *
     * @return MockObject
     */
    private function createDataValidatorMock(array $dataValidator): MockObject
    {
        $dataValidatorMock = $this->createMock(DataValidator::class);

        $dataValidatorMock
            ->expects($this->exactly($dataValidator['isValid']['expects']))
            ->method('isValid')
            ->willReturn($dataValidator['isValid']['result']);
        $dataValidatorMock
            ->expects($this->exactly($dataValidator['getErrors']['expects']))
            ->method('getErrors')
            ->willReturn($dataValidator['getErrors']['result']);

        return $dataValidatorMock;
    }

    /**
     * @param array $importer
     *
     * @return MockObject
     */
    private function createImporterMock(array $importer): MockObject
    {
        $importerMock = $this->createMock(ClientRestrictionsImporter::class);
        $importerMock
            ->expects($this->exactly($importer['expects']))
            ->method('import');

        return $importerMock;
    }

    /**
     * @param array $classService
     *
     * @return MockObject
     */
    private function createClassServiceMock(array $classService): MockObject
    {
        $classServiceMock = $this->createMock($classService['class']);
        $classServiceMock
            ->expects($this->exactly($classService['getExistingNames']['expects']))
            ->method('getExistingNames')
            ->willReturn($classService['getExistingNames']['result']);
        $classServiceMock
            ->method('getNamePropertyUri')
            ->willReturn($classService['nameProperty']);
        $classServiceMock
            ->method('getVersionPropertyUri')
            ->willReturn($classService['versionProperty']);

        return $classServiceMock;
    }
}
