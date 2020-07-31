<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use AppBundle\Entity\User;

/**
 * Class UserType
 *
 * @package AppBundle\Form
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', null, ['label' => 'form.username', 'translation_domain' => 'FOSUserBundle'])
                ->add('email', EmailType::class, ['label' => 'form.email', 'translation_domain' => 'FOSUserBundle'])
                ->add('firstname', null, ['label' => 'Имя'])
                ->add('lastname', null, ['label' => 'Фамилия'])
                ->add('phone', null, ['label' => 'Телефон'])
                ->add(
                    'plainPassword',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'options' => [
                            'translation_domain' => 'FOSUserBundle',
                            'attr' => [
                                'autocomplete' => 'new-password',
                            ],
                        ],
                        'first_options' => ['label' => 'form.password'],
                        'second_options' => ['label' => 'form.password_confirmation'],
                        'invalid_message' => 'fos_user.password.mismatch',
                    ]
                );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'csrf_token_id' => 'edit_user',
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }
}
