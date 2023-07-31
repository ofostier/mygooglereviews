<?php

//namespace MyGoogleReviews\Entity;
namespace Mygooglereviews\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Mygooglereviewsscore
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
    private $establishment_id;
    public function getEstablishment_id()
    {
        return $this->establishment_id;
    }
    public function setEstablishment_id($value)
    {
        $this->establishment_id = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $establishment_score;
    public function getEstablishment_score()
    {
        return $this->establishment_score;
    }
    public function setEstablishment_score($value)
    {
        $this->establishment_score = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $establishment_nbvote;
    public function getEstablishment_nbvote()
    {
        return $this->establishment_nbvote;
    }
    public function setEstablishment_nbvote($value)
    {
        $this->establishment_nbvote = $value;
    }

}