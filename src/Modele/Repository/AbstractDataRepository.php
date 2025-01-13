<?php
namespace App\Sae\Modele\Repository;
use App\Sae\Modele\DataObject\AbstractDataObject as AbstractDataObject;
use App\Sae\Modele\Repository\ConnexionBaseDeDonnees as ConnexionBaseDeDonnees;
abstract class AbstractDataRepository
{
    protected abstract function construireDepuisTableauSQL(array $objetFormatTableau) : AbstractDataObject;
    protected abstract function getNomTable(): string;
    protected abstract function getNomClePrimaire(): string;
    protected abstract function getNomColonnes(): array;
    protected abstract function formatTableauSQL(AbstractDataObject $objet): array;

    /**
     * @return array|null
     * retourne une liste des objets d'une même classe
     * renvoie null si aucun objet n'est créer
     */

    public function recuperer() : ?array
    {
        $tableauObjets = [];
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->query('SELECT * FROM '. $this->getNomTable());
        foreach ($pdoStatement as $objetFormatTableau) {
            $tableauObjets[] = $this->construireDepuisTableauSQL($objetFormatTableau);
        }
        return $tableauObjets;
    }
    public function ajouter(AbstractDataObject $objet)
    {
        $nomTable = $this->getNomTable();
        $nomsColonnes = join(",", $this->getNomColonnes());
        $colonnesTag = "";
        foreach ($this->getNomColonnes() as $nomColonne) {
            $colonnesTag = $colonnesTag . ":" . $nomColonne . "Tag, ";
        }
        $colonnesTag = substr($colonnesTag, 0, -2);

        $sql = "INSERT INTO $nomTable ($nomsColonnes) VALUES ($colonnesTag)";
        $pdo = ConnexionBaseDeDonnees::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = $this->formatTableauSQL($objet);
        $pdoStatement->execute($values);
        return $pdo->lastInsertId();
    }

    public function ajouterPlusieurs(array $objets)
    {
        if (empty($objets)) {
            return null;
        }

        $colonnes = $this->getNomColonnes();
        $nomTable = $this->getNomTable();
        $nomsColonnes = join(",", $this->getNomColonnes());

        $colonneObjet = [];
        $values = [];
        foreach ($objets as $index => $objet) {
            $formattedObjet = $this->formatTableauSQL($objet);
            $tags = [];
            foreach ($colonnes as $colonne) {
                $tag = ":{$colonne}Tag{$index}";
                $tags[] = $tag;

                $colonneTag = "{$colonne}Tag";
                $values[$tag] = $formattedObjet[$colonneTag];
            }
            $colonneObjet[] = "(" . join(", ", $tags) . ")";
        }
        $nomColonneObjets = join(',', $colonneObjet);
        $sql = "INSERT IGNORE INTO $nomTable ($nomsColonnes) VALUES $nomColonneObjets";
        $pdo = ConnexionBaseDeDonnees::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);
        return $pdo->lastInsertId();
    }

    public function recupererParClePrimaire(string $clefPrimTag): ?AbstractDataObject
    {
        $sql = 'SELECT * from ' . $this->getNomTable() . ' WHERE ' . $this->getNomClePrimaire() . ' = :clefPrimTag';
        // Préparation de la requête
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);

        $values = array(
            "clefPrimTag" => $clefPrimTag,
            //nomdutag => valeur, ...
        );
        // On donne les valeurs et on exécute la requête
        $pdoStatement->execute($values);

        // On récupère les résultats comme précédemment
        // Note: fetch() renvoie false si pas d'utilisateur correspondant

        $objetFormatTableau = $pdoStatement->fetch();
        if ($objetFormatTableau == null) {
            return null;
        }
        return $this->construireDepuisTableauSQL($objetFormatTableau);
    }

    public function supprimer(string $clefPrimaire): bool
    {

        $sql = 'DELETE FROM ' . $this->getNomTable() . ' WHERE ' . $this->getNomClePrimaire() . ' = :clefTag';
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = array(
            "clefTag" => $clefPrimaire,
        );
        try {
            $pdoStatement->execute($values);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    public function mettreAJour(AbstractDataObject $objet): bool
    {
        $sql = 'Update '.$this->getNomTable().' set ';
        $colonnes = $this->getNomColonnes();
        array_shift($colonnes);
        foreach ($colonnes as $colonne) {
            $setClause[] = "$colonne = :{$colonne}Tag";
        }
        $sql .= implode(", ", $setClause);
        $sql .= " WHERE " . $this->getNomClePrimaire() . " = :".$this->getNomClePrimaire()."Tag";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $values = $this->formatTableauSQL($objet);
        $pdoStatement->execute($values);
        return true;
    }


}