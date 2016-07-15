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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoClientRestrict\controller;

use oat\tao\model\entryPoint\EntryPointService;

/**
 *
 * @package taoDelivery
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class Error extends \tao_actions_SaSModule
{

    /**
     * constructor: initialize the service and the default data
     * @access public
     */
    public function notCompatibleEnvironment()
    {
        $entryPoints = $this->getServiceManager()->get(EntryPointService::SERVICE_ID)->getEntryPoints();
        $deliveryServerUrl = $entryPoints['deliveryServer']->getUrl();

        $this->setData('client_config_url', $this->getClientConfigUrl());
        $this->setData('returnUrl', $deliveryServerUrl);
        $this->setData('showControls', false);
        $this->setData('userLabel', \common_session_SessionManager::getSession()->getUserLabel());
        $this->setData('content-template', 'Error/notCompatibleEnvironment.tpl');
        $this->setData('content-extension', 'taoClientRestrict');
        $this->setView('DeliveryServer/layout.tpl', 'taoDelivery');
    }


}