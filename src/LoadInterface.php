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
 * Interface LoadInterface.
 */
interface LoadInterface
{
    /**
     * @return string
     *
     * return alias name
     */
    public static function getAlias(): string;

    /**
     * @param bool $live
     *
     * Allow populate to be done one an currently used index
     */
    public function setLiveMode(bool $live): void;

    /*
     * Action to perform before load
     */
    public function preLoad(): void;

    /**
     * Action to perform after Load.
     */
    public function postLoad(): void;

    /*
     * Insert many
     */
    public function bulkLoad(array $data): array;

    /**
     * @return array
     *
     * Insert one
     */
    public function singleLoad(array $data, bool $createIndexIfNotExists): array;
}
