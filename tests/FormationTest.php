<?php

namespace App\tests;

use App\Entity\Formation;
use DateTime;
use PHPUnit\Framework\TestCase;


/**
 * Tests unitaires de la méthode getPublishedAtString,
 * qui doit retourner la date de publication des formations au format string
 * @author Aurelie Demange
 */
class FormationTest extends TestCase
{
    /**
     * Méthode qui vérifie que getPublishedAtString() retourne la date au format d/m/Y
     * quand PublishedAt contient une date valide
     */
    public function testGetPublishedAtString()
    {
        // Création d'un objet Formation avec une date valide
        $formation = new Formation();
        $formation->setPublishedAt(new DateTime("2025-11-23"));
        // Vérifier que la date renvoyée est au format d/m/Y
        $this->assertEquals("23/11/2025", $formation->getPublishedAtString());
    }
    
    /**
     * Méthode qui permet de tester que, quand PublishedAt est vide, getPublishedAtString() retourne une chaîne vide
     */
    public function testGetPublishedAtStringEmpty()
    {
        // Création d'un nouvel objet Formation sans date
        $formation = new Formation();
        $formation->setPublishedAt(null);
        // Vérifier qu'une chaîne vide est renvoyée
        $this->assertEquals("", $formation->getPublishedAtString());
    }
}
