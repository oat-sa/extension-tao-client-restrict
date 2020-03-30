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
use oat\taoClientRestrict\model\useCase\import\ImportHandler;
use oat\taoClientRestrict\model\detection\DetectorClassService;

/**
 * Class ImportScript
 *
 * @package oat\taoClientRestrict\scripts\tools\import
 */
class ImportScript extends ScriptAction
{
    /**
     * @example --list - An array of approved browsers/OS
     *     [
     *         {
     *             "classMap": [
     *                 "Class 1",
     *                 "Class 2"
     *             ],
     *             "label": "Test label",
     *             "name": "Chrome",
     *             "version": "1.0.0"
     *         },
     *     ]
     * @example classMap - Will generate the necessary folder structure from the root class (Optional)
     * @example label - Label for browser/OS (Required)
     * @example name - Browser/OS name (Required)
     * @example version - Browser/OS version (Required)
     *
     * @example --service - Class service which will be used for import.
     *     List of available managers:
     *         - oat\taoClientRestrict\model\detection\BrowserClassService
     *         - oat\taoClientRestrict\model\detection\OsClassService
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
                'description' => 'List of authorized browsers/OS. Should be represented as an json array of browsers/OS.',
                'defaultValue' => [],
            ],
            'service' => [
                'prefix' => 's',
                'longPrefix' => 'service',
                'required' => true,
                'description' => 'Class service which will be used for import (string).',
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
        $serviceClass = $this->getOption('service');
        $report = Report::createInfo(sprintf('Importing... (%s)', $serviceClass));

        $serviceLocator = $this->getServiceLocator();

        /** @var DetectorClassService $classService */
        $classService = $serviceLocator->get($serviceClass);
        /** @var ImportHandler $handler */
        $handler = $serviceLocator->get(ImportHandler::class);

        $errors = $handler->handle($this->getOption('list'), $classService);

        foreach ($errors as $error) {
            $report->add(Report::createFailure($error));
        }

        return $report;
    }
}
