<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Remg\GeneratorBundle\Model\PrimaryKey;
use Remg\GeneratorBundle\Tests\Fixtures;

/**
 * Unit tests for the Field class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class PrimaryKeyTest extends TestCase
{
    use Fixtures\Provider\FieldProvider;

    /**
     * Tests the method "__construct()".
     *
     * @dataProvider fieldProvider
     */
    public function testConstruct(array $mapping)
    {
        $field = new PrimaryKey($mapping);

        $this->assertInstanceOf(
            'Remg\GeneratorBundle\Model\Field',
            $field,
            'The PrimaryKey should extend the Field class.'
        );
        $this->assertEquals($mapping['fieldName'], $field->getName(), 'The name should not have changed.');
        $this->assertEquals('integer', $field->getType(), 'The primary key type should be "integer".');
        $this->assertFalse($field->isNullable(), 'The primary key should not be nullable.');
        $this->assertTrue($field->isUnique(), 'The primary key should be unique.');
    }
}
