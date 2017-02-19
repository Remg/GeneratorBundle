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
 * Provider related to entity mappings.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
trait EntityProvider
{
    /**
     * Provides test cases for entities.
     *
     * @return array[] An array of entity mapping informations.
     *                 [
     *                 0  => The entity fully qualified class name.
     *                 1  => The bundle namespace.
     *                 2  => The bundle name.
     *                 3  => The entity namespace.
     *                 4  => The entity directory.
     *                 5  => The entity short name.
     *                 6  => The entity shortcut.
     *                 7  => The entity translation key.
     *                 8  => The entity route prefix.
     *                 9  => The bundle path.
     *                 10 => The entity path.
     *                 ]
     */
    public function entityProvider()
    {
        $entities = Yaml::parse(file_get_contents(__DIR__.'/Data/Entity.yml'));

        $mappings = [];
        foreach ($entities as $mapping) {
            $mappings[] = [$mapping];
        }

        return $mappings;
    }

    /**
     * Provides test cases for an user entity name input.
     *
     * @return array[] An array of entity name inputs.
     *                 [
     *                 'input'      => The name a user could input.
     *                 'fqcn'       => The corresponding fqcn.
     *                 'normalized' => The expected normalized input.
     *                 ]
     */
    public function entityInputProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/EntityInput.yml'));
    }

    /**
     * Provides test cases for detecting php keywords in entity names.
     *
     * @return array[] An array of entity name inputs.
     *                 [
     *                 'input'   => The entity name containing a php keyword.
     *                 'keyword' => The php keyword.
     *                 ]
     */
    public function entityPhpKeywordProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/EntityInputPhpKeyword.yml'));
    }

    /**
     * Provides test cases for detecting platform keywords in entity names.
     *
     * @return array[] An array of entity name inputs.
     *                 [
     *                 'input'     => The entity name containing a platform keyword.
     *                 'keyword'   => The platform keyword.
     *                 'playtform' => The platform name.
     *                 ]
     */
    public function entityPlatformKeywordProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/EntityInputPlatformKeyword.yml'));
    }

    /**
     * Provides test cases for detecting invalid entity names.
     *
     * @return array[] An array of entity name inputs.
     *                 [
     *                 'input'     => The invalid entity name.
     *                 ]
     */
    public function entityInvalidNameProvider()
    {
        return Yaml::parse(file_get_contents(__DIR__.'/Data/EntityInputInvalidName.yml'));
    }
}
