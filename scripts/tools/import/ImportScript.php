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

namespace oat\taoClientRestrict\scripts\tools\import;

use common_report_Report as Report;
use oat\oatbox\extension\script\ScriptAction;
use oat\taoClientRestrict\model\import\Importer;
use oat\taoClientRestrict\model\import\ImportDataProcessor;

/**
 * Class ImportScript
 *
 * @package oat\taoClientRestrict\scripts\tools\import
 */
abstract class ImportScript extends ScriptAction
{
    /** @var Importer */
    private $service;

    /** @var Report */
    private $report;

    /**
     * @example --list - An array of approved browsers/OS
     * [
     *     [
     *         'classMap' => [
     *             'Class 1',
     *             'Class 2',
     *         ],
     *         'label' => 'Test label',
     *         'name' => 'Chrome',
     *         'version' => '1.0.0',
     *     ],
     * ]
     * @example classMap - Will generate the necessary folder structure from the root class (Optional)
     * @example label - Label for browser/OS (Required)
     * @example name - Browser/OS name (Optional)
     * @example version - Browser/OS version (Optional)
     *
     * @return array
     */
    protected function provideOptions()
    {
        return [
            'list' => [
                'prefix' => 'l',
                'longPrefix' => 'list',
                'required' => true,
                'description' => 'List of approved browsers/OS. Should be represented as an json array of browsers/OS.',
            ],
        ];
    }

    /**
     * @return string
     */
    protected function provideDescription()
    {
        return 'Allow to import authorized data while seeding.';
    }

    /**
     * @throws \common_Exception
     * @throws \common_exception_Error
     *
     * @return Report
     */
    protected function run()
    {
        $this->report = Report::createInfo('Running script ' . static::class);
        $this->import($this->getData());

        return $this->report;
    }

    /**
     * @throws \common_Exception
     * @throws \common_exception_Error
     *
     * @return array
     */
    protected function getData(): array
    {
        if ($this->hasOption('list')) {
            $list = $this->getOption('list');
        }

        if (!isset($list) || !is_array($list)) {
            $list = [];
        }

        return $this->getValidData($list);
    }

    /**
     * @return string
     */
    abstract protected function getServiceClass(): string;

    /**
     * @param array $data
     *
     * @throws \common_Exception
     * @throws \common_exception_Error
     *
     * @return array
     */
    private function getValidData(array $data): array
    {
        /** @var ImportDataProcessor $importDataProcessor */
        $importDataProcessor = $this->getServiceLocator()->get(ImportDataProcessor::class);
        $processedData = $importDataProcessor->process($data, $this->getService());

        foreach ($processedData['errors'] as $error) {
            $this->report->add(Report::createFailure($error));
        }

        return $processedData['data'];
    }

    /**
     * @return Importer
     */
    private function getService(): Importer
    {
        if (!$this->service) {
            $this->service = $this->getServiceLocator()->get($this->getServiceClass());
        }

        return $this->service;
    }

    /**
     * @param array $data
     */
    private function import(array $data): void
    {
        $this->getService()->import($data);
    }
}
