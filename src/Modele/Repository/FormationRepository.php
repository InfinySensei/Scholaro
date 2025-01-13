<?php

namespace App\Sae\Modele\Repository;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\DataObject\Formation;

class FormationRepository extends AbstractDataRepository
{
    /**
     * @param array $objetFormatTableau
     * @return Formation permet de construire un avis depuis un tableau
     */

    protected function construireDepuisTableauSQL(array $objetFormatTableau): Formation
    {
        return new Formation($objetFormatTableau['nomFormation']);
    }

    /**
     * @return string nom de la table qui correspond à la classe Formation
     */
    protected function getNomTable(): string
    {
        return 'formation';
    }

    protected function getNomClePrimaire(): string
    {
        return "nomFormation";
    }

    protected function getNomColonnes(): array
    {
        return ['nomFormation'];
    }

    protected function formatTableauSQL(AbstractDataObject $objet): array
    {
        return array(
            "nomFormationTag" => $objet->getFormation(),
        );
    }

    public function existeFormation(string $nomFormation): bool {
        {
            $sql = "SELECT nomFormation FROM formation WHERE nomFormation = :nomFormation";
            $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
            $values = array(
                "nomFormation" => $nomFormation,
            );
            $pdoStatement->execute($values);
            $result = $pdoStatement->fetch();
            return $result !== false;
        }
    }
}