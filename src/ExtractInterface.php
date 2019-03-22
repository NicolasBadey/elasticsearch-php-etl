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

use Pagerfanta\Adapter\AdapterInterface;

/**
 * Interface ExtractInterface.
 */
interface ExtractInterface
{
    /**
     * @return AdapterInterface
     *
     * return PagerFanta Adapter for iterate in ETL
     */
    public function getAdapter(array $ids = []): AdapterInterface;

    /**
     * can be an empty function, basically allow to purge ORM used in Extract layer than can have memory leak.
     */
    public function purgeData(): void;
}
