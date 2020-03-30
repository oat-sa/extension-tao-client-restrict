<?php

namespace oat\taoClientRestrict\test\integration\useCase\import;

use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use oat\taoClientRestrict\model\useCase\import\Importer;
use oat\taoClientRestrict\model\useCase\import\ClassDTO;
use oat\taoClientRestrict\model\detection\OsClassService;
use oat\taoClientRestrict\model\useCase\import\ImportHandler;
use oat\taoClientRestrict\model\useCase\import\DataValidator;
use oat\taoClientRestrict\model\useCase\import\DataProcessor;
use oat\taoClientRestrict\model\detection\BrowserClassService;
use oat\taoClientRestrict\model\detection\DetectorClassService;

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
        $dataProcessorMock = $this->createDataProcessorMock($data['dataProcessor']);
        $importerMock = $this->createImporterMock($data['importer']);

        $serviceLocatorMock = $this->getServiceLocatorMock([
            DataValidator::class => $dataValidatorMock,
            DataProcessor::class => $dataProcessorMock,
            Importer::class => $importerMock,
        ]);

        /** @var DetectorClassService|MockObject $classServiceMock */
        $classServiceMock = $this->createClassServiceMock($data['classService']);

        $importHandler = new ImportHandler();
        $importHandler->setServiceLocator($serviceLocatorMock);

        $errors = $importHandler->handle($data['data'], $classServiceMock);

        $this->assertEquals($expected['errors'], $errors);
        $this->assertEquals(
            $expected['numberOfImportedItems'],
            count($data['data']) - count($errors)
        );
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
                        'getError' => [
                            'result' => null,
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
                        'getNames' => [
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
                        'getError' => [
                            'result' => 'Required property `label` is missing.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Required property `label` is missing.). The item will not be imported.',
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
                        'getError' => [
                            'result' => 'Required property `name` is missing.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Required property `name` is missing.). The item will not be imported.',
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
                        'getError' => [
                            'result' => 'Required property `version` is missing.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Required property `version` is missing.). The item will not be imported.',
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
                        'getError' => [
                            'result' => 'Property `name` is invalid.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Property `name` is invalid.). The item will not be imported.'
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
                        'getError' => [
                            'result' => null,
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
                        'getNames' => [
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
                        'getError' => [
                            'result' => null,
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
                        'getNames' => [
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
                        'getError' => [
                            'result' => 'Required property `label` is missing.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Required property `label` is missing.). The item will not be imported.',
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
                        'getError' => [
                            'result' => 'Required property `name` is missing.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Required property `name` is missing.). The item will not be imported.',
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
                        'getError' => [
                            'result' => 'Required property `version` is missing.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Required property `version` is missing.). The item will not be imported.',
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
                        'getError' => [
                            'result' => 'Property `name` is invalid.',
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
                        'expects' => 0,
                    ],
                    'classService' => [
                        'class' => BrowserClassService::class,
                        'getNames' => [
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
                        'Item 0 is invalid (Property `name` is invalid.). The item will not be imported.'
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
                        'getError' => [
                            'result' => null,
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
                        'getNames' => [
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
            ->expects($this->exactly($dataValidator['getError']['expects']))
            ->method('getError')
            ->willReturn($dataValidator['getError']['result']);

        return $dataValidatorMock;
    }

    /**
     * @param array $dataProcessor
     *
     * @return MockObject
     */
    private function createDataProcessorMock(array $dataProcessor): MockObject
    {
        $dataProcessorMock = $this->createMock(DataProcessor::class);
        $dataProcessorMock
            ->expects($this->exactly($dataProcessor['expects']))
            ->method('process')
            ->willReturn($this->createClassDtoMock($dataProcessor['item']));

        return $dataProcessorMock;
    }

    /**
     * @param array $item
     *
     * @return MockObject
     */
    private function createClassDtoMock(array $item): MockObject
    {
        $classDtoMock = $this->createMock(ClassDTO::class);
        $classDtoMock
            ->method('getLabel')
            ->willReturn($item['label']);
        $classDtoMock
            ->method('getName')
            ->willReturn($item['name']);
        $classDtoMock
            ->method('getVersion')
            ->willReturn($item['version']);

        return $classDtoMock;
    }

    /**
     * @param array $importer
     *
     * @return MockObject
     */
    private function createImporterMock(array $importer): MockObject
    {
        $importerMock = $this->createMock(Importer::class);
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
            ->expects($this->exactly($classService['getNames']['expects']))
            ->method('getNames')
            ->willReturn($classService['getNames']['result']);
        $classServiceMock
            ->method('getNamePropertyUri')
            ->willReturn($classService['nameProperty']);
        $classServiceMock
            ->method('getVersionPropertyUri')
            ->willReturn($classService['versionProperty']);

        return $classServiceMock;
    }
}
