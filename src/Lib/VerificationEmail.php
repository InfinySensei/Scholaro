<?php

namespace App\Sae\Lib;

use App\Sae\Configuration\ConfigurationSite;
use App\Sae\Modele\DataObject\Ecole;
use App\Sae\Modele\Repository\EcoleRepository;

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class VerificationEmail
{
    public static function envoiEmailValidation(Ecole $ecole): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'scolaroiut@gmail.com'; // Votre adresse email
            $mail->Password   = 'ilmg ytrc ieab vcwz'; // Mot de passe d'application généré
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@example.com', "Validation de l'adresse email");
            $mail->addAddress($ecole->getMailEcole());

            $mail->isHTML(true);
            $mail->Subject = 'Validation de l\'adresse email';
            $siret = rawurlencode($ecole->getSiret());
            $nonceURL = rawurlencode($ecole->getNonce());
            $URLAbsolue = ConfigurationSite::getURLAbsolue();
            $lienValidationEmail = "https://webinfo.iutmontp.univ-montp2.fr$URLAbsolue?action=validerEmail&controleur=EcolePartenaire&siret=$siret&nonce=$nonceURL";
            $mail->Body    = 'Veuillez cliquer sur le lien pour valider votre mail. <a href='.$lienValidationEmail.'>Validation</a>';

            $mail->send();
        } catch (Exception $e) {
        }
    }

    public static function traiterEmailValidation($siret, $nonce): bool
    {
        $ecole = (new EcoleRepository())->recupererParClePrimaire($siret);
        if ($ecole && $ecole->getNonce() === $nonce) {
            $ecole->setMailValider(true);
            $ecole->setNonce(MotDePasse::genererChaineAleatoire(32));
            (new EcoleRepository())->mettreAJour($ecole);
            return true;
        } else {
            return false;
        }
    }

    public static function validerParAdmin(Ecole $ecole)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'scolaroiut@gmail.com'; // Votre adresse email
            $mail->Password   = 'ilmg ytrc ieab vcwz'; // Mot de passe d'application généré
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@example.com', "Demande de validation");
            $mail->addAddress("scolaroiut@gmail.com");

            $mail->isHTML(true);
            $siret = $ecole->getSiret();
            $nom = htmlspecialchars($ecole->getNomEcole());
            $mail->Subject = 'Demande de Validation de ' . $nom;
            $nonceURL = rawurlencode($ecole->getNonce());
            $URLAbsolue = ConfigurationSite::getURLAbsolue();
            $lienValidationEmail = "http://localhost$URLAbsolue?action=validerEcole&controleur=EcolePartenaire&siret=$siret&nonce=$nonceURL";
            $mail->Body    = 'L\'école '.$nom.' demande une vérification de compte <a href='.$lienValidationEmail.'>Validation</a>';

            $mail->send();
        } catch (Exception $e) {

        }
    }

    public static function notificationValidation(Ecole $ecole)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'scolaroiut@gmail.com'; // Votre adresse email
            $mail->Password   = 'ilmg ytrc ieab vcwz'; // Mot de passe d'application généré
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@example.com', "Validation de l'adresse email");
            $mail->addAddress($ecole->getMailEcole());

            $mail->isHTML(true);
            $mail->Subject = 'Validation du compte';
            $mail->Body    = 'Votre demande de création de compte est achever et validé';

            $mail->send();
        } catch (Exception $e) {
        }
    }
}