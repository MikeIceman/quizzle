<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
	        ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('firstname', null, ['label' => 'Имя'])
            ->add('lastname', null, ['label' => 'Фамилия'])
            ->add('phone', null, ['label' => 'Телефон'])
	        ->add('plainPassword', RepeatedType::class, array(
		        'type' => PasswordType::class,
		        'options' => array(
			        'translation_domain' => 'FOSUserBundle',
			        'attr' => array(
				        'autocomplete' => 'new-password',
			        ),
		        ),
		        'first_options' => array('label' => 'form.password'),
		        'second_options' => array('label' => 'form.password_confirmation'),
		        'invalid_message' => 'fos_user.password.mismatch',
	        ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
	        'csrf_token_id' => 'edit_user',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
