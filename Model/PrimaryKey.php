<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Model;

use Doctrine\DBAL\Types\Type;

/**
 * Represents a primary key field.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class PrimaryKey extends Field implements PrimaryKeyInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $mapping = null)
    {
        parent::__construct($mapping);

        $this
            ->setType(Type::INTEGER)
            ->setNullable(false)
            ->setUnique(true);
    }
}
