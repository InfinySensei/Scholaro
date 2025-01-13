<div class="marge">
    <table>
        <thead>
        <tr>
        <th>Id Agregation</th>
        <th>Nom Agregation</th>
        </tr>
        </thead>


        <tbody>
        <?php
        /**
         * @var \App\Sae\Modele\DataObject\Agregation[] $agregations
         */
        foreach ($agregations as $agregation) {
            echo '
   <tr>
    <td> <a href="?controleur=agregation&action=afficherDetail&id=' . $agregation->getIdAgregation() . '">' . $agregation->getIdAgregation() . '</a></td>
    <td><a href="?controleur=agregation&action=afficherDetail&id=' . $agregation->getIdAgregation() . '">' . urldecode($agregation->getNomAgregation()) . '</a></td> 
    </tr>';
        }
        ?>
        </tbody>
    </table>

</div>
