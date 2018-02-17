<?php

namespace FeatureToggleBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class FeatureToggle
 * @package FeatureToggleBundle\Entity
 * @ORM\Table(name="feature_toggle")
 * @ORM\Entity(repositoryClass="FeatureToggleBundle\Entity\FeatureToggleRepository")
 */
class FeatureToggle
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     * @Column(name="active", type="integer", nullable=false, options={"default" : 0})
     */
    private $active = 1;

    /**
     * @var integer
     * @Column(name="public", type="integer", nullable=false, options={"default" : 0})
     */
    private $public = 0;

    /**
     * @var string
     * @Column(name="feature_name", type="string", nullable=true, length=255)
     */
    private $feature_name;

    /**
     * @var string
     * @Column(name="description", type="string", nullable=true, length=255)
     */
    private $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return FeatureToggle
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param int $active
     * @return FeatureToggle
     */
    public function setActive($active)
    {
        $this->active = (int)$active;
        return $this;
    }

    /**
     * @return string
     */
    public function getFeatureName()
    {
        return $this->feature_name;
    }

    /**
     * @param string $feature_name
     * @return FeatureToggle
     */
    public function setFeatureName($feature_name)
    {
        $this->feature_name = $feature_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return FeatureToggle
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getPublic(): int
    {
        return (int)$this->public;
    }

    /**
     * @param int $public
     * @return FeatureToggle
     */
    public function setPublic($public)
    {
        $this->public = (int)$public;
        return $this;
    }


}