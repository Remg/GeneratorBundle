<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tests\Fixtures\Provider;

use Symfony\Component\Yaml\Yaml;

/**
 * Provider related to association mappings.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait AssociationProvider
{
    /**
     * Provides test cases for association mappings.
     *
     * @see unidirectionalProvider
     * @see bidirectionalProvider
     *
     * @return array An array of association mapping informations.
     *               [
     *               0 => The association name.
     *               1 => The association type.
     *               2 => The association target entity.
     *               3 => Whether the association is bidirectional.
     *               4 => Whether the association is on the owning side.
     *               5 => The mappedBy property of the association.
     *               6 => The inversedBy property of the association.
     *               ]
     */
    public function associationProvider()
    {
        $mappings = [];

        foreach ($this->bidirectionalProvider() as $relation) {
            foreach ($relation as $mapping) {
                $mappings[] = [$mapping];
            }
        }

        foreach ($this->unidirectionalProvider() as $relation) {
            foreach ($relation as $mapping) {
                $mappings[] = [$mapping];
            }
        }

        return $mappings;
    }

    /**
     * Provides test cases for bidirectional associations.
     *
     * @return array An array containing the owning association mapping informations
     *               and the inverse association mapping informations.
     *               [
     *               'owning' => [
     *               0 => The association name.
     *               1 => The association type.
     *               2 => The association target entity.
     *               3 => Whether the association is bidirectional.
     *               4 => Whether the association is on the owning side.
     *               5 => The mappedBy property of the association.
     *               6 => The inversedBy property of the association.
     *               ],
     *               'inverse' => [
     *               0 => The reversed association name.
     *               1 => The reversed association type.
     *               2 => The reversed association target entity.
     *               3 => Whether the reversed association is bidirectional.
     *               4 => Whether the reversed association is on the owning side.
     *               5 => The mappedBy property of the reversed association.
     *               6 => The inversedBy property of the reversed association.
     *               ]
     *               ]
     */
    public function bidirectionalProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/AssociationBidirectional.yml'));
    }

    /**
     * Provides test cases for unidirectional associations.
     *
     * @return array An array of unidirectional association mapping informations.
     *               [
     *               0 => The association name.
     *               1 => The association type.
     *               2 => The association target entity.
     *               3 => Whether the association is bidirectional.
     *               4 => Whether the association is on the owning side.
     *               5 => The mappedBy property of the association.
     *               6 => The inversedBy property of the association.
     *               ]
     */
    public function unidirectionalProvider()
    {
        $fixtures = Yaml::parse(file_get_contents(__DIR__.'/Data/AssociationUnidirectional.yml'));

        $mappings = [];
        foreach ($fixtures as $mapping) {
            $mapping = array_merge($mapping, [
                'bidirectional' => false,
                'owningSide'    => null,
                'mappedBy'      => null,
                'inversedBy'    => null,
            ]);

            $mappings[] = [$mapping];
        }

        return $mappings;
    }

    /**
     * Provides test cases for guessing an association type.
     *
     * @return array[] An array of an association type guess test case.
     *                 [
     *                 0 => The association name.
     *                 1 => The expected type.
     *                 ]
     */
    public function associationTypeGuessProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/AssociationTypeGuess.yml'));
    }

    /**
     * Provides test cases for guessing an association target entity.
     *
     * @return array[] An array of an association target entity guess test case.
     *                 [
     *                 0 => The association name.
     *                 1 => The bundle name of the entity owning the association.
     *                 2 => The expected target entity.
     *                 ]
     */
    public function associationTargetEntityGuessProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/AssociationTargetEntityGuess.yml'));
    }

    /**
     * Provides test cases for guessing an association inverse property.
     *
     * @return array[] An array of an association inverse property guess test case.
     *                 [
     *                 0 => The entity short name.
     *                 1 => The association type.
     *                 2 => The expected inverse property.
     *                 ]
     */
    public function associationInversePropertyGuessProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/AssociationInversePropertyGuess.yml'));
    }
}
