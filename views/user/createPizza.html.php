<?php

use App\AppRepoManager;
use Core\Session\Session; ?>
<main class="container-form">
    <h1 class="title ">Je crée ma boite de dev</h1>
    <!-- on imorte le template de gestion de success et error -->
    <?php include(PATH_ROOT . '/views/_templates/_message.html.php'); ?>
    <form class="auth-form" action="/add-custom-pizza-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= Session::get(Session::USER)->id; ?>">
        <h3 class="sub-title">Je choisi un nom :</h3>
        <div class="box-auth-input">
            <input type="text" name="name" class="form-control">
        </div>
        <h3 class="sub-title">Je choisi mes poupées:</h3>
        <div class="box-auth-input list-ingredient">
            <!-- on va boucler sur notre tableau d'ingrédients -->
            <?php foreach (AppRepoManager::getrm()->getIngredientRepository()->getIngredientActiveByCategory() as $category => $ingredients) : ?>
                <div class="list-ingredient-box-update">
                    <h5 class="title-update"><?= ucfirst($category) ?></h5>
                    <?php foreach ($ingredients as $ingredient) : ?>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="ingredients[]" value="<?= $ingredient->id ?>" class="form-check-input" role="switch">
                            <label class="form-check-label footer-description m-1"><?= $ingredient->label ?></label>
                        </div>
                    <?php endforeach ?>

                </div>
            <?php endforeach ?>
        </div>
        <!--choix de la taille  -->
        <div class="box-auth-input list-size">
            <h3 class="sub-title">Je choisi ma taille : petite</h3>
            <!-- affichage des tailles -->
            <?php foreach (AppRepoManager::getrm()->getSizeRepository()->getAllSize() as $size) : ?>
                <div class="d-flex align-items-center">
                    <div class="list-size-input me-2">
                        <input type="radio" name="size_id" value="<?= $size->id ?>">
                    </div>
                    <label class="footer-description"><?= $size->label ?></label>
                </div>
            <?php endforeach ?>
            <!-- affichage du bouton -->
            <button class="call-action" type="submit">Crée ma boite de conserve, qui ne périme pas</button>
    </form>
</main>