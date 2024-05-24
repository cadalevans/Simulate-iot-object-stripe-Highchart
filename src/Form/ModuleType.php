<?php

namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'choices' => [
                    'Mercedes' => 'Mercedes',
                    'Toyota' => 'Toyota',
                    'Nissan' => 'Nissan',
                    'Samsung' => 'Samsung',
                    'Apple' => 'Apple',
                    'Bosh' => 'Bosh',
                    'Nokia' => 'Nokia',
                    'Siemens' => 'Siemens',
                    'HTC' => 'Htc',
                    'Amazon' => 'Amazon',
                    'LG' => 'LG',
                    'Motorola' => 'Motorola',
                ],
                'placeholder' => 'Choose a name',
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Mobile Phone' => 'Mobile_Phone',
                    'Car' => 'Car',
                    'Tablet' => 'Tablet',
                    'TV' => 'TV',
                    'Light' => 'Light',
                    'Windows' => 'Windows',
                    'Door' => 'Door',
                    'Watch' => 'Watch',
                    'Laptop' => 'Laptop',
                    'Earphone' => 'Earphone',
                    'Camera' => 'Camera',
                    'Desktop' => 'Desktop',
                    'Davenport' => 'Davenport',
                    'Bed' => 'Bed',
                    'Other' => 'Other',
                ],
                'placeholder' => 'Choose a category',
            ])

            //->add('date') it's going to take the current date so the user don't have to add it
            //->add('isOperating')  because i am going to set it to true so all the new module should be working fine
            //->add('user')
/*
            ->add('buyingDate', DateType::class, [])
*/
            ->add('isNew', ChoiceType::class, [
                'label' => 'Is New?',
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'expanded' => true, // Render as checkboxes
                'multiple' => false, // Only one choice can be selected
                'choice_attr' => [
                    'Yes' => ['class' => 'custom-checkbox-margin'],
                    'No' => ['class' => 'custom-checkbox-margin'],
                ],
            ])

            ->add('fixCount', IntegerType::class, [
                'label' => 'Fix Count',
                'required' => true,
                'empty_data' => 0, // Sets default value to 0
                'attr' => ['min' => 0] // Sets minimum value allowed to 0

            ])
            //->add('prediction', TextType::class, [
            //    'label' => 'Prediction',
             //   'required' => false,
           // ])  It's going be generated automatically

            ->add('buyingDate', DateType::class, [
                'label' => 'Buying Date',
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'The buying date must be today or in the past.',
                    ]),
                ],
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
