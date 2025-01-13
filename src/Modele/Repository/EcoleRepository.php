<?php

namespace App\Sae\Modele\Repository;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\DataObject\Agregation;
use App\Sae\Modele\Repository\AbstractDataRepository;
use App\Sae\Modele\DataObject\Ecole;

class EcoleRepository extends AbstractDataRepository
{

    protected function getNomTable(): string
    {
        return "ecolePartenaire";
    }

    protected function getNomClePrimaire(): string
    {
        return "siret";
    }

    protected function getNomColonnes(): array
    {
        return ['siret', 'nomEcole', 'villeEcole', 'telEcole', 'mailEcole', 'estValide', 'mailValider', 'nonce', 'mdpHache'];
    }

    protected function construireDepuisTableauSQL(array $objetFormatTableau) : Ecole {
        return new Ecole($objetFormatTableau["siret"], $objetFormatTableau["nomEcole"], $objetFormatTableau["villeEcole"], $objetFormatTableau['telEcole'],
        $objetFormatTableau['mailEcole'], $objetFormatTableau['estValide'], $objetFormatTableau["mailValider"], $objetFormatTableau['nonce'], $objetFormatTableau['mdpHache']);
    }
    protected function formatTableauSQL(AbstractDataObject $objet): array
    {
        return array(
            "siretTag" => $objet->getSiret(),
            "nomEcoleTag" => $objet->getNomEcole(),
            "villeEcoleTag" => $objet->getVilleEcole(),
            "telEcoleTag" => $objet->getTel(),
            "mailEcoleTag" => $objet->getMail(),
            "estValideTag" => $objet->isEstValide()?1:0,
            "mailValiderTag" => $objet->isMailValider()?1:0,
            "nonceTag" => $objet->getNonce(),
            "mdpHacheTag" => $objet->getMdpHache()
        );
    }

    public function recupererEcoleFavoris($idEtudiant)
    {
        $sql = "SELECT * FROM ecolePartenaire JOIN ecoleFavoris on ecoleFavoris.siret = ecolePartenaire.siret WHERE idEtudiant = :idEtudiantTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array("idEtudiantTag" => $idEtudiant);
        $pdoStatement->execute($values);
        $tableauObjets = [];
        foreach ($pdoStatement as $objetFormatTableau) {
            $tableauObjets[] = $this->construireDepuisTableauSQL($objetFormatTableau);
        }
        return $tableauObjets;
    }

    /**
     * @param $siret
     * @return Agregation|array recupere les agrégations d'une école partenaire
     */
    public function recupererAgregations($siret)
    {
        $sql = "SELECT * from Agregations where siretCreateur = :siretTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array("siretTag" => $siret);
        try {

            $pdoStatement->execute($values);
        }
        catch (\Exception $e) {
            return null;
        }
        $tableauObjets = [];
        foreach ($pdoStatement as $objetFormatTableau) {
            $tableauObjets = (new AgregationRepository())->construireDepuisTableauSQL($objetFormatTableau);
        }
        return $tableauObjets;
    }
}
