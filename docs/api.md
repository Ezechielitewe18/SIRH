# API JSON GLOBIT

Base : `http://<hote>/SIRH/api.php`

## Authentification

### Login
```
POST /api.php?action=login
Content-Type: application/json
{ "email": "...", "password": "..." }
```
Réponse :
```json
{ "success": true, "token": "<64 hex>", "expires_at": "2026-10-02 ...",
  "user": { "id_utilisateur": 1, "nom_complet": "...", "email": "...", "role": "admin|rh|employe",
            "id_employe": null, "matricule": null } }
```
Le token expire après **30 jours**.

### Utilisation du token
Toutes les autres requêtes doivent porter le header :
```
Authorization: Bearer <token>
```
ou le paramètre `token=<token>`. Erreur 401 si invalide/expiré.

### Logout (révoque le token)
```
POST /api.php?action=logout
```

## Endpoints

| Action | Méthode | Rôle | Description |
|--------|---------|------|-------------|
| `login` | POST | public | Authentification -> token |
| `logout` | POST | connecté | Révoque tous les tokens de l'utilisateur |
| `profil` | GET | connecté | Profil + infos employé (si lié) |
| `presence_aujourdhui` | GET | connecté | Présence du jour de l'employé |
| `presence_declarer` | POST | connecté | Déclarer son arrivée (validation RH) |
| `presence_depart` | POST | connecté | Pointer sa sortie |
| `presences` | GET | connecté | Historique (filtre ?mois=&annee=) |
| `presences_validation` | GET | admin, rh | File d'attente des présences non validées |
| `presence_valider` | POST | admin, rh | Valider une présence `{ "id_presence": N }` |
| `conges` | GET | connecté | Liste des congés de l'employé |
| `conge_demander` | POST | connecté | `{ type_conge, date_debut, date_fin, motif }` |
| `conge_approuver` | POST | admin, rh | `{ "id_conge": N }` |
| `conge_refuser` | POST | admin, rh | `{ "id_conge": N, "motif_refus": "..." }` |
| `notifications` | GET | connecté | `{ liste, non_lues }` |
| `notifications_lues` | POST | connecté | Marquer toutes les notifications comme lues |
| `bulletins` | GET | connecté | Bulletins de paie de l'employé |

## Types de congé valides
`annuel`, `maladie`, `maternite`, `paternite`, `exceptionnel`, `autre`

## Codes d'erreur
- `400` : Données invalides / manquantes
- `401` : Non authentifié (token manquant, invalide ou expiré)
- `403` : Rôle insuffisant
- `404` : Ressource ou action inconnue
- `405` : Méthode non autorisée
- `409` : Conflit (ex : déjà pointé aujourd'hui, déjà déclaré)

## Format de réponse standard
Succès : `{ "success": true, "data": ..., "message": "..." }`
Erreur : `{ "success": false, "message": "..." }`

## Table des tokens
La table `api_tokens` stocke les jetons (`token` unique, `expires_at`, `id_utilisateur`).
Les tokens expirés sont invalidés automatiquement à la lecture (et supprimés au login/logout).
