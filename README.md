# Mediatekformation
## Présentation
Ce site, développé avec Symfony, permet d'accéder aux vidéos d'auto-formation sur les outils numériques proposées par une chaîne de médiathèques et qui sont également accessibles sur YouTube.<br> 
De plus, il est possible de gérer les formations après s'être authentifié sur le site internet. L'application d'origine ne contenait que la partie front-office. Le dépôt d'origine se situe à cette adresse : https://github.com/CNED-SLAM/mediatekformation. Le README de ce premier dépôt contient la présentation de l'application d'origine avec uniquement la partie front-office. Dans le README du présent dépôt seront présentées uniquement les fonctionnalités ajoutées concernant le back-office ainsi que le mode opératoire pour installer et utiliser l'application en local. Il contient aussi les instructions nécessaires pour accéder à l'application en ligne. Les fonctionnalités ajoutées dans ce dépôt concernent la partie back-office et sont les suivantes :<br>
<br>
![Casutilisationbackoffice](https://github.com/user-attachments/assets/143d6971-633e-4c43-bf0a-32c8ca08a4b3)
## Les différentes pages
Voici les 8 pages correspondant aux différents cas d’utilisation.
### Page 1 : la page d'authentification
Cette page permet à un administrateur de se connecter.<br>
La partie du haut contient une bannière (logo, nom et phrase présentant le but du site).<br>
Le centre contient un formulaire avec deux champs : login et mot de passe, ainsi qu'un bouton "Se connecter".<br>
Ces champs doivent être remplis, sinon un message d'erreur s'affiche afin d'indiquer à l'utilisateur que le champ doit être renseigné. Si les informations saisies ne correspondent pas à un administrateur existant, un message d'erreur s'affiche également. Si les identifiants sont validés, l'administrateur accède au back-office de l'application et arrive sur la page de gestion des formations.<br> <br>
<img width="763" height="636" alt="Capture1" src="https://github.com/user-attachments/assets/42231feb-ae81-4cab-a898-5de72fe12afa" />
<br>


### Page 2 : gestion des formations
Cette page présente les formations proposées en ligne (accessibles sur YouTube) et permet à l'administrateur de les gérer.<br>
La partie haute contient la même bannière que sur la page d'authentification ainsi que le menu permettant d'accéder aux trois pages principales (Accueil, Formations, Playlists).<br>
En dessous se trouvent le titre de la page et, tout à droite, un bouton permettant d'ajouter une nouvelle formation.<br>
La partie centrale contient un tableau composé de six colonnes :<br>
•	La 1ère colonne ("Formations") contient le titre de chaque formation.<br>
•	La 2ème colonne ("Playlist") contient le nom de la playlist associée à chaque formation.<br>
•	La 3ème colonne ("Catégories") contient la ou les catégories concernées par chaque formation.<br>
•	La 4ème colonne ("Date") contient la date de parution de chaque formation.<br>
•	La 5ème contient la miniature visible sur YouTube pour chaque formation.<br>
• La 6ème colonne contient deux boutons d'action : "Editer" et "Supprimer".<br>
<br>
Au niveau de l'en-tête des colonnes "Formations", "Playlist" et "Date", deux boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">").<br>
Pour ces mêmes colonnes, il est possible de filtrer les lignes en saisissant un texte : seules les lignes contenant ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "Filtrer" permet d'afficher à nouveau la liste complète.<br> 
Pour la catégorie, la sélection d'une catégorie dans le menu déroulant permet d'afficher uniquement les formations associées à celle-ci. La selection de la ligne vide du menu permet d'afficher à nouveau toutes les formations.<br>
Par défaut, la liste est triée par la date de publication par ordre décroissant (la formation la plus récente en premier).<br>
Le clic sur une miniature permet d'accéder à la page de détail de la formation.<br>
Lors du clic sur le bouton "Supprimer", une fenêtre de confirmation s'affiche afin de demander à l'utilisateur s'il est certain de vouloir supprimer la formation. S'il répond "non", l'action est annulée, et s'il répond "oui", la formation est supprimée.<br>
La partie basse de la page contient un lien vers la page des CGU, présent sur l'ensemble des pages du back-office.
<br><br>
<img width="1552" height="903" alt="Capture2" src="https://github.com/user-attachments/assets/6cf4d841-8a35-40f0-a793-dec191d74052" />
<br>

### Page 3 : ajout d'une nouvelle formation
Cette page est accessible en cliquant sur le bouton "Ajouter une nouvelle formation" depuis la page de gestion des formations.<br>
La partie haute est identique aux autres pages du back-office (bannière et menu).<br>
La partie centrale contient un formulaire avec plusieurs champs :<br>
•	Deux sont facultatifs : la description et le choix d'une ou plusieurs catégories.<br>
•	La date de publication est obligatoire et doit respecter une règle : ne pas être postérieure à la date du jour.<br>
• Le titre de la formation doit être saisi. <br>
• Une playlist doit être choisie parmi une liste déroulante. <br>
• L'ID YouTube correspond à l'identifiant de la vidéo YouTube de la formation. <br>
• Un bouton "Enregistrer" permet de valider le formulaire. Si un des champs obligatoires ou si la règle concernant la date ne sont pas respectés, un message d'erreur s'affiche et l'utilisateur reste sur la page du formulaire. Si tout est validé, la formation est enregistrée et l'utilisateur est redirigé vers la page de gestion des formations.<br>
La partie basse de la page est identique aux autres pages du back-office (lien vers les CGU).<br><br>
<img width="1572" height="930" alt="Capture3" src="https://github.com/user-attachments/assets/802f1b67-6a1e-40eb-9ae9-a6ee102ff261" /> 
<br>


### Page 4 : modification d'une formation existante
Cette page permet de modifier une formation déjà existante.<br>
La partie haute est identique aux autres pages du back-office (bannière et menu).<br>
La partie centrale est identique et fonctionne de la même manière que le formulaire d'ajout d'une formation. La seule différence est que les champs sont préremplis avec les informations de la formation à modifier.<br>
La partie basse de la page est identique aux autres pages du back-office (lien vers les CGU).<br><br>
<img width="1592" height="927" alt="Capture4" src="https://github.com/user-attachments/assets/107caab8-31c3-4c0e-bf45-b809689a177a" />
<br>

### Page 5 : gestion des playlists
Cette page présente l'ensemble des playlists existantes et permet à l'administrateur de les gérer. Elle est accessible par le menu en cliquant sur "Playlists".<br>
La partie haute est identique aux autres pages du back-office (bannière et menu).<br>
La partie centrale contient un tableau composé de cinq colonnes :<br>
• La 1ère colonne ("Playlist") contient le nom de la playlist.<br>
• La 2ème colonne ("Nombre de formations incluses") contient le nombre de formations dans la playlist.<br>
• La 3ème colonne ("Catégories") contient la ou les catégories associées à la playlist.<br>
• La 4ème contient un bouton pour accéder à la page de détail de la playlist.<br>
• La 5ème contient les boutons d'actions "Editer" et "Supprimer".<br> <br>
Au niveau des colonnes "Playlist" et "Nombre de formations incluses" , deux boutons permettent de trier les lignes en ordre croissant ("<") ou décroissant (">"). Pour la colonne "Playlist", il est également possible de filtrer les lignes en saisissant un texte : seules les lignes qui contiennent ce texte sont affichées. Si la zone est vide, le fait de cliquer sur "Filtrer" permet de retrouver la liste complète.<br>
Au niveau de la catégorie, la sélection d'une catégorie dans la liste déroulante permet d'afficher uniquement les playlists associées à cette catégorie. Le fait de sélectionner la ligne vide de la liste permet d'afficher à nouveau l'ensemble des playlists.<br>
Par défaut, la liste est triée sur le nom de la playlist.<br>
Cliquer sur le bouton "Voir détail" d'une playlist permet d'accéder à la page présentant le détail de la playlist concernée.<br>
Si l'utilisateur clique sur le bouton "Supprimer", une fenêtre de confirmation s'ouvre afin de lui demander s'il est certain de vouloir supprimer la playlist. S'il répond "non", l'action est annulée. S'il répond "oui", la playlist est supprimée uniquement si les conditions sont validées, c'est-à-dire qu'elle ne contient aucune formation. Dans le cas contraire, un message d'erreur s'affiche afin d'informer l'utilisateur que la playlist contient encore des formations.<br><br>
<img width="1561" height="881" alt="Capture5" src="https://github.com/user-attachments/assets/91d38464-5ff7-42ad-8232-5168f80aaed1" />
<br>

### Page 6 : ajout d'une nouvelle playlist
La partie haute est identique aux autres pages du back-office (bannière et menu).<br>
La partie centrale contient un formulaire avec plusieurs champs :<br>
• Le nom de la playlist, qui est obligatoire.<br>
• La description de la playlist, facultative.<br>
• Un bouton "Enregistrer". Si le champ obligatoire n'est pas renseigné, un message d'erreur s'affiche et l'utilisateur reste sur la page du formulaire. Si tout est validé, la playlist est enregistrée et l'utilisateur est renvoyé vers la page de gestion des playlists.<br>
La partie basse de la page est identique aux autres pages du back-office (lien vers les CGU).<br><br>
<img width="1590" height="823" alt="Capture6" src="https://github.com/user-attachments/assets/b5cd8434-c0de-4eb7-80ee-fb4b3b59f3e1" />
<br>

### Page 7 : modification d'une playlist
La partie haute est identique aux autres pages du back-office (bannière et menu).<br>
La partie centrale est quasiment identique et fonctionne de la même manière que le formulaire d'ajout d'une playlist. La seule différence est que les champs sont préremplis avec les informations de la playlist à modifier. De plus, la liste des formations appartenant à cette playlist s'affiche sous le formulaire.<br>
La partie basse de la page est identique aux autres pages du back-office (lien vers les CGU).<br><br>
<img width="1570" height="934" alt="Capture7" src="https://github.com/user-attachments/assets/cea6d06b-7557-4929-82c2-574feb16e063" />
</br>

### Page 8 : gestion des catégories
Cette page liste l'ensemble des catégories existantes et permet à l'administrateur de les gérer. Elle est accessible par le menu en cliquant sur "Catégories".<br>
La partie haute est identique aux autres pages du back-office (bannière et menu).<br>
La partie centrale est constitué d'un tableau de deux colonnes :<br>
• La 1ère colonne ("Catégories") correspond à la liste des catégories existantes.<br>
• La 2ème colonne contient l'action "Supprimer" avec le bouton correspondant. Lors du clic sur ce bouton, une confirmation est demandée à l'utilisateur. S'il confirme et que les conditions sont validées, la catégorie est supprimée. Si la catégorie est encore liée à des formations, la suppression n'est pas possible et un message en informe l'utilisateur.<br>
Sous la liste des catégories existantes se trouve le formulaire d'ajout d'une nouvelle catégorie. Celui-ci contient un champ permettant de saisir le nom de la catégorie ainsi qu'un bouton "Enregistrer". Si l'utilisateur clique sur le bouton alors que la catégorie existe déjà, un message s'affiche pour lui indiquer qu'il n'est pas possible d'ajouter une catégorie dont le nom existe déjà. Dans le cas contraire, la catégorie sera ajoutée.<br>
La partie basse de la page est identique aux autres pages du back-office (lien vers les CGU).<br><br>
<img width="1540" height="882" alt="Capture8" src="https://github.com/user-attachments/assets/fd2a3785-c064-426f-9650-d9ea279a65d4" />
</br>


## Test de l'application en local
- Vérifier que Composer, Git et Wamserver (ou équivalent) sont installés sur l'ordinateur.
- Télécharger le code et le dézipper dans www de Wampserver (ou dossier équivalent), puis renommer le dossier en "mediatekformation".<br>
- Ouvrir une fenêtre de commandes en mode administrateur, se positionner dans le dossier du projet et saisir la commande "composer install" afin de reconstituer le dossier vendor.<br>
- Dans phpMyAdmin, se connecter à MySQL avec l'utilisateur root sans mot de passe, puis créer la base de données 'mediatekformation'.<br>
- Récupérer le fichier "mediatekformation.sql" en racine du projet et l'utiliser pour remplir la BDD (si vous voulez mettre un login/pwd d'accès, il faut créer un utilisateur, lui donner les droits sur la BDD et il faut le préciser dans le fichier ".env" en racine du projet).<br>
- Il est recommandé d'ouvrir l'application dans un IDE professionnel. L'adresse permettant de la lancer est la suivante : http://localhost/mediatekformation/public/index.php<br>


## Test de l'application en ligne
- Pour tester l'application en ligne, il faut se rendre à l'adresse suivante : https://mediatekformation.infinityfreeapp.com. <br>
- Pour accéder à la partie administrateur , il faut ajouter /admin à la fin de l'URL précedente et disposer des identifants nécessaires : https://mediatekformation.infinityfreeapp.com/admin. <br>
