# Les cookies

Les cookies permettent de sauvegarder une petite quantitée d'information sur la navigateur de l'utilisateur


Les cookies sont définis comme ça :

- Un nom de cookie
- Une valeur de cookie
- Un nom de domaine qui lui est associé
- Un chemin, qui permet de restreindre les cookies à un certain dossier
- Date d'expiration du cookie
- Taille du cookie en octet ( limité a 4-5ko)

Si on veut définir un cookie :
On envoie une entête `setcookie`

Si on veut récupérer un cookie :
On lit l'entête cookie, le Request Headers

        cookie: CONSENT=WP.27a4fe; NID=184=hh_sVfOS_4wMb_SSZDDW98tb3bj9hvGlvuPMGq4HpaHuERlIE5CRUStKMyAi0feFcMyHgSmCC1OmxN143V2aa4NlroL1H2T-h7QiEHo4c93R_2nekfyWrkgM_ug7fUelYBUQDEjbHmOWreXu4ErUfNWdlojtj8IN17uZ_Qtg3aM; 1P_JAR=2019-5-26-15



**F12 -> Network : (Entête cookie)**
_Par défaut on reçois le status 301_
_Nous voulons vérifier, alors on regarde le status 200 pour voir les cookies :_

    set-cookie: 1P_JAR=2019-05-26-15; expires=Tue, 25-Jun-2019 15:47:05 GMT; path=/; domain=.google.fr


## Etapes

A l'aide la video [https://www.youtube.com/watch?v=LARX660fup0]
Créer un fichier cookie.php

## Préambule setcookie()

On veut définir un cookie pour l'utilisateur
On pourrait  manuellement envoyer l'entête au navigateur avec la methode `header()`

On choisis une entête, la clé et sa valeure attribué

```php
header('Location: http://www.example.com')
```
C'est sympa pour des entêtes assez basiques mais pour les cookies ça va pas aller car il faut que nous mettions beaucoup d'infos ( la date la taille etc etc )

Donc on a une methode révolutionnaire pour palier à ça !! Cette methode s'appele `setcookie` on va générer un cookie, cette methode nous permet de renovyer un entête 

    setcookie ( string $name [, string $value = "" [, int $expires = 0 [, string $path = "" [, string $domain = "" [, bool $secure = FALSE [, bool $ht`tponly = FALSE ]]]]]] ) : bool




- `string $name` : En premier paramètre la clé ( NID, CONSENT )
- `string $value` : La valeur de ce name ( qui? ), en string obligatoirement
- `int $expires` = 0 : La date d'expiration du cookie
- `string $path` = "" : Le chemin
- `string $domain` = "" : Le nom de domaîne
- `boo $secure` = FALSE : Si on veut ou non que le cookie soit sécurisé ( peut il être transmis en HTTP )
- `bool $httponly` = FALSE : cookie accessible en javascript ou non

# Etape 1

On veut essayer de définir un cookie qui contiendrait le nom de l'utilisateur

On peut définir exactement quand le cookie se détruira grace à `int $expires`

- `time()`  Expirera tout de suite
- `time() +60`   Expirera dans 1 minute
- `time() +60 * 60`  Expirera dans 1 heure
- `time() +60 *60 * 24`  Expirera dans 1 journée


Si on ne spécifie pas ce paramètre, ou si il est égal à 0 alors le cookie expirera à la fin de la session ( lorsque de le navigateur sera férmé )

```php
setcookie('utilisateur', 'John', time() + 60 * 60 * 24);
```

On va ensuite dans la reponse du header de l'inspecteur
On retrouve ça

    Set-Cookie: utilisateur=John; expires=Mon, 27-May-2019 16:24:17 GMT; Max-Age=86400

_L'age est fixé automatiquement par php_

# Etape 2

Application -> Cookies -> Localhost

On a bien le cookie qui est présent, nom `utilisateur`, valeur `John`

On peut maintenant commenter la ligne `setcookie('utilisateur', 'John', time() + 60 * 60 * 24);` de notre php

# Etape 3

On veut maintenant récupérer les cookies.
Si l'utilisateur actualise la page on aimerait bien lui afficher son nom en récupérant ses cookies ( l'utilisateur ).

On utilise donc la supervariable globale

```php
$_COOKIE

var_dump($_COOKIE);
```

Le var dump nous renvoit :

```php
array(1) { ["utilisateur"]=> string(4) "John" }
```

Nos cookies sont donc stockés dans $_COOKIE c'est tout !






#### Attention !

```php
var_dump($_COOKIE);
setcookie('utilisateur', 'John', time() + 60 * 60 * 24);
```

Le fait de faire un var_dump($_COOKIE) avant de faire un setcookie() nous renvoie une erreur `Cannot modify header information`, il faut donc inverser et declarer le set cookie avant, meme si il y a un espace entre le bord et `<?php`, ça fera une erreur aussi, alors attention a ne avoir **aucun** contenu avant la manipulation d'entête http.





## Simulation

On va maintenant s'imaginer qu'on est sur notre site, on veut créer une page profil.php qui sera accessible uniquement si l'utilisateur a entrer son nom, donc un formulaire avec un nom d'utilisateur, à la soumission il sera redirigé vers cette page, selon ses cookies on lui affichera son nom.

Faites un formulaire n'importe lequel, moi j'ai eu la flemme j'ai repris un template de bootstrap.

On veut donc sauvegarder ce que tappe l'utilisateur ( son nom ) dans un cookie

# Etape 1

Avant meme le require du header on va demander à php si le formulaire à été soumis avec la methode `POST`

On vérifie si on a `_POST['nom']` qui n'est **PAS vide** ( donc que l'utilisateur a bien noter entré si nom), si on rentre dans ce cas là alors :
On va pouvoir sauvegarder son nom dans un `cookie` (clé, valeur comme tout à l'heure) ...

```php
if (!empty($_POST['nom'])) {
    setcookie('utilisateur', $_POST['nom']);
}

require 'inc/header.php';
```

Quand on regarde maintenant dans `Application -> cookies -> Localhost`, on voit que si on met un autre nom d'utilisateur, la valeur change en fonction du nouveau nom écrit !

# Etape 2

- On créer une variable qu'on initialise à null de base, pour dire que l'utilisateur à aucun nom  (avant qu'il n'en mette un)
- On vérifie ensuite si le cookie est défini, autrement dit, si l'utilisateur existe avec `!empty` de `COOKIE`
- Si il est défini on le sauvegarde donc dans la variable `$nom`

```php
$nom = null;
if (!empty($_COOKIE['utilisateur'])) {
    $nom = $_COOKIE['utilisateur'];
}
```

## Etape 3

On doit mettre une nouvelle condition :

- Si le nom est défini j'affiche Bonjour suivi du nom
- Sinon on affiche le formulaire d'inscription

Pour l'instant le code ressemble à ça_ :


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

## Etape 3.5

Quand j'utilise `setcookie()` je peux définir tout de suite la valeur du nom de l'utilisateur en faisant
    $nom = $_POST['nom'];

Comme on le sait, tout ce que nous envoie l'utilisateur n'est pas vérifié, on ne fais pas confiance !

Il faut donc penser à faire un `htmlentities($nom)` dans la condition if du "Bonjour $nom"

_La fonction htmlentities va convertir les caractères spéciaux d’une chaîne de caractères en un équivalent en html_.

[http://www.analyste-programmeur.com/php/les-chaines-de-caracteres/fonction-htmlentities]

C'est très bien expliqué là dessus, mais en gros c'est pour avoir un bon encodage pour les caractères html.


