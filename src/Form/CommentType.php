<?php

// namespace Mygooglereviews\Form;
//namespace PrestaShop\Module\MyGoogleReviews\Controller\Form;
declare(strict_types=1);

namespace PrestaShop\Module\MyGoogleReviews\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                "attr" => array(
                    "placeholder" => "The name"
                )
            ))
            ->add('description', TextType::class, array(
                "attr" => array(
                    "placeholder" => "The description"
                )
            ))
            ->add('price', NumberType::class, array(
                "attr" => array(
                    "placeholder" => "The price"
                )
            ))
//            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class);
    }
}