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

/**
 * Class ImportScript
 *
 * @package oat\taoClientRestrict\scripts\tools\import
 */
abstract class ImportScript extends ScriptAction
{
    private const EXT_JSON = 'json';
    private const EXT_CSV = 'csv';

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
                'description' => 'String or array',
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
            } elseif (is_string($list) && file_exists($list)) {
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
     * @return array
     */
    private function parseFile(string $filename): array
    {
        switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case self::EXT_JSON:
                $data = json_decode(file_get_contents($filename), true);
                break;
            case self::EXT_CSV:
                $lines = array_filter(explode("\n", file_get_contents($filename)));
                $items = array_map('str_getcsv', $lines);
                $keys = $items[0];

                $data = array_map(static function ($item) use ($keys) {
                    return array_combine($keys, $item);
                }, array_slice($items, 1));
                break;
            default:
                $data = [];
                break;
        }

        return is_array($data) ? $data : [];
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
        $validData = [];
        $service = $this->getService();

        foreach ($data as $index => $item) {
            if (!isset($item[Importer::PROPERTY_LABEL])) {
                $this->report->add(Report::createFailure(sprintf(
                    'Required property `label` for item %s is missing. The item will not be imported...',
                    $index
                )));
                continue;
            }

            if (isset($item[Importer::PROPERTY_NAME])) {
                if ($service->nameExists($item[Importer::PROPERTY_NAME])) {
                    $item[Importer::PROPERTY_NAME] = $service->getNameUri($item[Importer::PROPERTY_NAME]);
                } else {
                    $this->report->add(Report::createFailure(sprintf(
                        'Property `name` for item %s is invalid. The item will not be imported...',
                        $index
                    )));
                    continue;
                }
            }

            $validData[] = $item;
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
