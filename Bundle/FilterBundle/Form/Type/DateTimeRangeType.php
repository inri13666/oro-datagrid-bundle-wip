<?php

namespace Oro\Bundle\FilterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeRangeType extends AbstractType
{
    const NAME = 'oro_type_datetime_range';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return DateRangeType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'field_type' => DateTimeType::class,
                'field_options' => array(
                    'format' => 'yyyy-MM-dd HH:mm',
                    'view_timezone' => 'UTC',
                    'model_timezone' => 'UTC',
                ),
            )
        );
    }
}
