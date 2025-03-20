<?php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('depart', TextType::class, ['label' => 'Lieu de départ'])
        ->add('arrivee', TextType::class, ['label' => 'Lieu d\'arrivée'])
        ->add('stopOption', CheckboxType::class, ['label' => 'Ajouter un stop ?', 'required' => false])
        ->add('stopLieu', TextType::class, ['label' => 'Lieu du stop', 'required' => false])
        ->add('siegeBebe', CheckboxType::class, ['label' => 'Siège bébé ?', 'required' => false])
        ->add('typeVehicule', ChoiceType::class, [
            'choices' => [
                'Green' => 'Green',
                'Berline' => 'Berline',
                'Van' => 'Van',
                'Grand Coffre' => 'Grand Coffre'
            ]
        ])
        ->add('save', SubmitType::class, ['label' => 'Estimer le prix']);
}
