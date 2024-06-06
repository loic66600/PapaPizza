
<?php
if($auth::isauth()) $auth::redirect('/');
?>
<main class="container-form">
    <h1>je crée mon compte</h1>
    <!-- affichage des erreur si il y en as -->
    <?php if($form_result && $form_result->hasErrors()): ?>
        <div class="alert alert-danger" role="alert"> 
            <?php  echo $form_result->getErrors()[0]->getMessage() ?>
        </div>
    <?php endif ?>

    <form class="auth-form" action="/login" method="post"> 
        <div class="box-auth-input">
            <label class="detail-description">Adresse Email</label>
            <input class="form-control" type="email" name="email" >
        </div>
        <div class="box-auth-input">
            <label class="detail-description">Mot de passe</label>
            <input class="form-control" type=" password" name="password" >
        </div>

                  <button type="submit" class="call-action">Je me connecte</button>

    </form>
    <p class="header-description">Je n'ai pas de compte , <a class="auth-link" href="/inscription">Je m'inscrit</a></p>

</main>