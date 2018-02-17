<?php

namespace FeatureToggleBundle\Twig;

use FeatureToggleBundle\Entity\FeatureToggleRepository;

/**
 * Class FeatureToggleExtension
 * @package FeatureToggleBundle\Twig
 */
class FeatureToggleExtension extends \Twig_Extension
{
    /**
     * @var FeatureToggleRepository
     */
    private $featureToggleRepo;

    /**
     * FeatureToggleExtension constructor.
     * @param FeatureToggleRepository $featureToggleRepository
     */
    public function __construct(
        FeatureToggleRepository $featureToggleRepository
    )
    {
        $this->featureToggleRepo = $featureToggleRepository;
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
        return $this->featureToggleRepo->isEnabled($feature_name);
    }

}