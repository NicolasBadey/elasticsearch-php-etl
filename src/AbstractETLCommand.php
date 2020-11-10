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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractETLCommand.
 */
abstract class AbstractETLCommand extends Command
{
    /**
     * @var BuilderInterface
     */
    protected $ETLBuilder;

    protected function configure()
    {
        $this
            ->setDescription('ETL for populate Elasticsearch from SQL')
            ->addOption(
                'live',
                'l'
            )->addArgument(
                'ids',
                InputArgument::IS_ARRAY,
                'specifics Ids to populate (separate multiple ids with a space)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ETLBuilder->build()->run($output, $input->getOption('live'), $input->getArgument('ids'));
        
        return Command::SUCCESS;
    }
}
