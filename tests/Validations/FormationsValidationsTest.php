<?php

namespace app\tests\Validations;

use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Tests d'intégration concernant la validation de la date de publication lors de l'ajout ou
 * de la modification d'une formation, qui vérifient le fonctionnement correct des contraintes
 * appliquées sur l'entité Formation
 * @author Aurelie Demange
 */
class FormationsValidationsTest extends KernelTestCase
{
    /**
     * Permet de créer une instance de formation valide (sans la date) pour les tests et éviter les répétitions
     * @return Formation
     */
    public function getFormation(): Formation
    {
        $playlist = new Playlist();
        $playlist->setName("Test Playlist");
        return (new Formation())
                ->setTitle("Test Formation")
                ->setPlaylist($playlist);
    }
    
    /**
     * Test qui permet de vérifier que la date du jour ou une date antérieure sera validée
     */
    public function testValidationDate()
    {
        $dateToday = new \DateTime();
        $this->assertErrors(
                $this->getFormation()->setPublishedAt($dateToday),
                0,
                "La date d'aujourd'hui devrait être acceptée"
                );
        $dateAnterieure = (new \DateTime())->sub(new \DateInterval("P3D"));
        $this->assertErrors(
                $this->getFormation()->setPublishedAt($dateAnterieure),
                0,
                "La date antérieure devrait être acceptée"
                );
    }
    
    /**
     * Test qui permet de vérifier qu'une date postérieure à la date du jour sera rejetée
     */
    public function testNonValidationDate()
    {
        $datePosterieure = (new \DateTime())->add(new \DateInterval("P5D"));
        $this->assertErrors(
            $this->getFormation()->setPublishedAt($datePosterieure),
                1,
                "La date postérieure devrait échouer"
                );
    }
    
    /**
     * Méthode d'assertion qui reçoit l'objet Formation à valider via le Validator Symfony, de
     * compter le nombre d'erreurs obtenues et que ce dernier est bien le nombre attendu
     * @param Formation $formation Objet Formation à valider
     * @param int $nbErreursAttendues Nombre d'erreurs attendu
     * @param string $message Message en cas d'échec
     */
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message="")
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
}
