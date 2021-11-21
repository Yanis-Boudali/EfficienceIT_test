<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class, array(
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom*',
                ]
            ))
            ->add('surname', TextType::class, array(
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom*',
                ]
            ))
            ->add('email', TextType::class, array(
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adresse email*',
                ]
            ))
            ->add('departement', EntityType::class, array(
                'label' => false,
                'placeholder' => 'Département*',
                'class' => Departement::class,
                'choice_label' => 'name',
                'query_builder' => function (DepartementRepository $d) {
                    return $d->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
            ))
            ->add('message', TextareaType::class, array(
                'label' => false,
                'attr' => [
                    'placeholder' => 'Message*',
                ]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
