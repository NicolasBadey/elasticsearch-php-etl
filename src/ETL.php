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

use Pagerfanta\Pagerfanta;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ETL.
 *
 * You cannot directly use this class in symfony, use a Builder that extends AbstractETLBuilder for dependency injection
 */
class ETL
{
    /**
     * @var LoadInterface
     */
    protected $load;

    /**
     * @var ExtractInterface
     */
    protected $extract;

    /**
     * @var TransformInterface
     */
    protected $transform;

    /**
     * @var int
     *
     * Be careful,with SQL, Limit statement become time consuming after 1000, test the good value depending ES load
     */
    protected $maxPerPage = 1000;

    /**
     * @return $this
     */
    public function setExtract(ExtractInterface $extract)
    {
        $this->extract = $extract;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTransform(TransformInterface $transform)
    {
        $this->transform = $transform;

        return $this;
    }

    /**
     * @return $this
     */
    public function setLoad(LoadInterface $load)
    {
        $this->load = $load;

        return $this;
    }

    public static function create()
    {
        return new static();
    }

    public function setMaxPerPage(int $maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * @param array $ids
     *
     * run the ETL
     * if you don't want output, send NullOutput object
     */
    public function run(OutputInterface $output, bool $live = false, array $ids = []): void
    {
        $this->load->setLiveMode($live);

        $this->load->preLoad();

        //Extract
        $adapter = $this->extract->getAdapter($ids);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->maxPerPage);
        $nbPages = $pagerfanta->getNbPages();

        if (0 === $pagerfanta->getNbResults()) {
            $output->writeln('no documents to index for '.$this->load->getAlias());

            return;
        }

        $output->writeln('<info>'.$pagerfanta->getNbResults().' documents will be indexed in '.$this->load->getAlias().'</info>');

        $progressBar = new ProgressBar($output, $pagerfanta->getNbPages());
        $progressBar->start();

        for ($page = 1; $page <= $nbPages; ++$page) {
            $pagerfanta->setCurrentPage($page);

            /**
             * @var \ArrayIterator
             */
            $objects = $pagerfanta->getCurrentPageResults();

            //Transform
            $transformedObjects = $this->transform->transformObjects($objects->getArrayCopy());

            //memory optimisation
            $objects = null;
            $this->extract->purgeData();

            //Load
            $this->load->bulkLoad($transformedObjects);

            $progressBar->advance();
        }

        $this->load->postLoad();

        $progressBar->finish();

        $output->writeln('');
    }

    public function indexOne($object, bool $createIndexIdNotExists = false): array
    {
        $transformedObject = $this->transform->transformObject($object);

        return $this->load->singleLoad($transformedObject, $createIndexIdNotExists);
    }
}
