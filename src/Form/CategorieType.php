<?php


namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


/**
 * Formulaire pour gérer l'ajout des catégories
 * @author Aurelie Demange
 */

class CategorieType extends AbstractType
{
    /**
     * Construction du formulaire
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom : ',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de la catégorie est obligatoire'])
                ]
            ])
            //Ajout du bouton de soumission du formulaire
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }
    
    /**
     * Configuration des options du formulaire
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}

