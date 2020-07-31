<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use AppBundle\Entity\Prize;

/**
 * Class PrizeType
 *
 * @package AppBundle\Form
 */
class PrizeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, ['label' => 'Заголовок'])
                ->add('description', null, ['label' => 'Описание'])
                ->add('image', null, ['label' => 'Изображение'])
                ->add('cost', NumberType::class, ['label' => 'Цена'])
                ->add('quantity', NumberType::class, ['label' => 'Остаток']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Prize::class
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'appbundle_prize';
    }
}
