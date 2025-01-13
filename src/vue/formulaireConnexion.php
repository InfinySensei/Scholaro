<form method="<?php if (\App\Sae\Configuration\ConfigurationSite::getDebug()) echo "get"; else echo "post" ?>" action="controleurFrontal.php">
    <fieldset>
        <legend>Connexion :</legend>
        <input type='hidden' name='action' value='connecter'>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="login_id">Login&#42;</label>
            <input class="InputAddOn-field" type="text" placeholder="Ex : pallejax" name="login" id="login_id" required>
        </p>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="mdp_id">Mot de passe&#42;</label>
            <input class="InputAddOn-field" type="password" value="" placeholder="" name="mdp" id="mdp_id" required>
        </p>
        <p>
            <input type="submit" value="Envoyer">
        </p>
        <h5 class="champs">
            * champs requis
        </h5>
        <h5>
            Vous êtes une école partenaire et vous n'avez pas encore de compte ?
        </h5>
        <p style="font-size: small;"><a href="?controleur=ecolePartenaire&action=afficherFormulaireCreationCompte"> Créer un compte</a></p>
    </fieldset>
</form>