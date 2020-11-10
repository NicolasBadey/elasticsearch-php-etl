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
 * Class AbstractElasticsearchLoad.
 */
abstract class AbstractElasticsearchLoad implements LoadInterface
{
    /**
     * @var ElasticsearchClientInterface
     */
    protected $client;

    /**
     * @var string
     *
     * Index Name
     */
    private $index = '';

    /**
     * @var string
     *
     * Index Name
     */
    private $env;

    /**
     * @var bool
     *
     * Live mode allow indexing directly in the current alias' index if exists or by create a new index with an alias
     * The point is to show content as fast as possible without wait indexation's end
     * Basically it's the panic button when ES server has been reset directly in prod or for update index without mapping and deletion changes
     */
    private $live;

    /**
     * AbstractElasticsearchLoad constructor.
     */
    public function __construct(ElasticsearchClientInterface $client, string $app_env)
    {
        $this->client = $client;
        $this->env = $env;
    }

    abstract public function getMappingProperties();

    abstract public function getAlias(): string;

    protected function getMapping(): array
    {
        return [
            'index' => $this->getIndex(),
            'body' => [
                'properties' => $this->getMappingProperties(),
            ],
        ];
    }

    protected function invertAlias()
    {
        $this->client->indices()->updateAliases([
            'body' => [
                'actions' => [
                    [
                        'remove' => [
                            'index' => '*',
                            'alias' => $this->getAlias(),
                        ],
                    ],
                    [
                        'add' => [
                            'index' => $this->getIndex(),
                            'alias' => $this->getAlias(),
                        ],
                    ],
                ],
            ],
        ]);
    }

    protected function deleteUnusedIndices()
    {
        $response = $this->client->indices()->getMapping();
        $indices = array_keys($response);

        foreach ($indices as $key => $existingIndex) {
            //only if it's not the current index and not a 3rd party index
            if ($existingIndex !== $this->getIndex() && 0 === mb_strpos($existingIndex, $this->getAlias())) {
                $this->client->indices()->delete([
                    'index' => $existingIndex,
                ]);
            }
        }
    }

    public function setLiveMode(bool $live): void
    {
        $this->live = $live;
    }

    public function getIndex()
    {
        if ('' === $this->index) {
            if (true === $this->live && true === $this->aliasExists()) {
                //in this case we want to populate current live index if already exists
                $this->index = $this->client->getIndexNameFromAlias($this->getAlias())[0];
            } else {
                $this->index = $this->getAlias().'_'.(new \DateTime())->format('U');
            }
        }

        return $this->index;
    }

    public function createIndex($live): void
    {
        $this->client->indices()->create([
            'index' => $this->getIndex(),
        ]);

        $this->client->indices()->putMapping($this->getMapping());

        if (true === $live) {
            //put it live directly
            $this->invertAlias();
        }
    }

    public function preLoad(): void
    {
        if (false === $this->live || false === $this->aliasExists()) {
            $this->createIndex($this->live);
        }
    }

    public function postLoad(): void
    {
        if (false === $this->live) {
            $this->invertAlias();
            $this->deleteUnusedIndices();
        }

        //avoid index collision if ETL is use repetitively in the same script (in test for example)
        $this->index = '';
    }

    public function formatForBulkIndex(array $params): array
    {
        $paramsIndex = [];

        foreach ($params as $param) {
            $paramsIndex['body'][] = [
                'index' => [
                    '_index' => $this->getIndex(),
                   //'_type' => $this->getAlias(),
                    '_id' => $param['id'],
                ],
            ];

            unset($param['id']);
            $paramsIndex['body'][] = $param;
        }

        return $paramsIndex;
    }

    public function formatForIndex(array $param): array
    {
        $paramIndex = [
            'index' => $this->getAlias(),
            //'type' => $this->getAlias(),
            'id' => $param['id'],
        ];

        unset($param['id']);
        $paramIndex['body'] = $param;

        return $paramIndex;
    }

    public function bulkLoad(array $data): array
    {
        return $this->client->bulk($this->formatForBulkIndex($data));
    }

    public function singleLoad(array $data, bool $createIndexIfNotExists): array
    {
        if (true === $this->aliasExists()) {
            return $this->client->index($this->formatForIndex($data));
        } elseif (true === $createIndexIfNotExists) {
            $this->createIndex(true);

            return $this->client->index($this->formatForIndex($data));
        }

        return [];
    }

    public function aliasExists(): bool
    {
        return $this->client->indices()->existsAlias([
            'name' => $this->getAlias(),
        ]);
    }
    
    /**
     * 
     * @return type ElasticsearchClientInterface
     */
    public function getClient(): ElasticsearchClientInterface
    {
        return $this->client;
    }
}
