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
 * Class CsvReader
 *
 * @package oat\taoClientRestrict\model\reader
 */
class CsvReader extends FileReader
{
    private const EXT_CSV = 'csv';

    /**
     * @return array
     */
    public function toArray(): array
    {
        $lines = array_filter(explode("\n", file_get_contents($this->getPath())));
        $items = array_map('str_getcsv', $lines);
        $keys = $items[0];

        return array_map(static function ($item) use ($keys) {
            return array_combine($keys, $item);
        }, array_slice($items, 1));
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function supports(string $path): bool
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION)) === self::EXT_CSV;
    }
}
