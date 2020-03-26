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
use oat\taoClientRestrict\model\reader\ReaderFactory;
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
     * @return array
     */
    protected function provideOptions()
    {
        return [
            'list' => [
                'prefix' => 'l',
                'longPrefix' => 'list',
                'required' => true,
                'description' => 'List of approved browsers/OS. Can be represented as an array of browsers/OS or a path'
                    . ' to the file with the necessary data. Supported file extensions: JSON and CSV.',
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
     * @throws \common_exception_Error
     *
     * @return Report
     */
    protected function run()
    {
        $this->report = Report::createInfo('Running script ' . static::class);

        $data = $this->parseData();
        $this->import($data);

        return $this->report;
    }

    /**
     * @throws \common_exception_Error
     *
     * @return array
     */
    protected function parseData(): array
    {
        if ($this->hasOption('list')) {
            $list = $this->getOption('list');

            if (is_array($list)) {
                $data = $list;
            } elseif (is_string($list)) {
                $data = $this->parseFile($list);
            }
        }

        return $this->getValidData($data ?? []);
    }

    /**
     * @return string
     */
    abstract protected function getServiceClass(): string;

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
     * @param string $filename
     *
     * @throws \common_exception_Error
     *
     * @return array
     */
    private function parseFile(string $filename): array
    {
        try {
            $data = ReaderFactory::create($filename)->toArray();
        } catch (\common_exception_Error $e) {
            $this->report->add(Report::createFailure($e->getMessage()));
            $data = [];
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @throws \common_exception_Error
     *
     * @return array
     */
    private function getValidData(array $data): array
    {
        /** @var ImportDataProcessor $importDataProcessor */
        $importDataProcessor = $this->getServiceLocator()->get(ImportDataProcessor::class);
        $validData = $importDataProcessor->process($data, $this->getService());
        $errors = $importDataProcessor->getErrors();

        if (!empty($errors)) {
            /** @var Report $error */
            foreach ($errors as $error) {
                $this->report->add($error);
            }
        }

        return $validData;
    }

    /**
     * @param array $data
     */
    private function import(array $data): void
    {
        $this->getService()->import($data);
    }
}
