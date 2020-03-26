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

namespace oat\taoClientRestrict\model\reader;

/**
 * Class JsonReader
 *
 * @package oat\taoClientRestrict\model\reader
 */
class JsonReader extends FileReader
{
    private const EXT_JSON = 'json';

    /**
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(file_get_contents($this->getPath()), true);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function supports(string $path): bool
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === self::EXT_JSON;
    }
}
