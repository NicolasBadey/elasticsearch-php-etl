<?php
/*
 * This file is part of the ElasticsearchETL package.
 *
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ElasticsearchETL;

/**
 * Interface TransformInterface.
 */
interface TransformInterface
{
    /**
     * @return array
     *
     * Transform an array of objects
     */
    public function transformObjects(array $objects): array;

    /**
     * @return array
     *
     * Transform one object
     */
    public function transformObject($object): array;
}
