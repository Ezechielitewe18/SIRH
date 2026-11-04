# SIRH — Guide de l'exposition

**Comment l'application est rendue accessible sur Internet, et comment l'équipe doit s'en servir le jour J.**

Document de briefing — à lire une fois à voix haute avec l'équipe avant l'événement.

---

## 1. L'idée en une phrase

L'application tourne sur **notre PC**. Un petit programme, **`cloudflared`**, ouvre une
liaison permanente vers **Cloudflare**, qui lui donne une **adresse publique en HTTPS**.
Les visiteurs passent par Cloudflare, qui amène leurs requêtes jusqu'à notre PC.

> **Notre PC = le serveur.** Cloudflare = le standard téléphonique qui reçoit les appels
> et nous les transfère.

---

## 2. Le trajet d'une visite (schéma à montrer)

```
   VISITEUR (4G, Wi-Fi du salon, n'importe où)
        │  https://....trycloudflare.com/SIRH
        ▼
   CLOUDFLARE            ← possède l'adresse publique et le cadenas HTTPS
        ▲
        │  liaison ouverte en permanence,
        │  lancée par NOTRE PC (vers l'extérieur)
        │
   ══ INTERNET ══
        │
   BOX / WI-FI           ← laisse sortir la liaison, rien à configurer
        │
   NOTRE PC
   cloudflared → Apache → PHP → MySQL
```

**À retenir :** c'est notre PC qui « appelle » Cloudflare, pas l'inverse.
C'est pour cela qu'il n'y a **aucun port à ouvrir** sur la box et que ça fonctionne
derrière n'importe quelle connexion (box, 4G, Wi-Fi du salon).

---

## 3. Les trois pièces du dispositif

| Pièce | Rôle | Si elle s'arrête |
|---|---|---|
| **Apache + MySQL** (XAMPP) | Exécute le site et stocke les données | Le site ne répond plus, même en local |
| **`cloudflared`** | Relaie le trafic entre Cloudflare et le PC | Le lien public meurt ; le réseau local continue |
| **Cloudflare** | Point d'entrée public + HTTPS | Plus d'accès depuis Internet |

**Rien n'est stocké chez Cloudflare.** Cloudflare transporte seulement. Les employés,
les présences, les QR : tout reste sur notre PC, dans MySQL.

---

## 4. Ce que les visiteurs ont besoin — et ce qu'ils n'ont pas besoin

| Question | Réponse |
|---|---|
| Doivent-ils être sur notre Wi-Fi ? | **Non.** N'importe quelle connexion Internet suffit. |
| Ont-ils besoin d'un compte ? | **Oui.** Sans identifiants, ils ne voient que la page de connexion. |
| Voient-ils les vraies données ? | **Oui.** C'est une vraie base — d'où les règles de la section 7. |
| Notre Wi-Fi sert-il à quelque chose ? | Seulement au **plan de secours** (section 6). |

Le Wi-Fi local ne transporte pas le trafic des visiteurs. Il ne transporte qu'**un seul
flux** : celui entre notre PC et Cloudflare. Tous les visiteurs le partagent — c'est
notre bande passante montante qui fixe la limite.

---

## 5. Le jour J — check-list

### La veille (J-1)

- [ ] PC branché sur secteur, mises en veille désactivées (écran et veille système).
- [ ] XAMPP démarre bien : **Apache** et **MySQL** en vert.
- [ ] `http://localhost/SIRH/login` s'ouvre.
- [ ] Test du QR + code de secours sur un téléphone.
- [ ] La base ne contient **aucune donnée de test** (présences de démo supprimées).
- [ ] Mots de passe à jour, notés dans un endroit sûr (jamais dans ce document).

### Le matin (H-30)

- [ ] Démarrer **Apache** et **MySQL**.
- [ ] Double-cliquer sur **`SIRH - Tunnel expo.cmd`** (sur le Bureau).
- [ ] Dans la fenêtre noire, repérer la ligne `https://........trycloudflare.com`.
- [ ] **Laisser cette fenêtre ouverte** — la réduire, jamais la fermer.
- [ ] Tester le lien complet : `https://........trycloudflare.com/SIRH` depuis un téléphone **en 4G** (donc hors de notre Wi-Fi).
- [ ] Écrire l'adresse finale sur un papier/tableau visible par l'équipe.

### Pendant

