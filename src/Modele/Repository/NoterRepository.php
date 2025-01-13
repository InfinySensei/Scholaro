<?php

namespace App\Sae\Modele\Repository;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\DataObject\Noter;

class NoterRepository extends AbstractDataRepository
{
    /**
     * @param array $objetFormatTableau
     * @return Noter permet de construire une note depuis un tableau
     */

    protected function construireDepuisTableauSQL(array $objetFormatTableau): Noter
    {
        return new Noter($objetFormatTableau['etudid'],
            $objetFormatTableau['semestre_id'],
            $objetFormatTableau['nomRessource'],
            $objetFormatTableau['note']);
    }

    /**
     * @return string nom de la table qui correspond à la classe Etudiant
     */
    protected function getNomTable(): string
    {
        return 'noter';
    }

    protected function getNomClePrimaire(): string
    {
        return "etudid";
    }

    protected function getNomColonnes(): array
    {
        return ["etudid", "semestre_id", "nomRessource", "note"];
    }

    protected function formatTableauSQL(AbstractDataObject $objet): array
    {
        return array(
            "etudidTag"=> $objet->getEtudid(),
            "semestre_idTag" => $objet->getSemestre_id(),
            "nomRessourceTag" => $objet->getNomRessource(),
            "noteTag" => $objet->getNote(),
        );
    }


}