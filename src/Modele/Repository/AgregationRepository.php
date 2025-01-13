<?php
namespace App\Sae\Modele\Repository;

use App\Sae\Modele\DataObject\AbstractDataObject;
use App\Sae\Modele\DataObject\Agregation;

class AgregationRepository extends AbstractDataRepository
{
    protected function construireDepuisTableauSQL(array $objetFormatTableau): Agregation
    {
        return new Agregation($objetFormatTableau['idAgregation'], $objetFormatTableau['nomAgregation'], $objetFormatTableau['loginCreateur'], $objetFormatTableau['siretCreateur']);
    }

    protected function getNomTable(): string
    {
       return "agregation";
    }

    protected function getNomClePrimaire(): string
    {
        return "idAgregation";
    }

    protected function getNomColonnes(): array
    {
        return ['idAgregation', 'nomAgregation', 'loginCreateur', 'siretCreateur'];
    }

    protected function formatTableauSQL(AbstractDataObject $objet): array
    {
        return array(
            "idAgregationTag" => $objet->getIdAgregation(),
            "nomAgregationTag" => $objet->getNomAgregation(),
            "loginCreateurTag" => $objet->getLoginCreateur(),
            "siretCreateurTag" => $objet->getSiretCreateur(),
        );
    }

    /**
     * @param int $idAgregation
     * @param int $etudid
     * @return void ajoute l'agrégation à un étudiant
     */
    public function ajouterEtudiant(int $idAgregation, int $etudid, float $note): void
    {
        $sql = "INSERT INTO etudiantAgregation (idAgregation, etudid, note) VALUES (:idAgregationTag, :etudidTag, :noteTag) ";
        $pdoStatement = (ConnexionBaseDeDonnees::getPdo())->prepare($sql);
        $values = array(
            "idAgregationTag" => $idAgregation,
            "etudidTag" => $etudid,
            "noteTag" => $note
        );
        $pdoStatement->execute($values);
    }

    /**
     * @param int $idAgregation
     * @return array retourne la liste des ressources qui ont été agregées
     */
    public function listeRessourcesAgregees(int $idAgregation): array
    {
        $sql = "SELECT nomRessource, coefficient
        FROM agregerRessource
        WHERE idAgregation = :idAgregationTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "idAgregationTag" => $idAgregation
        );
        $tab = [];
        $pdoStatement->execute($values);
        foreach($pdoStatement as $row){
            $tab[] = $row;
        }
        return $tab;
    }

    /**
     * @param int $idAgregation
     * @return array retourne la liste des agregations qui ont été agrégées
     */
    public function listeAgregationsAgregees(int $idAgregation): array
    {
        $sql = "SELECT idAgregationAgregee, coefficient 
        FROM agregerAgregation aa 
        WHERE idAgregation = :idAgregationTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "idAgregationTag" => $idAgregation
        );
        $pdoStatement->execute($values);
        $tab = [];
        foreach ($pdoStatement as $row){
            $tab[] = $row;
        }
        return $tab;
    }

    /**
     * @param int $idAgregation
     * @return float retourne la moyenne des notes d'une agrégation
     */
    public function moyenne(int $idAgregation): float
    {
        $sql = "SELECT AVG(note) AS moyenne 
                FROM etudiantAgregation 
                 WHERE idAgregation = :idAgregationTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "idAgregationTag" => $idAgregation
        );
        $pdoStatement->execute($values);
        $res = $pdoStatement->fetch();
        $moyenne = $res['moyenne'] !== null ? (float)$res['moyenne'] : 0.0;
        return round($moyenne, 2);
    }

    public function recupererEtudiants(int $idAgregation): array{
        $sql = "SELECT e.* FROM etudiant e JOIN etudiantAgregation a ON e.etudid = a.etudid WHERE a.idAgregation = :idAgregationTag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "idAgregationTag" => $idAgregation
        );
        $pdoStatement->execute($values);
        $tab = array();
        foreach ($pdoStatement as $row){
            $tab[] = (new EtudiantRepository())->construireDepuisTableauSQL($row);
        }
        return $tab;
    }

    public function recupererParUtilisateur(string $utilisateur)
    {
        if ($utilisateur == "prof"){
            $sql = "SELECT * FROM agregation WHERE loginCreateur = :utilisateurTag";
        } else {
            $sql = "SELECT * FROM agregation WHERE siretCreateur = :utilisateurTag";
        }
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "utilisateurTag" => $utilisateur
        );
        $pdoStatement->execute($values);
        $tab = array();
        foreach ($pdoStatement as $row){
            $tab[] = (new AgregationRepository())->construireDepuisTableauSQL($row);
        }
        return $tab;
    }
}
