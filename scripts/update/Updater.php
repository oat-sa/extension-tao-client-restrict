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
 *
 */
namespace oat\taoClientRestrict\scripts\update;

use \common_ext_ExtensionUpdater;
use oat\tao\scripts\update\OntologyUpdater;

/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends common_ext_ExtensionUpdater {

    /**
     * (non-PHPdoc)
     * @see common_ext_ExtensionUpdater::update()
     */
    public function update($initialVersion) {
        $this->skip('1.0.0', '1.0.6');

        if ($this->isVersion('1.0.6')) {
            OntologyUpdater::syncModels();
            $this->setVersion('1.0.7');
        }

        $this->skip('1.0.7', '2.1.0');
    }

}
