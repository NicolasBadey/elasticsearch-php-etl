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
 * Interface BuilderInterface.
 */
interface BuilderInterface
{
    /**
     * @return ETL
     *
     * Build and return ETL instance
     */
    public function build(): ETL;
}
