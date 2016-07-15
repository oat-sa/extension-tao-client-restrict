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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoClientRestrict\model;


use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\authorization\AuthorizationProvider;
use oat\oatbox\service\ServiceManager;
use oat\taoClientRestrict\model\requirements\RequirementsServiceInterface;
use oat\taoDelivery\model\execution\DeliveryExecution;
use oat\oatbox\user\User;
use oat\taoClientRestrict\model\requirements\RequirementsService;
use oat\taoDelivery\model\authorization\UnAuthorizedException;

/**
 * Default authorization provider using the strainer strategy...
 *
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class ClientAuthorizationProvider extends ConfigurableService implements AuthorizationProvider
{
    protected function validateClient($deliveryId)
    {
        $service = $this->getServiceLocator()->get(RequirementsServiceInterface::CONFIG_ID);
        if (!$service->compliesToDelivery($deliveryId)) {
            throw new UnAuthorizedException(_url('notCompatibleEnvironment','Error','taoClientRestrict'));
        }
    }

    public function verifyStartAuthorization($deliveryId, User $user)
    {
        $this->validateClient($deliveryId);
    }
    
    public function verifyResumeAuthorization(DeliveryExecution $deliveryExecution, User $user)
    {
        $this->validateClient($deliveryExecution->getDelivery()->getUri());
    }
}
