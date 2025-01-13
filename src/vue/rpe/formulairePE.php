<div class="marge">
    <table>
        <thead>
        <tr>
            <th>Avis prédéfinis</th>
        </tr>
        </thead>


        <tbody>
        <?php
        use App\Sae\Modele\DataObject\Avis;

        /**
         * @var Avis[] $avisListe
         */

        foreach ($avisListe as $avis) {
            $avisURL = urlencode($avis->getAvis());
            echo '
   <tr>
    <td>' . htmlspecialchars($avis->getAvis()). '<a href="controleurFrontal.php?controleur=etudiant&action=supprimerAvis&avis=' . $avisURL .'"><img class="corbeille" src = "../ressources/images/delete.png" alt = "corbeille" ></a></td>
    </tr>';
        }
        ?>
        </tbody>
    </table>

</div>

<form method="<?php if (\App\Sae\Configuration\ConfigurationSite::getDebug()) echo "get"; else echo "post" ?>" action="controleurFrontal.php">
    <fieldset>
        <input type='hidden' name='action' value='enregistrerAvis'>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp_id">Ajouter un avis prédéfini :</label>
            <input class="InputAddOn-field" value="" placeholder="" name="avis" id="avis_id" required>
        </p>
        <p>
            <input type="submit" value="Envoyer">
        </p>
    </fieldset>
</form>

<div class="marge">
    <table>
        <thead>
        <tr>
            <th>Formations prédéfinies</th>
        </tr>
        </thead>


        <tbody>
        <?php
        use App\Sae\Modele\DataObject\Formation;

        /**
         * @var Formation[] $formations
         */

        foreach ($formations as $formation) {
            $formationURL = urlencode($formation->getFormation());
            echo '
   <tr>
    <td>' . htmlspecialchars($formation->getFormation()). '<a href="controleurFrontal.php?controleur=etudiant&action=supprimerFormation&nomFormation=' . $formationURL .'"><img class="corbeille" src = "../ressources/images/delete.png" alt = "corbeille" ></td>
    </tr>';
        }
        ?>
        </tbody>
    </table>

</div>

<form method="<?php if (\App\Sae\Configuration\ConfigurationSite::getDebug()) echo "get"; else echo "post" ?>" action="controleurFrontal.php">
    <fieldset>
        <input type='hidden' name='action' value='enregistrerFormation'>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp_id">Ajouter une formation prédéfinie :</label>
            <input class="InputAddOn-field" value="" placeholder="" name="nomFormation" id="formation_id" required>
        </p>
        <p>
            <input type="submit" value="Envoyer">
        </p>
    </fieldset>
</form>