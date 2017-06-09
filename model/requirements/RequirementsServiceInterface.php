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
namespace oat\taoClientRestrict\model\requirements;

/**
 * Manage delivery client restrictions
 *
 * @author Mikhail Kamarouski <kamarouski@1pt.com>
 */
interface RequirementsServiceInterface
{
    const CONFIG_ID = 'taoClientRestrict/requirements';

    /**
     * Whether client complies to delivery
     * @param string $deliveryId
     * @return bool
     */
    public function compliesToDelivery($deliveryId);

    /**
     * @param \core_kernel_classes_Resource $delivery
     * @return boolean
     */
    public function browserComplies(\core_kernel_classes_Resource $delivery = null);

    /**
     * @param \core_kernel_classes_Resource $delivery
     * @return boolean
     */
    public function osComplies(\core_kernel_classes_Resource $delivery = null);

}