<?php

namespace FeatureToggleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class FeatureToggleRepository
 * @package FeatureToggleBundle\Entity
 */
class FeatureToggleRepository extends EntityRepository
{

    protected $cacheFeatures = null;
    /**
     * Is a feature is enabled
     * @param $feature_name
     * @return bool
     */
    public function isEnabled($feature_name){
        if($this->cacheFeatures === null){
            $this->cacheFeatures = [];
            /** @var FeatureToggle $feature */
            foreach ($this->findBy(['active' => 1]) as $feature){
                $this->cacheFeatures[$feature->getFeatureName()] = (bool)$feature->getActive();
            }
        }
        if(array_key_exists($feature_name, $this->cacheFeatures)){
            return $this->cacheFeatures[$feature_name];
        }
        return false;

    }

}