<div class="marge">
    <div class="Filtre">
        <h1>Filtre :</h1>
        <!-- Affichage des différents filtres disponibles -->
        <form class="filtres" method=<?php

        use App\Sae\Configuration\ConfigurationSite;
        use App\Sae\Lib\ConnexionUtilisateur;
        use App\Sae\Lib\Preferences;
        use App\Sae\Modele\Repository\AgregationRepository;

        if (ConfigurationSite::getDebug()) echo "get"; else echo "post" ?> action="controleurFrontal.php">
            <?php
            /** @var \App\Sae\Modele\DataObject\Agregation[] $agregations */
            if ($agregations) {
                foreach ($agregations as $agregation) {
                    $id = $agregation->getIdAgregation();
                    if (in_array($agregation->getIdAgregation(), Preferences::lire("choixFiltres"))) {
                        echo "<input type=checkbox name='idAgregations$id' value='" . $agregation->getIdAgregation() . "' id='" . $agregation->getIdAgregation() . "' checked onchange='this.form.submit()'>
                <label for='" . $agregation->getIdAgregation() . "' > " . htmlspecialchars($agregation->getNomAgregation()) . " </label>";
                    } else {
                        echo "<input type=checkbox name='idAgregations$id' value='" . $agregation->getIdAgregation() . "' id='" . $agregation->getIdAgregation() . "' onchange='this.form.submit()'>
                <label for='" . $agregation->getIdAgregation() . "' > " . htmlspecialchars($agregation->getNomAgregation()) . " </label> 
            ";
                    }
                }
            }
            ?>
            <input type='hidden' name='action' value='enregistrerFiltres'>
<!--            <input type="hidden" name="controleur" value="etudiant">-->
        </form>
    </div>

    <!-- Affichage du Titre de la page ainsi que le bouton d'importation -->
    <div class="titre">
        <div class="table-title">
            <h1>Liste Etudiant</h1>
        </div>

        <?php
        if (\App\Sae\Lib\ConnexionUtilisateur::estAdministrateur()) {
            echo '<div class="bouton-importation">
                        <form action="controleurFrontal.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="controleur" value="etudiant">
                            <input type="hidden" name="action" value="ajouterDepuisCSV">
                            <div>
                                <input type="file" id="file" name="file" accept=".csv" style="display: none;"
                                       onchange="this.form.submit()">
                                <label for="file">
                                    Importer Etudiants (.csv)
                                </label>
                            </div>
                        </form>
                    </div>';
        }
        ?>

    </div>

    <!-- Affichage du Tableau contenant la liste des étudiants -->
    <table>
        <thead>
        <tr>
            <th>Id Etudiant</th>
            <th>Nom</th>
            <th>Prenom</th>
            <?php
            $agregationFiltres = Preferences::lire("choixFiltres");
            if (!empty($agregations) && !empty($agregationFiltres)) {
                foreach ($agregationFiltres as $agregationFiltre) {
                    foreach ($agregations as $agregation) {
                        if ($agregation->getIdAgregation() == $agregationFiltre) {


                            $idAgregation = $agregation->getIdAgregation();
                            echo '<th>' . htmlspecialchars($agregation->getNomAgregation()) .
                                '<a href="?controleur=etudiant&action=triDecroissant&idAgregation=' . $idAgregation . '"> <img class="fleche" src="../ressources/images/fleche_haut.png" alt="fleche_haut"> </a>
                    <a href="?controleur=etudiant&action=triCroissant&idAgregation=' . $idAgregation . '"><img class="fleche" src="../ressources/images/fleche_bas.png" alt="fleche_bas"></a> </th>';
                        }
                    }
                }
            } ?>

        </tr>
        </thead>


        <tbody>
        <?php
        /**
         * @var \App\Sae\Modele\DataObject\Etudiant[] $etudiants
         */

        if (ConnexionUtilisateur::estAdministrateur()) $regarder = "admin";
        else if (ConnexionUtilisateur::estEcolePartenaire(ConnexionUtilisateur::getLoginUtilisateurConnecte()) || ConnexionUtilisateur::estProfesseur()) $regarder = "ecole";
        else $regarder = "";


        foreach ($etudiants as $etudiant) {
            $idEtudiant = $etudiant->getEtudid();
            echo '
       <tr>
        <td> <a href="?controleur=etudiant&action=afficherEtudiantPage&regarder='.$regarder.'&idEtudiant=' . $idEtudiant . '">' . $idEtudiant . '</a></td>
        <td> <a href="?controleur=etudiant&action=afficherEtudiantPage&regarder='.$regarder.'&idEtudiant=' . $idEtudiant . '">' . htmlspecialchars($etudiant->getNomEtu()) . '</a> </td> 
        <td> <a href="?controleur=etudiant&action=afficherEtudiantPage&regarder='.$regarder.'&idEtudiant=' . $idEtudiant . '">' . htmlspecialchars($etudiant->getPrenomEtu()) . '</a></td>
        
        
        ';
            if (!empty($agregations) && !empty($agregationFiltres)) {
                foreach ($agregationFiltres as $agregationFiltre) {
                    foreach ($agregations as $agregation) {
                        if ($agregation->getIdAgregation() == $agregationFiltre) {
                            echo '<td> <a href="?controleur=etudiant&action=afficherEtudiantPage&'.$regarder.'=admin&idEtudiant=' . $etudiant->getEtudid() . '">' . (new \App\Sae\Modele\Repository\EtudiantRepository())->getNoteEtudiantAgregation($etudiant->getEtudid(), $agregation->getIdAgregation()) . '</a></td>';
                        }
                    }
                }
            }
            echo '</tr>';
        }


        ?>
        </tbody>
    </table>
    <?php
    if (ConnexionUtilisateur::estEcolePartenaire(ConnexionUtilisateur::getLoginUtilisateurConnecte()) && empty($etudiants)) {
        echo '<h3 class="pas-etudiant">Il n\'y a aucun étudiant qui vous a choisis en tant ecole favorite</h3>';
    } else if (empty($etudiants)) {
        echo '<h3 class="pas-etudiant">Il n\'y a aucun étudiant, veuillez en importer</h3>';
    } ?>
</div>

