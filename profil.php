<?php
$nom = null;
if (!empty($_COOKIE['utilisateur'])) {
    $nom = $_COOKIE['utilisateur'];
}
if (!empty($_POST['nom'])) {
    setcookie('utilisateur', $_POST['nom']);
    $nom = $_POST['nom'];
}

require 'inc/header.php' ?>



<?php if ($nom) : ?>
    <h1>Bonjour <?= htmlentities($nom) ?></h1>
<?php else: ?>    
<form action ="" method="post">
    <div class="form-group">
        <input class="form-control" name="nom" placeholder="Entrez votre nom">
    </div>
    <button class="btn btn-primary">Se connecter</button>
</form>
<?php endif; ?>
<?php require 'inc/footer.php'; ?>