<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * Formulaire permettant de gérer les formations côté administrateur
 * @author Aurélie Demange
 */
class FormationType extends AbstractType
{
    /**
     * Construction du formulaire
     * @param FormBuilderInterface $builder Objet permettant de construire le formulaire
     * @param array $options Tableau d'options pour le formulaire
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //Champ du titre, obligatoire avec contrainte NotBlank
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire'])
                ]
            ])
            //Champ de la description
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 5]
            ])
            // Champ de la date, obligatoire et ne doit pas être postérieure à la date du jour
            ->add('published_at', DateType::class, [
                'widget' => 'single_text',
                //Initialise le champ à la date du jour s'il ne comporte pas déjà une date
                'data' => isset($options['data']) && $options['data']
                    ->getPublishedAt() != null ? $options['data']->getPublishedAt() : new DateTime('now'),
                'label' => 'Date',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire']),
                    new LessThanOrEqual([
                       'value' => new DateTime('now'),
                       'message' => 'La date ne peut pas être postérieure à la date du jour.',
                    ]),
                ],
            ])
            //Champ catégories : affiche liste à choix multiples liés à l'entity 'Categorie'
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégories',
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'size' => 3  // montre 3 lignes visibles dans le select
                ]
            ])
            //afficher liste à choix multiples liés à l'entity 'Playlist'
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'multiple' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le choix de la playlist est obligatoire'])
                ]
            ])
            //Ajout du bouton de soumission du formulaire
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }
    
    /**
     * Configuration des options du formulaire
     * @param OptionsResolver $resolver Définit options par défaut
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
