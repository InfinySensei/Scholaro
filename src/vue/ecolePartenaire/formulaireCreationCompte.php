<form method="<?php use App\SAE\Configuration\ConfigurationSite;

echo ConfigurationSite::getDebug() ? 'get' : 'post'; ?>" action="controleurFrontal.php">
    <!-- Remplacer method="get" par method="post" pour changer le format d'envoi des données -->
    <fieldset>
        <legend>Formulaire Ecole :</legend>
        <p>
            <label for="siret_id">SIRET :</label>
            <input type="text" placeholder="12345678901234" name="siret" id="siret_id" required />
        </p>
        <p>
            <label for="nomEcole_id">Nom de l'École :</label>
            <input type="text" placeholder="Nom de l'École" name="nomEcole" id="nomEcole_id" required />
        </p>
        <p>
            <label for="villeEcole_id">Ville :</label>
            <input type="text" placeholder="Ville" name="villeEcole" id="villeEcole_id" required />
        </p>
        <p>
            <label for="tel_id">Téléphone :</label>
            <input type="tel" placeholder="0102030405" name="tel" id="tel_id" required />
        </p>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="email_id">Email : </label>
            <input class="InputAddOn-field" type="email" value="" placeholder="toto@yopmail.com" name="email" id="email_id" required>
        </p>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp_id">Mot de passe : </label>
            <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp" id="mdp_id" required>
        </p>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp2_id">Vérification du mot de passe : </label>
            <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp2" id="mdp2_id" required>
        </p>
        <p>
            <input type='hidden' name='action' value='creerDepuisFormulaire'>
            <input type="hidden" name="controleur" value="EcolePartenaire">
            <input type="submit" value="Envoyer" />
        </p>
    </fieldset>
</form>