- [ ] Personne ne redémarre le PC, ne ferme XAMPP, ne ferme la fenêtre du tunnel.
- [ ] Un référent garde un œil sur la page de connexion toutes les heures.
- [ ] Les comptes de démonstration sont utilisés pour les visiteurs (voir section 7).

### À la fin

- [ ] Fermer la fenêtre du tunnel (le lien public cesse immédiatement).
- [ ] Supprimer les données créées pendant la démo (présences, comptes visiteurs).
- [ ] Éteindre normalement, puis retirer le dispositif de sécurité (section 8).

---

## 6. Si quelque chose ne va pas

| Symptôme | Cause probable | Que faire |
|---|---|---|
| Le lien public affiche une erreur | Fenêtre du tunnel fermée | Relancer `SIRH - Tunnel expo.cmd` |
| Le lien public ne répond plus du tout | PC en veille / Wi-Fi coupé / Internet tombé | Réveiller le PC, vérifier la connexion, relancer le tunnel |
| Page blanche ou erreur 500 | MySQL arrêté | Panneau XAMPP → démarrer MySQL |
| « Impossible de se connecter » en local | Apache arrêté | Panneau XAMPP → démarrer Apache |
| La caméra ne s'ouvre pas | Page ouverte en `http://` au lieu de `https://` | Utiliser le lien `https://` du tunnel |
| L'adresse a changé | Le tunnel a été relancé | Relire la nouvelle adresse dans la fenêtre, la réécrire |

**Règle d'or :** on ne « répare » jamais en touchant à la base de données pendant
l'exposition. On redémarre le minimum (tunnel, puis Apache/MySQL si besoin) et on
note ce qui s'est passé.

---

## 7. Règles de sécurité (à dire explicitement à l'équipe)

1. **On ne donne jamais le compte administrateur** à un visiteur.
2. On utilise un **compte de démonstration** dédié pour les essayages.
3. **On n'ouvre pas `phpMyAdmin`** devant le public — il est bloqué depuis Internet, mais reste accessible en local.
4. **On ne montre pas** l'écran des mots de passe, les fichiers de configuration, ni ce document s'il contient des identifiants.
5. **On ne laisse pas** un poste connecté sans surveillance sur une session admin.
6. Un visiteur qui insiste pour « voir la base » : réponse polie, c'est non.
7. À la fin de chaque démonstration : **déconnexion** du poste.

> Les mots de passe ne figurent **jamais** dans un document versionné. Ils sont
> communiqués séparément, de vive voix ou par un canal privé.

---

## 8. Après l'exposition — retour à la normale

Le dispositif d'exposition est **temporaire**. Pour le retirer :

1. Fermer la fenêtre du tunnel (ou arrêter `cloudflared`).
2. Supprimer `C:\xampp\htdocs\.htaccess` (blocage des chemins hors `/SIRH` via tunnel).
3. Restaurer `C:\xampp\apache\conf\httpd.conf.bak-expo` à la place de `httpd.conf`.
4. Redémarrer Apache.

L'application redevient alors strictement locale, exactement comme avant.

---

## 9. Questions que les visiteurs vont poser

**« C'est hébergé où ? »**
Sur notre serveur, ici. Cloudflare sert seulement de porte d'entrée sécurisée.

**« Pourquoi le QR change tout le temps ? »**
Pour qu'une photo ou un code envoyé par message ne puisse pas servir à pointer à la
place de quelqu'un. Le code n'est valable que quelques secondes.

**« Et si je n'ai pas de caméra ? »**
La réception saisit le matricule et le code à 6 chiffres affiché sous le QR.

**« Est-ce que je peux me connecter depuis chez moi ? »**
Oui, tant que le PC de l'exposition est allumé et que le lien est actif. Après
l'exposition, l'accès public est fermé.

**« Mes données sont-elles en sécurité ? »**
Le trafic est chiffré (HTTPS). Le QR ne contient jamais le secret de l'employé, et
l'application ne renvoie que le strict nécessaire en cas d'erreur.

---

## 10. Les 5 phrases à retenir

1. **Le PC est le serveur** ; Cloudflare n'est qu'une porte d'entrée.
2. **Les visiteurs n'ont pas besoin de notre Wi-Fi**, juste d'Internet.
3. **Trois choses doivent rester allumées** : Apache, MySQL, la fenêtre du tunnel.
4. **Le Wi-Fi local est le plan de secours**, pas le mode normal.
5. **Le dispositif est temporaire** : on le retire après l'exposition.

---

*Document interne GLOBIT SAS — exposition SIRH.*
