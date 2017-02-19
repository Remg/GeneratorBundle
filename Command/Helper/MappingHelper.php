<?php

/**
 * This file is part of the RemgGeneratorBundle package.
 *
 * (c) Rémi Gardien <remi@gardien.biz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Remg\GeneratorBundle\Command\Helper;

use Remg\GeneratorBundle\Mapping\MappingGuesserInterface;
use Remg\GeneratorBundle\Mapping\MappingValidatorInterface;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * Abstract helper for all mapping related helpers.
 *
 * @author Rémi Gardien <remi@gardien.biz>
 */
abstract class MappingHelper implements StyleAwareInterface
{
    /**
     * Contains a MappingValidatorInterface instance.
     *
     * @var MappingValidatorInterface
     */
    protected $validator;

    /**
     * Contains a MappingGuesserInterface instance.
     *
     * @var MappingGuesserInterface
     */
    protected $guesser;

    /**
     * Contains a StyleInterface instance.
     *
     * @var StyleInterface
     */
    protected $display;

    /**
     * Constructor.
     *
     * @param MappingValidatorInterface $validator
     * @param MappingGuesserInterface   $guesser
     */
    public function __construct(
        MappingValidatorInterface $validator,
        MappingGuesserInterface $guesser
    ) {
        $this->validator = $validator;
        $this->guesser = $guesser;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplay(StyleInterface $display)
    {
        $this->display = $display;
    }
}
