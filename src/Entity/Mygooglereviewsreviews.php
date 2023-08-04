<?php

namespace Mygooglereviews\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Mygooglereviewsreviews
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
    private $author_name;
    public function getAuthor_name()
    {
        return $this->author_name;
    }
    public function setAuthor_name($value)
    {
        $this->author_name = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $author_url;
    public function getAuthor_url()
    {
        return $this->author_url;
    }
    public function setAuthor_url($value)
    {
        $this->author_url = $value;
    }
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $language;
    public function getLanguage()
    {
        return $this->language;
    }
    public function setLanguage($value)
    {
        $this->language = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $original_language;
    public function getOriginal_language()
    {
        return $this->original_language;
    }
    public function setOriginal_language($value)
    {
        $this->original_language = $value;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profile_photo_url;
    public function getProfile_photo_url()
    {
        return $this->profile_photo_url;
    }
    public function setProfile_photo_url($value)
    {
        $this->profile_photo_url = $value; //profile_photo_url
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $rating;
    public function getRating()
    {
        return $this->rating;
    }
    public function setRating($value)
    {
        $this->rating = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $relative_time_description;
    public function getRelative_time_description()
    {
        return $this->relative_time_description;
    }
    public function setRelative_time_description($value)
    {
        $this->relative_time_description = $value;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;
    public function getText()
    {
        return $this->text;
    }
    public function setText($value)
    {
        $this->text = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $time;
    public function getTime()
    {
        return $this->time;
    }
    public function setTime($value)
    {
        $this->time = $value;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $translated;
    public function getTranslated()
    {
        return $this->translated;
    }
    public function setTranslated($value)
    {
        $this->translated = $value;
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