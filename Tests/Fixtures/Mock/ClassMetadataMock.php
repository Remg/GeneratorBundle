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
 * Mockery for the ClassMetadata class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait ClassMetadataMock
{
    /**
     * Creates a mocked ClassMetadata.
     *
     * @param string $name         The class name.
     * @param array  $fields       An array of field mappings indexed by field names.
     * @param array  $associations An array of association mappings indexed by association names.
     *
     * @return ClassMetadata A mocked ClassMetadata.
     */
    public function getClassMetadata(
        $name = null,
        array $fields = [],
        array $associations = []
    ) {
        $metadata = $this
            ->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        if (null !== $name) {
            $metadata
                ->method('getName')
                ->willReturn($name);
        }

        $metadata
            ->method('getFieldNames')
            ->willReturn(array_keys($fields));

        $mappingMap = [];
        foreach ($fields as $fieldName => $mapping) {
            $mappingMap[] = [$fieldName, $mapping];
        }

        $metadata
            ->method('getFieldMapping')
            ->will($this->returnValueMap($mappingMap));

        $metadata
            ->method('getAssociationNames')
            ->willReturn(array_keys($associations));

        $mappingMap = [];
        foreach ($associations as $fieldName => $mapping) {
            $mappingMap[] = [$fieldName, $mapping];
        }

        $metadata
            ->method('getAssociationMapping')
            ->will($this->returnValueMap($mappingMap));

        return $metadata;
    }
}
