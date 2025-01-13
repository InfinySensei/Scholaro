<?php
namespace App\Sae\Service;

use App\Sae\Exception\ArgNullException;
use App\Sae\Exception\DroitException;
use App\Sae\Lib\ConnexionUtilisateur;

class ServiceGenerique {
    /**
     * @param $login
     * @return void check si l'utilisateur à l'autorisation de se connecter
     * @throws DroitException
     */
    public function verifDroit($login)
    {
        if (!ConnexionUtilisateur::estAdministrateur() && !ConnexionUtilisateur::estEcolePartenaire($login) && !ConnexionUtilisateur::estProfesseur()) {
            throw new DroitException("Vous n'avez pas les droits");
        }
    }

    /**
     * @param $id
     * @return void check si l'id est null
     * @throws ArgNullException
     */
    public function idVide($id)
    {
        if (!$id) {
            throw new ArgNullException("L'id n'est pas passé en paramètre.");
        }
    }
}