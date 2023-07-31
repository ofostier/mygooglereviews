<?php

//namespace MyGoogleReviews\Entity;
namespace Mygooglereviews\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Mygooglereviews
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $address;
    public function getAddress()
    {
        return $this->address;
    }
    public function setAddress($value)
    {
        $this->address = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $placeid;
    public function getPlaceid()
    {
        return $this->placeid;
    }
    public function setPlaceid($value)
    {
        $this->placeid = $value;
    }

}