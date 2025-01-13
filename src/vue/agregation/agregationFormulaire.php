<form method="<?php use App\Sae\Lib\Preferences;

if (\App\Sae\Configuration\ConfigurationSite::getDebug()) echo "get"; else echo "post" ?>"
      action='controleurFrontal.php' class="form-semestres">
    <fieldset class="fieldset-semestres">
        <legend class="legend-semestres">Semestres</legend>
        <div class="semestre-options">
            <?php
            for ($i = 1; $i < 6; $i++) {
                if (in_array($i, Preferences::lire("choixSemestre"))) {
                    echo "
                <label for='numSemestre$i' class='label-semestre'> $i </label>
                <input type='checkbox' class='checkbox-semestre' name='numSemestre$i' id='numSemestre$i' value=$i checked onchange='this.form.submit()'>
                ";
                } else {
                    echo "
                <label for='numSemestre$i' class='label-semestre'> $i </label>
                <input type='checkbox' class='checkbox-semestre' name='numSemestre$i' id='numSemestre$i' value=$i onchange='this.form.submit()'>
                ";
                }
            }
            ?>
        </div>
    </fieldset>
    <input type='hidden' name='action' value='enregistrerSemestre'>
</form>

<form method="<?php if (\App\Sae\Configuration\ConfigurationSite::getDebug()) echo "get"; else echo "post" ?>"
      action='controleurFrontal.php' class="form-agregation">
    <fieldset class="fieldset-agregation">
        <legend class="legend-agregation">Agrégations</legend>
        <?php
        $semestres = Preferences::lire("choixSemestre");
        if (empty($semestres) && empty($agregations)) {
            echo "<p class='message-info'>Veuillez sélectionner un ou plusieurs semestres</p>";
        } else {
            echo " 
            <p class='input-agregation-container'>
                <input type='text' class='input-agregation' placeholder='Nom Agrégation' name='nomAgregation' id='nomA_id' required>
            </p>
            ";
        }
        $id = 0;
        if (!empty($ressources) && !empty($semestres)) {
            echo '<p class="ressources-title">Ressources :</p><div class="ressources-list">';
            foreach ($semestres as $semestre) {
                foreach ($ressources as $ressource) {
                    if ($ressource->getNomRessource()[1] == $semestre) {
                        echo "<div class='ressource-item'>
                            <span class='ressource-name'>" . htmlspecialchars($ressource->getNomRessource()) . "</span>
                            <input type='hidden' name='idNom$id' value='" . urldecode($ressource->getNomRessource()) . "'>
                            <input type='number' class='input-coeff' name='coeff$id' value='0' id='coefRessource$id'>
                        </div>";
                        $id += 1;
                    }
                }
            }
            echo '</div>';
        }
        if (!empty($agregations)) {
            echo '<p class="agregation-title">Agregations :</p><div class="agregation-list">';
            foreach ($agregations as $agregation) {
                echo "<div class='agregation-item'>
                    <span class='agregation-name'>" . htmlspecialchars($agregation->getNomAgregation()) . "</span>
                    <input type='hidden' name='idNom$id' value='" . $agregation->getIdAgregation() . "'>
                    <input type='number' class='input-coeff' name='coeff$id' value='0' id='coefAgregation$id'>
                </div>";
                $id += 1;
            }
            echo '</div>';
        }
        echo "<input type='hidden' name='count' value='$id'>";
        ?>
        <input type='hidden' name='action' value='construireDepuisFormulaire'>
        <input type="hidden" name="controleur" value="agregation">
        <input type="submit" class="btn-submit" name="envoyer" value="Envoyer">
    </fieldset>
</form>

