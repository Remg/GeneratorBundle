<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Tools;

use Remg\GeneratorBundle\Exception\BundleNotFoundException;

/**
 * Bundle manager.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
class BundleManager implements BundleManagerInterface
{
    /**
     * Contains an array of the enabled bundles.
     *
     * @var Symfony\Component\HttpKernel\Bundle\BundleInterface[]
     */
    private $bundles = [];

    /**
     * Constructor.
     *
     * @param Symfony\Component\HttpKernel\Bundle\BundleInterface[] $bundles
     */
    public function __construct(array $bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBundle($name)
    {
        try {
            $this->getBundle($name);
        } catch (BundleNotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getBundle($name)
    {
        if (!isset($this->bundles[$name])) {
            /* @throws BundleNotFoundException */
            $this->bundles[$name] = $this->findBundle($name);
        }

        return $this->bundles[$name];
    }

    /**
     * Finds a bundle.
     *
     * @param string $name The name of the bundle to find.
     *
     * @throws BundleNotFoundException If the bundle can not be found.
     *
     * @return Symfony\Component\HttpKernel\Bundle\BundleInterface The bundle.
     */
    private function findBundle($name)
    {
        foreach ($this->bundles as $bundle) {
            if (
                $bundle->getName() === $name ||
                $bundle->getNamespace() === $name
            ) {
                return $bundle;
            }
        }

        if (false !== strpos($name, '\\')) {
            $parts = explode('\\', $name);
            array_pop($parts);

            return $this->findBundle(implode('\\', $parts));
        }

        throw new BundleNotFoundException(sprintf(
            'The bundle "%s" does not exist.', $name
        ));
    }
}
