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
 * Class FileReader
 *
 * @package oat\taoClientRestrict\model\reader
 */
abstract class FileReader implements ReaderInterface
{
    /** @var string */
    private $path;

    /**
     * FileReader constructor.
     *
     * @param string $path
     *
     * @throws \common_exception_Error
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \common_exception_Error('File does not exist.');
        }

        $this->path = $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    abstract public static function supports(string $path): bool;

    abstract public function toArray(): array;

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }
}
