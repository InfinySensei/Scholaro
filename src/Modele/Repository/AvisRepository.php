<?php

namespace App\Sae\Modele\Repository;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\DataObject\Avis;

class AvisRepository extends AbstractDataRepository
{
    /**
     * @param array $objetFormatTableau
     * @return Avis permet de construire un avis depuis un tableau
     */

    protected function construireDepuisTableauSQL(array $objetFormatTableau): Avis
    {
        return new Avis($objetFormatTableau['avis']);
    }

    /**
     * @return string nom de la table qui correspond à la classe Avis
     */
    protected function getNomTable(): string
    {
        return 'avis';
    }

    protected function getNomClePrimaire(): string
    {
        return "avis";
    }

    protected function getNomColonnes(): array
    {
        return ['avis'];
    }

    protected function formatTableauSQL(AbstractDataObject $objet): array
    {
        return array(
            "avisTag" => $objet->getAvis(),
        );
    }

    public function existeAvis(string $avis): bool {
        {
            $sql = "SELECT avis FROM avis WHERE avis = :avis";
            $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
            $values = array(
                "avis" => $avis,
            );
            $pdoStatement->execute($values);
            $result = $pdoStatement->fetch();
            return $result !== false;
        }
    }

    public function recupererAvis($idEtudiant)
    {
        $sql = "SELECT etudid, avis, formation FROM etreAvis WHERE etudid = :idEtudiantTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array("idEtudiantTag" => $idEtudiant);
        $pdoStatement->execute($values);
        $tableauObjets = [];
        foreach ($pdoStatement as $objetFormatTableau) {
            $tableauObjets[$objetFormatTableau["siret"]] = [$objetFormatTableau["avis"], $objetFormatTableau["commentaire"]];
        }
        return $tableauObjets;
    }

}