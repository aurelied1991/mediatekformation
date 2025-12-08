<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


/**
 * Formulaire permettant de gérer les playlists côté administrateur
 * Fournit les champs nécessaires pour créer ou éditer une playlist
 * @author Aurelie Demange
 */
class PlaylistType extends AbstractType
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
            //Champ du nom de la playlist, obligatoire avec contrainte NotBlank
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire'])
                ]
            ])
            //Champ de la description de la playlist, facultatif
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 5]
            ])
            //Ajout du bouton de soumission du formulaire
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }
    
    /**
     * Configuration des options du formulaire
     * @param OptionsResolver $resolver Définit les options par défaut
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
