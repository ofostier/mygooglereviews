<?php

// namespace Mygooglereviews\Form;
//namespace PrestaShop\Module\MyGoogleReviews\Controller\Form;
declare(strict_types=1);

namespace Mygooglereviews\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;

class EstablishmentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, array(
                "attr" => array(
                    "placeholder" => "Address of your establishments",
                    "label" => "Your address",
                    "readonly" => true,
                    //"value" => "Krysakids, Mallemort"
                ),
                "label" => "Address of your establishments"
            ))
            ->add('placeid', TextType::class, array(
                "attr" => array(
                    "placeholder" => "The Google ID of your establishment",
                    "label" => "The Google place ID"
                ),
                "label" => "The Google place ID"
            ))
            ->add('Refresh', ButtonType::class, array(
                "attr" => array(
                    'class' => 'btn btn-warning pull-left', 
                    
                    // 'onClick' => 'alert("you click")',
                ),
                "label" => "Retrieve your place ID"
            ))
            ->add('Save', SubmitType::class, array(
                "attr" => array(
                    'class' => 'btn btn-danger pull-right', 
                )
            ))
            ->getForm();
    }
}