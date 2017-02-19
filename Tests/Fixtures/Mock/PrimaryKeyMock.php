<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Fixtures\Mock;

/**
 * Mockery for the PrimaryKey class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait PrimaryKeyMock
{
    /**
     * Creates a mocked PrimaryKeyInterface.
     *
     * @return PrimaryKeyInterface A mocked PrimaryKeyInterface.
     */
    protected function getPrimaryKey($name = 'id')
    {
        $field = $this
            ->getMockBuilder('Remg\GeneratorBundle\Model\PrimaryKeyInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $field
            ->method('getName')
            ->willReturn($name);

        return $field;
    }
}
