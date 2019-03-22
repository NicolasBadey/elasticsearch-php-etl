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
 * Class AbstractETLBuilder.
 */
abstract class AbstractETLBuilder implements BuilderInterface
{
    /**
     * @var ExtractInterface
     */
    protected $extract;

    /**
     * @var TransformInterface
     */
    protected $transform;

    /**
     * @var LoadInterface
     */
    protected $load;

    /**
     * @var ETL
     */
    protected $etl;

    public function build(): ETL
    {
        if (null === $this->etl) {
            $this->etl = (ETL::create())
                ->setExtract($this->extract)
                ->setTransform($this->transform)
                ->setLoad($this->load);
        }

        return $this->etl;
    }
}
