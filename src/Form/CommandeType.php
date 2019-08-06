<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\TypeDemande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DivnInitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('demarche', EntityType::class, array(
               'class' => TypeDemande::class,
               'choice_label' => 'nom',
               'data'=>$options['defaultType']
            ));
            $builder
            ->add('codePostal', ChoiceType::class, [
                'label' => 'label.dep',
                'choices' => $options['departement']
                ])
            ->add('immatriculation', null, [
                'label' => 'label.immatriculation',
                'required' => true,
                ])
            ->add('fourthChange', CheckboxType::class, [
                'label' => 'label.fourthChange',
                'required' => false,
                ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commande::class
        ]);

        $resolver->setRequired(array('defaultType', 'departement'));
    }
}
