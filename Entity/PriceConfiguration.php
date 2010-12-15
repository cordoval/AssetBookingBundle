<?php

namespace Application\AssetBookingBundle\Entity;

/**
 * Application\AssetBookingBundle\Entity\PriceConfiguration
 */
class PriceConfiguration
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var string $accessSequence
     */
    private $accessSequence;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Application\AssetBookingBundle\Entity\PriceCondition
     */
    private $conditions;

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set accessSequence
     *
     * @param string $accessSequence
     */
    public function setAccessSequence($accessSequence)
    {
        $this->accessSequence = $accessSequence;
    }

    /**
     * Get accessSequence
     *
     * @return string $accessSequence
     */
    public function getAccessSequence()
    {
        return $this->accessSequence;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set conditions
     *
     * @param Application\AssetBookingBundle\Entity\PriceCondition $conditions
     */
    public function setConditions(\Application\AssetBookingBundle\Entity\PriceCondition $conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * Get conditions
     *
     * @return Application\AssetBookingBundle\Entity\PriceCondition $conditions
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}