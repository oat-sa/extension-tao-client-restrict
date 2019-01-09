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
 * Copyright (c) 2016-2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoClientRestrict\controller;

use oat\taoClientRestrict\model\detection\BrowserClassService;
use oat\taoClientRestrict\model\requirements\RequirementsServiceInterface;

/**
 *
 * @package taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class WebBrowsers extends \tao_actions_SaSModule
{

    public function editInstance()
    {
        $clazz = $this->getCurrentClass();
        $instance = $this->getCurrentInstance();
        $myFormContainer = new \tao_actions_form_Instance($clazz, $instance);

        $myForm = $myFormContainer->getForm();
        $nameElement = $myForm->getElement(\tao_helpers_Uri::encode(BrowserClassService::PROPERTY_NAME));
        $versionElement = $myForm->getElement(\tao_helpers_Uri::encode(BrowserClassService::PROPERTY_VERSION));
        $nameElement->addClass('select2');
        $versionElement->setHelp(
            "<span class=\"icon-help tooltipstered\" data-tooltip=\".web-browser-form .browser-version-tooltip-content\" data-tooltip-theme=\"info\"></span>"
        );
        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {

                $values = $myForm->getValues();
                // save properties
                $binder = new \tao_models_classes_dataBinding_GenerisFormDataBinder($instance);
                $instance = $binder->bind($values);
                $message = __('Instance saved');

                $this->setData('message', $message);
                $this->setData('reload', true);
            }
        }

        $this->setData('formTitle', __('Edit Authorized Web Browser'));
        $this->setData('myForm', $myForm->render());
        $this->setView('WebBrowsers/form.tpl');
    }

    /**
     *
     */
    public function diagnose()
    {
        /** @var RequirementsServiceInterface $requirementsService */
        $requirementsService = $this->getServiceLocator()->get(RequirementsServiceInterface::CONFIG_ID);

        $approvedBrowsers = $requirementsService->getApprovedBrowsers();
        $approvedBrowsersFormatted = [];

        if (! empty($approvedBrowsers)) {
            forEach($approvedBrowsers as $browser) {
                $browserName = $browser->getUniquePropertyValue($this->getClassService()->getNameProperty());
                $browserVersion = $browser->getUniquePropertyValue($this->getClassService()->getNameProperty());
                $approvedBrowsersFormatted[$browserName->getLabel()][] = $browserVersion->__toString();
            }
        }
        $this->returnJson([
            'success' => $requirementsService->browserComplies(),
            'approvedBrowsers' => $approvedBrowsersFormatted
        ]);
    }

    /**
     * @return BrowserClassService
     */
    protected function getClassService()
    {
        if (is_null($this->service)) {
            $this->service = BrowserClassService::singleton();
        }
        return $this->service;
    }
}