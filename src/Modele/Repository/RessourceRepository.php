<?php

namespace App\Sae\Modele\Repository;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\DataObject\Ressource;

class RessourceRepository extends AbstractDataRepository
{

    protected function construireDepuisTableauSQL(array $objetFormatTableau): AbstractDataObject
    {
        return new Ressource($objetFormatTableau['nomRessource']);
    }

    protected function getNomTable(): string
    {
        return 'ressource';
    }

    protected function getNomClePrimaire(): string
    {
        return 'nomRessource';
    }

    protected function getNomColonnes(): array
    {
        return ['nomRessource'];
    }

    protected function formatTableauSQL(AbstractDataObject $objet): array
    {

        return array(
            "nomRessourceTag"=> $objet->getNomRessource()
        );
    }

    public function moyenne(string $nomRessource): float
    {
        $sql = "SELECT AVG(note) AS moyenne 
                FROM noter 
                 WHERE nomRessource = :nomRessourceTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "nomRessourceTag" => $nomRessource
        );
        $pdoStatement->execute($values);
        $res = $pdoStatement->fetch();
        $moyenne = $res['moyenne'] !== null ? (float)$res['moyenne'] : 0.0;
        return round($moyenne, 2);
    }

}
