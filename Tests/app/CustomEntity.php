<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a custom entity for unit tests.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 *
 * @ORM\Table(name="custom_entity")
 * @ORM\Entity
 */
class CustomEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
