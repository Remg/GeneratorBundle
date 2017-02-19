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
 * Mockery for the Association class.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait AssociationMock
{
    /**
     * Creates a mocked Association.
     *
     * @param array $mapping The association mapping informations.
     *                       [
     *                       'fieldName'    => The association name.
     *                       'type'         => The association type.
     *                       'targetEntity' => The association target entity.
     *                       'mappedBy'     => The association mappedBy property.
     *                       'inversedBy'   => The association inversedBy property.
     *                       ]
     *
     * @return Association A mocked Association.
     */
    protected function getAssociation(array $mapping = [])
    {
        $association = $this
            ->getMockBuilder('Remg\GeneratorBundle\Model\AssociationInterface')
            ->getMock();

        if (isset($mapping['fieldName'])) {
            $association
                ->method('getName')
                ->willReturn($mapping['fieldName']);
        }

        if (isset($mapping['type'])) {
            $association
                ->method('getType')
                ->willReturn($mapping['type']);

            if (isset($mapping['bidirectional'])) {
                $association
                    ->method('getDoctrineType')
                    ->willReturn(
                        $this->getDoctrineType(
                            $mapping['type'],
                            $mapping['bidirectional']
                        )
                    );
            }
        }

        if (isset($mapping['targetEntity'])) {
            $association
                ->method('getTargetEntity')
                ->willReturn($mapping['targetEntity']);
        }

        if (isset($mapping['bidirectional'])) {
            $association
                ->method('isBidirectional')
                ->willReturn($mapping['bidirectional']);
        }

        if (isset($mapping['owningSide'])) {
            $association
                ->method('isOwningSide')
                ->willReturn($mapping['owningSide']);
        }

        if (isset($mapping['mappedBy'])) {
            $association
                ->method('getMappedBy')
                ->willReturn($mapping['mappedBy']);
        }

        if (isset($mapping['inversedBy'])) {
            $association
                ->method('getInversedBy')
                ->willReturn($mapping['inversedBy']);
        }

        return $association;
    }

    /**
     * Returns the Doctrine type of an association alias.
     *
     * Handle the case: One-To-Many, Unidirectional with Join Table:
     *
     * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
     *
     * @param string $type          The alias type of an association.
     * @param int    $bidirectional Whether the association is bidirectional.
     *
     * @return int
     */
    public function getDoctrineType($type, $bidirectional)
    {
        if ('OneToMany' === $type && false === $bidirectional) {
            return 8;
        }

        switch ($type) {
            case 'OneToOne':
                return 1;
            case 'ManyToOne':
                return 2;
            case 'OneToMany':
                return 4;
            case 'ManyToMany':
                return 8;
        }
    }
}
