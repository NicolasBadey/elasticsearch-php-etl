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

use Elasticsearch\Namespaces\IndicesNamespace;

/**
 * Interface ElasticsearchClientInterface.
 *
 * write your Symfony elasticsearch-php ClientBuilder wrapper and implement this interface.
 * Doc is elasticsearch-php Client doc
 */
interface ElasticsearchClientInterface
{
    /**
     * $params['index']        = (string) The name of the index (Required)
     *        ['type']         = (string) The type of the document (Required)
     *        ['id']           = (string) Specific document ID (when the POST method is used)
     *        ['consistency']  = (enum) Explicit write consistency setting for the operation
     *        ['op_type']      = (enum) Explicit operation type
     *        ['parent']       = (string) ID of the parent document
     *        ['refresh']      = (boolean) Refresh the index after performing the operation
     *        ['replication']  = (enum) Specific replication type
     *        ['routing']      = (string) Specific routing value
     *        ['timeout']      = (time) Explicit operation timeout
     *        ['timestamp']    = (time) Explicit timestamp for the document
     *        ['ttl']          = (duration) Expiration time for the document
     *        ['version']      = (number) Explicit version number for concurrency control
     *        ['version_type'] = (enum) Specific version type
     *        ['body']         = (array) The document.
     */
    public function index(array $params): array;

    /**
     * Operate on the Indices Namespace of commands.
     */
    public function indices(): IndicesNamespace;

    /**
     * $params['index']       = (string) Default index for items which don't provide one
     *        ['type']        = (string) Default document type for items which don't provide one
     *        ['consistency'] = (enum) Explicit write consistency setting for the operation
     *        ['refresh']     = (boolean) Refresh the index after performing the operation
     *        ['replication'] = (enum) Explicitly set the replication type
     *        ['fields']      = (list) Default comma-separated list of fields to return in the response for updates
     *        ['body']        = (array) The document.
     */
    public function bulk(array $params = []): array;

    /**
     * return an array of index name that is link to the alias.
     */
    public function getIndexNameFromAlias(string $alias): array;
}
