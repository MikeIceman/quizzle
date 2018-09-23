<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 23.09.2018
	 * Time: 19:20
	 */

	namespace AppBundle\Form;

	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;

	class RegistrationType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder->add('firstname', null, ['label' => 'Имя']);
			$builder->add('lastname', null, ['label' => 'Фамилия']);
			$builder->add('phone', null, ['label' => 'Телефон']);
		}

		public function getParent()
		{
			return 'FOS\UserBundle\Form\Type\RegistrationFormType';
		}

		public function getBlockPrefix()
		{
			return 'app_user_registration';
		}

		// For Symfony 2.x
		public function getName()
		{
			return $this->getBlockPrefix();
		}

	}