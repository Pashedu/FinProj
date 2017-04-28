<?php

namespace Softgroup\FinalBundle\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('messagetext')->add('password')->add('email');
        $builder->add('TTL', ChoiceType::class, array(
            'choice_list' => new ChoiceList(
                array('0','1','2','3','4','5'),
                array('set TTL','1 hour','2 hours','1 day','3 days','1 week')
            ),
            'choices_as_values' => true,
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Softgroup\FinalBundle\Entity\Message'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'softgroup_finalbundle_message';
    }


}
