<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'label' => 'Votre avatar:',
                // unmapped signifie que le champ n'est pas associé à une propriété d'entité.
                'mapped' => false,
                /* rendez-le facultatif afin que vous n'ayez pas à télécharger à nouveau le fichier
                a chaque fois que vous editez les details de la recette.*/
                'required' => false,
                /* les champs unmapped ne peuvent pas définir leur validation à l'aide d'attributs
                dans l'entité associée, vous pouvez donc utiliser les classes de contraintes PHP*/
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Téléversez une image valide',
                    ])
                ],
            ])
            ->add('pseudo')
            ->add('surname')
            ->add('name')
            ->add('email', EmailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'csrfId',
        ]);
    }
}
