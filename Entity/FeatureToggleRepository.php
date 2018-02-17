<?php

namespace FeatureToggleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class FeatureToggleRepository
 * @package FeatureToggleBundle\Entity
 */
class FeatureToggleRepository extends EntityRepository
{

    protected $cacheFeatures = [];
    /**
     * Is a feature is enabled
     * @param $feature_name
     * @return bool
     */
    public function isEnabled($feature_name){
        if(array_key_exists($feature_name, $this->cacheFeatures)){
            return $this->cacheFeatures[$feature_name];
        }
        /** @var FeatureToggle $feature */
        $feature = $this->findOneBy(['feature_name'=>$feature_name]);
        if(!$feature){
            return false;
        }
        $this->cacheFeatures[$feature_name] = (bool)$feature->getActive();
        return $this->cacheFeatures[$feature_name];
    }

}