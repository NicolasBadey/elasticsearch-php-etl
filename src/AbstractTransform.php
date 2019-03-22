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
 * Class AbstractTransform.
 */
abstract class AbstractTransform implements TransformInterface
{
    /**
     * {@inheritdoc}
     */
    public function transformObjects(array $objects): array
    {
        return \array_map([
            $this, 'transformObject',
        ], $objects);
    }
}
