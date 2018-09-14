<?php

namespace FeatureToggleBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use FeatureToggleBundle\Entity\FeatureToggle;

/**
 * Class FeatureToggleExtension
 * @package FeatureToggleBundle\Twig
 */
class FeatureToggleExtension extends \Twig_Extension
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * FeatureToggleExtension constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Return a list of functions to add to the existing list
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('is_feature_enabled', [$this, 'isFeatureEnabled'])
        ];
    }

    /**
     * Returns if a feature is enabled
     * @param $feature_name
     * @return bool
     */
    public function isFeatureEnabled($feature_name){
        return $this->entityManager->getRepository(FeatureToggle::class)->isEnabled($feature_name);
    }

}
