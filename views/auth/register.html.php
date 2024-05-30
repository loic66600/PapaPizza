<main class="container-form">
    <h1>je crée mon compte</h1>
    <!-- affichage des erreur si il y en as -->
    <?php if($form_result && $form_result->hasErrors()): ?>
        <div class="alert alert-danger" role="alert"> 
            <?php $form_result->getErrors()[0]->getMessage() ?>
        </div>
    <?php endif ?>
    <form class="auth-form" action="/register" method="post"> 
        <div class="box-auth-input">
            <label class="detail-description">Adresse Email</label>
            <input class="form-control" type="email" name="email" >
        </div>
        <div class="box-auth-input">
            <label class="detail-description">Mot de passe</label>
            <input class="form-control" type=" password" name="password" >
        </div>
        <div class="box-auth-input">
            <label class="detail-description">Confirmer mot de passe</label>
            <input class="form-control" type=" password" name="password" >
        </div>
        <div class="box-auth-input">
            <label class="detail-description">Votre nom</label>
            <input class="form-control" type="text" name="name" >
        </div>
        <div class="box-auth-input">
            <label class="detail-description">Votre prénom</label>
            <input class="form-control" type="text" name="firstname" >
        </div>
        <div class="box-auth-input">
            <label class="detail-description">Votre téléphone</label>
            <input class="form-control" type="text" name="phone" >
        </div>  
                  <button type="submit" class="call-action">Je m'inscris</button>

    </form>
    <p class="header-description">J ai déja un compte, <a class="auth-link" href="/connexion">Je me connecte</a></p>
</main>