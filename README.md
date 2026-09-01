# GLOBIT - Système d'Information des Ressources Humaines

Système de gestion du personnel développé en PHP (MVC) avec MySQL, Bootstrap et AdminLTE.

**Développé par Ezechiel Itewe Nzukumayi** — © GLOBIT, tous droits réservés.

## Prérequis

- **XAMPP** (Apache + PHP 7.4+ + MySQL)
- Navigateur web moderne

## Installation

1. **Copier le projet** dans le dossier htdocs de XAMPP :
   ```
   C:\xampp\htdocs\SIRH
   ```

2. **Créer la base de données** :
   - Démarrez Apache et MySQL dans le panneau XAMPP.
   - Ouvrez phpMyAdmin : `http://localhost/phpmyadmin`
   - Importez le fichier `sql/sirh.sql` (onglet Importer).
   - La base de données `sirh` avec les tables et données initiales sera créée.

3. **Configurer la connexion** :
   - Modifiez si nécessaire `config/database.php` (utilisateur/mot de passe MySQL).

4. **Lancer l'application** :
   - Ouvrez votre navigateur à l'adresse : `http://localhost/SIRH`

## Comptes par défaut

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | `admin@sirh.local` | `password` |

> **Important :** Changez ce mot de passe dès la première connexion.
> Vous pouvez aussi créer un compte administrateur via la page d'inscription.

## Architecture

```
SIRH/
├── config/          # Configuration
│   ├── config.php   # Paramètres généraux
│   └── database.php # Connexion BDD
├── core/            # Noyau MVC
│   ├── Router.php   # Routeur
│   ├── Database.php # Connexion PDO (Singleton)
│   └── Model.php    # Classe de base
├── models/          # Modèles (accès données)
│   ├── UserModel.php
│   ├── EmployeeModel.php
│   ├── ServiceModel.php
│   ├── PresenceModel.php
│   └── CongeModel.php
├── controllers/     # Contrôleurs (logique)
├── views/           # Vues (interfaces)
├── public/          # Assets (CSS, JS)
├── sql/             # Script SQL
└── index.php        # Point d'entrée
```

## Modules

- **Authentification** : connexion, inscription, rôles (admin, rh, employé)
- **Employés** : ajout, modification, recherche, consultation (CRUD)
- **Services** : gestion des services/départements
- **Présences** : pointage arrivée/départ, retards automatiques
- **Congés** : demandes avec workflow d'approbation/refus
- **Tableau de bord** : statistiques et graphiques (Chart.js)

## Rôles et permissions

| Fonctionnalité | Admin | RH | Employé |
|----------------|:-----:|:--:|:-------:|
| Gérer les employés | ✓ | ✓ | |
| Gérer les services | ✓ | | |
| Valider les congés | ✓ | ✓ | |
| Soumettre un congé | ✓ | ✓ | ✓ |
| Pointer une présence | ✓ | ✓ | ✓ |
| Consulter le tableau de bord | ✓ | ✓ | ✓ |

## Évolutions futures

- Gestion automatisée de la paie
- Biométrie / reconnaissance faciale
- Gestion des formations
- Application mobile
- Notifications par email
