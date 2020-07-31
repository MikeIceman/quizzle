<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;

/**
 * Class RegistrationType
 *
 * @package AppBundle\Form
 */
class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', null, ['label' => 'Имя'])
                ->add('lastname', null, ['label' => 'Фамилия'])
                ->add('phone', null, ['label' => 'Телефон']);
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return RegistrationFormType::class;
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    /**
     * For Symfony 2.x
     * @return string|null
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
