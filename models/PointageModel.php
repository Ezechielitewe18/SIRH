<?php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/QrcodeModel.php';

/**
 * Moteur de pointage unique.
 *
 * Toutes les sources (QR, RFID, biometrie, manuel) passent par la methode
 * traiter(). Le moteur :
 *   1. valide la source et l'horodatage ;
 *   2. verifie l'appareil emetteur ;
 *   3. ecrit l'evenement brut (la preuve) avec anti-doublon materiel ;
 *   4. retrouve l'employe (jeton QR, carte, matricule) ;
 *   5. decide arrivee ou depart ;
 *   6. met a jour la ligne du jour dans `presences` et journalise.
 *
 * Le moteur ne recoit ni empreinte, ni image, ni gabarit biometrique.
 */
class PointageModel extends Model {
    protected $table = 'pointages_evenements';
    protected $primaryKey = 'id_evenement';

    const SOURCES = ['qr', 'rfid', 'biometrie', 'manuel'];
    const TOLERANCE_FUTUR_MINUTES = 5;
    const TAILLE_MAX_DONNEES = 8192;

    private $qrcode;

    /** Horloge injectable (tests). null = heure systeme. */
    private $horloge = null;

    public function __construct() {
        parent::__construct();
        $this->qrcode = new QrcodeModel();
    }

    /** Fixe une heure de reference pour les tests (null = heure systeme). */
    public function fixerHorloge($timestamp = null) {
        $this->horloge = $timestamp === null ? null : (int) $timestamp;
        return $this;
    }

    private function maintenant() {
        return $this->horloge ?? time();
    }

    /**
     * Traite un evenement de pointage.
     *
     * $evt attendu :
     *   source        qr | rfid | biometrie | manuel
     *   identifiant   jeton QR, UID de carte, matricule (ou id_employe si manuel)
     *   horodatage    'Y-m-d H:i:s' (defaut : maintenant)
     *   id_dispositif int|null
     *   uid_externe   numero unique fourni par l'appareil (defaut : derive)
     *   donnees_brutes array|string (nettoyee avant stockage)
     */
    public function traiter(array $evt) {
        $source = strtolower(trim((string) ($evt['source'] ?? '')));
        if (!in_array($source, self::SOURCES, true)) {
            return $this->echec('source_invalide', 'Source de pointage inconnue.');
        }

        $identifiant = trim((string) ($evt['identifiant'] ?? ''));
        $idEmployeFourni = isset($evt['id_employe']) && $evt['id_employe'] !== '' ? (int) $evt['id_employe'] : null;

        if ($identifiant === '' && $source !== 'manuel') {
            return $this->echec('identifiant_manquant', 'Identifiant de pointage manquant.');
        }
        if ($source === 'manuel' && !$idEmployeFourni) {
            return $this->echec('employe_requis', 'Un pointage manuel exige l\'employe.');
        }

        $horodatage = $this->normaliserHorodatage($evt['horodatage'] ?? null);
        if ($horodatage === null) {
            return $this->echec('horodatage_invalide', 'Horodatage invalide ou illisible.');
        }

        // --- 1. Appareil emetteur -------------------------------------------
        $idDispositif = isset($evt['id_dispositif']) && $evt['id_dispositif'] !== ''
            ? (int) $evt['id_dispositif'] : null;

        if ($idDispositif !== null) {
            $dispositif = $this->db->prepare("SELECT * FROM dispositifs WHERE id_dispositif = :id");
            $dispositif->execute(['id' => $idDispositif]);
            $dispositif = $dispositif->fetch();

            if (!$dispositif) {
                return $this->echec('dispositif_inconnu', 'Appareil non declare : pointage refuse.', 403);
            }
            if ($dispositif['statut'] !== 'actif') {
                return $this->echec('dispositif_inactif', 'Appareil ' . $dispositif['statut'] . ' : pointage refuse.', 403);
            }
            $this->db->prepare("UPDATE dispositifs SET dernier_contact = NOW() WHERE id_dispositif = :id")
                     ->execute(['id' => $idDispositif]);
        } elseif (in_array($source, ['rfid', 'biometrie'], true)) {
            return $this->echec('dispositif_requis', 'Un pointage ' . $source . ' doit venir d\'un appareil declare.', 403);
        }

        // --- 2. Ecriture de l'evenement brut (preuve) -----------------------
        $uidExterne = trim((string) ($evt['uid_externe'] ?? ''));
        if ($uidExterne === '') {
            $uidExterne = $this->uidParDefaut($source, $idDispositif, $identifiant, $horodatage);
        }
        $uidExterne = mb_substr($uidExterne, 0, 100);

        list($donnees, $biometrieRetiree) = $this->nettoyerDonnees($evt['donnees_brutes'] ?? null);

        try {
            $idEvenement = (int) $this->create([
                'id_employe' => $idEmployeFourni,
                'id_dispositif' => $idDispositif,
                'source' => $source,
                'type_evenement' => 'inconnu',
                'horodatage' => $horodatage,
                'horodatage_recu' => date('Y-m-d H:i:s', $this->maintenant()),
                'uid_externe' => $uidExterne,
                'identifiant_lu' => mb_substr($identifiant, 0, 191),
                'statut_traitement' => 'recu',
                'donnees_brutes' => $donnees,
            ]);
        } catch (PDOException $e) {
            if ($this->estDoublon($e)) {
                $existant = $this->trouverParDedup($idDispositif, $uidExterne);
                return [
                    'ok' => false,
                    'code' => 'doublon',
                    'statut' => 'doublon',
                    'message' => 'Evenement deja recu : ignore (anti-doublon materiel).',
                    'id_evenement' => $existant ? (int) $existant['id_evenement'] : null,
                ];
            }
            throw $e;
        }

        // --- 3. Identification de l'employe ---------------------------------
        $resolution = $this->resoudreEmploye($source, $identifiant, $idEmployeFourni);
        if (!$resolution['ok']) {
            $this->majEvenement($idEvenement, [
                'statut_traitement' => 'en_attente',
                'message' => $this->court($resolution['message']),
            ]);
            return [
                'ok' => false,
                'code' => $resolution['code'],
                'statut' => 'en_attente',
                'message' => $resolution['message'],
                'id_evenement' => $idEvenement,
            ];
        }

        $employe = $resolution['employe'];
        $idCarte = $resolution['id_carte'] ?? null;
        $this->majEvenement($idEvenement, [
            'id_employe' => (int) $employe['id_employe'],
            'id_carte' => $idCarte,
        ]);

        if (($employe['statut'] ?? '') !== 'actif') {
            $message = $employe['prenom'] . ' ' . $employe['nom'] . ' n\'est pas un employe actif : pointage refuse.';
            $this->majEvenement($idEvenement, ['statut_traitement' => 'rejete', 'message' => $this->court($message)]);
            return ['ok' => false, 'code' => 'employe_inactif', 'statut' => 'rejete', 'message' => $message, 'id_evenement' => $idEvenement];
        }

        // --- 4. Controles de date -------------------------------------------
        $dateEvt = substr($horodatage, 0, 10);
        $heureEvt = substr($horodatage, 11, 8);
        $aujourdhui = date('Y-m-d', $this->maintenant());

        if (strtotime($horodatage) > $this->maintenant() + (self::TOLERANCE_FUTUR_MINUTES * 60)) {
            $message = 'Horodatage dans le futur : evenement signale, non applique.';
            $this->majEvenement($idEvenement, ['statut_traitement' => 'rejete', 'message' => $this->court($message)]);
            return ['ok' => false, 'code' => 'horodatage_futur', 'statut' => 'rejete', 'message' => $message, 'id_evenement' => $idEvenement];
        }

        if ($dateEvt > $aujourdhui) {
            $message = 'Date posterieure a aujourd\'hui : evenement refuse.';
            $this->majEvenement($idEvenement, ['statut_traitement' => 'rejete', 'message' => $this->court($message)]);
            return ['ok' => false, 'code' => 'date_future', 'statut' => 'rejete', 'message' => $message, 'id_evenement' => $idEvenement];
        }

        $presence = $this->presenceDuJour((int) $employe['id_employe'], $dateEvt);

        if ($dateEvt < $aujourdhui) {
            $message = 'Evenement en retard (' . $dateEvt . ') : journee deja close, a valider par le RH.';
            $this->majEvenement($idEvenement, [
                'statut_traitement' => 'a_valider',
                'type_evenement' => $presence && empty($presence['heure_depart']) ? 'depart' : 'arrivee',
                'id_presence' => $presence['id_presence'] ?? null,
                'message' => $this->court($message),
            ]);
            return [
                'ok' => false,
                'code' => 'evenement_en_retard',
                'statut' => 'a_valider',
                'message' => $message,
                'id_evenement' => $idEvenement,
                'id_presence' => $presence['id_presence'] ?? null,
            ];
        }

        // --- 5. Decision arrivee / depart -----------------------------------
        if ($presence === null || ($presence['statut'] === 'absent' && $presence['validation'] === 'auto')) {
            $action = 'arrivee';
        } elseif (empty($presence['heure_depart'])) {
            $action = 'depart';
        } else {
            // Option B : la journee est complete. L'evenement reste enregistre
            // comme preuve, mais `presences` n'est pas modifie.
            $message = 'Journee deja complete (arrivee et depart pointes). Evenement conserve sans modification.';
            $this->majEvenement($idEvenement, [
                'statut_traitement' => 'traite',
                'type_evenement' => 'inconnu',
                'id_presence' => (int) $presence['id_presence'],
                'message' => $this->court($message),
            ]);
            return [
                'ok' => true,
                'code' => 'journee_complete',
                'statut' => 'traite',
                'action' => null,
                'message' => $message,
                'id_evenement' => $idEvenement,
                'id_presence' => (int) $presence['id_presence'],
            ];
        }

        // --- 6. Ecriture dans le cahier propre ------------------------------
        if ($action === 'arrivee') {
            $retard = $this->minutesRetard($heureEvt);
            $statutPresence = $retard > 0 ? 'retard' : 'present';

            if ($presence === null) {
                $this->db->prepare(
                    "INSERT INTO presences
                        (id_employe, source, date_presence, heure_arrivee, retard, statut, validation)
                     VALUES (:id_employe, :source, :date_presence, :heure, :retard, :statut, 'auto')"
                )->execute([
                    'id_employe' => (int) $employe['id_employe'],
                    'source' => $source,
                    'date_presence' => $dateEvt,
                    'heure' => $heureEvt,
                    'retard' => $retard,
                    'statut' => $statutPresence,
                ]);
                $idPresence = (int) $this->db->lastInsertId();
            } else {
                $this->db->prepare(
                    "UPDATE presences
                        SET source = :source, heure_arrivee = :heure, retard = :retard,
                            statut = :statut, validation = 'auto', updated_at = NOW()
                      WHERE id_presence = :id"
                )->execute([
                    'source' => $source,
                    'heure' => $heureEvt,
                    'retard' => $retard,
                    'statut' => $statutPresence,
                    'id' => (int) $presence['id_presence'],
                ]);
                $idPresence = (int) $presence['id_presence'];
            }

            $message = 'Arrivee enregistree a ' . substr($heureEvt, 0, 5)
                . ($retard > 0 ? ' (retard ' . $retard . ' min).' : ' — a l\'heure.');
        } else {
            if (!empty($presence['heure_arrivee'])
                && strtotime($heureEvt) <= strtotime($presence['heure_arrivee'])) {
                $message = 'Depart anterieur ou egal a l\'arrivee (horloge de l\'appareil a verifier) : a valider par le RH.';
                $this->majEvenement($idEvenement, [
                    'statut_traitement' => 'a_valider',
                    'type_evenement' => 'depart',
                    'id_presence' => (int) $presence['id_presence'],
                    'message' => $this->court($message),
                ]);
                return [
                    'ok' => false,
                    'code' => 'depart_incoherent',
                    'statut' => 'a_valider',
                    'message' => $message,
                    'id_evenement' => $idEvenement,
                    'id_presence' => (int) $presence['id_presence'],
                ];
            }

            $this->db->prepare("UPDATE presences SET heure_depart = :heure, updated_at = NOW() WHERE id_presence = :id")
                     ->execute(['heure' => $heureEvt, 'id' => (int) $presence['id_presence']]);
            $idPresence = (int) $presence['id_presence'];
            $message = 'Depart enregistre a ' . substr($heureEvt, 0, 5) . '.';
        }

        if ($biometrieRetiree) {
            $message .= ' Donnee biometrique retiree avant stockage.';
        }

        $this->majEvenement($idEvenement, [
            'statut_traitement' => 'traite',
            'type_evenement' => $action,
            'id_presence' => $idPresence,
            'message' => $this->court($message),
        ]);

        $this->journaliser($source, $action, $employe, $horodatage, $idPresence, $idEvenement);

        return [
            'ok' => true,
            'code' => 'traite',
            'statut' => 'traite',
            'action' => $action,
            'message' => $message,
            'id_evenement' => $idEvenement,
            'id_presence' => $idPresence,
            'employe' => [
                'id_employe' => (int) $employe['id_employe'],
                'matricule' => $employe['matricule'],
                'nom' => $employe['nom'],
                'prenom' => $employe['prenom'],
            ],
        ];
    }

    // ------------------------------------------------------------------
    // Identification
    // ------------------------------------------------------------------

    private function resoudreEmploye($source, $identifiant, $idEmployeFourni) {
        if ($source === 'manuel') {
            $employe = $this->employeParId($idEmployeFourni);
            return $employe
                ? ['ok' => true, 'employe' => $employe]
                : ['ok' => false, 'code' => 'employe_inconnu', 'message' => 'Employe introuvable.'];
        }

        if ($source === 'qr') {
            $verif = $this->qrcode->validerJeton($identifiant);
            if (!$verif['success']) {
                return ['ok' => false, 'code' => 'qr_invalide', 'message' => $verif['message']];
            }
            $employe = $this->employeParId($verif['employe']['id_employe']);
            return $employe
                ? ['ok' => true, 'employe' => $employe]
                : ['ok' => false, 'code' => 'employe_inconnu', 'message' => 'Employe introuvable.'];
        }

        if ($source === 'rfid') {
            $carte = $this->db->prepare(
                "SELECT c.id_carte, c.statut AS statut_carte, e.*
                   FROM cartes c
                   INNER JOIN employes e ON c.id_employe = e.id_employe
                  WHERE c.uid_carte = :uid
                  LIMIT 1"
            );
            $uid = strtoupper(preg_replace('/[\s:.\-]/', '', (string) $identifiant));
            $carte->execute(['uid' => $uid]);
            $row = $carte->fetch();

            if (!$row) {
                return ['ok' => false, 'code' => 'carte_inconnue', 'message' => 'Carte inconnue : elle n\'est attribuee a aucun employe.'];
            }
            if ($row['statut_carte'] !== 'active') {
                return [
                    'ok' => false,
                    'code' => 'carte_' . $row['statut_carte'],
                    'message' => 'Carte ' . $row['statut_carte'] . ' : pointage refuse. Signalez-la au RH.',
                ];
            }
            return ['ok' => true, 'employe' => $row, 'id_carte' => (int) $row['id_carte']];
        }

        if ($source === 'biometrie') {
            $stmt = $this->db->prepare("SELECT * FROM employes WHERE matricule = :m LIMIT 1");
            $stmt->execute(['m' => $identifiant]);
            $employe = $stmt->fetch();
            return $employe
                ? ['ok' => true, 'employe' => $employe]
                : ['ok' => false, 'code' => 'matricule_inconnu', 'message' => 'Matricule inconnu : verifiez la correspondance dans le terminal.'];
        }

        return ['ok' => false, 'code' => 'source_invalide', 'message' => 'Source inconnue.'];
    }

    private function employeParId($id) {
        if (!$id) {
            return null;
        }
        $stmt = $this->db->prepare("SELECT * FROM employes WHERE id_employe = :id");
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch() ?: null;
    }

    // ------------------------------------------------------------------
    // Presence du jour
    // ------------------------------------------------------------------

    private function presenceDuJour($id_employe, $date) {
        $stmt = $this->db->prepare(
            "SELECT * FROM presences WHERE id_employe = :id AND date_presence = :date LIMIT 1"
        );
        $stmt->execute(['id' => (int) $id_employe, 'date' => $date]);
        return $stmt->fetch() ?: null;
    }

    private function minutesRetard($heure) {
        $debut = strtotime(date('Y-m-d') . ' ' . HEURE_DEBUT . ':00');
        $arrivee = strtotime(date('Y-m-d') . ' ' . $heure);
        $minutes = (int) floor(($arrivee - $debut) / 60);
        return max(0, $minutes);
    }

    // ------------------------------------------------------------------
    // Anti-doublon
    // ------------------------------------------------------------------

    private function uidParDefaut($source, $id_dispositif, $identifiant, $horodatage) {
        if ($source === 'qr') {
            return 'qr:' . hash('sha256', $identifiant);
        }
        return substr(
            $source . ':' . hash('sha256', ($id_dispositif ?? 0) . '|' . $identifiant . '|' . $horodatage),
            0,
            64
        );
    }

    private function cleDedup($id_dispositif, $uid_externe) {
        return ((int) ($id_dispositif ?? 0)) . ':' . $uid_externe;
    }

    private function trouverParDedup($id_dispositif, $uid_externe) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE cle_dedup = :c LIMIT 1");
        $stmt->execute(['c' => $this->cleDedup($id_dispositif, $uid_externe)]);
        return $stmt->fetch() ?: null;
    }

    private function estDoublon(PDOException $e) {
        $info = $e->errorInfo ?? [];
        return isset($info[1]) && (int) $info[1] === 1062;
    }

    // ------------------------------------------------------------------
    // Donnees brutes : nettoyage des donnees sensibles
    // ------------------------------------------------------------------

    private function nettoyerDonnees($donnees) {
        if ($donnees === null || $donnees === '' || $donnees === []) {
            return [null, false];
        }

        $retire = false;
        $propre = $this->filtrerSensible($donnees, 0, $retire);

        $encode = json_encode($propre, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encode === false) {
            return [null, $retire];
        }
        if (strlen($encode) > self::TAILLE_MAX_DONNEES) {
            $encode = substr($encode, 0, self::TAILLE_MAX_DONNEES);
        }
        return [$encode, $retire];
    }

    /**
     * Supprime toute cle evoquant une donnee biometrique. Le terminal fait la
     * verification : nous ne conservons jamais gabarit, empreinte ou image.
     */
    private function filtrerSensible($valeur, $profondeur, &$retire) {
        if ($profondeur > 6) {
            return '[tronque]';
        }
        if (is_array($valeur)) {
            $sortie = [];
            foreach ($valeur as $cle => $v) {
                if (preg_match('/template|gabarit|empreinte|finger|face|visage|image|photo|biometr|iris|palm/i', (string) $cle)) {
                    $retire = true;
                    continue;
                }
                $sortie[$cle] = $this->filtrerSensible($v, $profondeur + 1, $retire);
            }
            return $sortie;
        }
        if (is_string($valeur)) {
            return mb_substr($valeur, 0, 1000);
        }
        if (is_scalar($valeur) || $valeur === null) {
            return $valeur;
        }
        return '[objet]';
    }

    // ------------------------------------------------------------------
    // Utilitaires
    // ------------------------------------------------------------------

    private function normaliserHorodatage($valeur) {
        if ($valeur === null || $valeur === '') {
            return date('Y-m-d H:i:s', $this->maintenant());
        }
        if (is_numeric($valeur)) {
            $ts = (int) $valeur;
            if ($ts > 9999999999) {
                $ts = (int) floor($ts / 1000);
            }
            return date('Y-m-d H:i:s', $ts);
        }
        $valeur = trim((string) $valeur);
        $ts = strtotime($valeur);
        if ($ts === false) {
            return null;
        }
        return date('Y-m-d H:i:s', $ts);
    }

    private function majEvenement($id, array $data) {
        return $this->update($id, $data);
    }

    private function court($message) {
        return mb_substr((string) $message, 0, 250);
    }

    private function echec($code, $message, $http = 400) {
        return ['ok' => false, 'code' => $code, 'statut' => 'rejete', 'message' => $message, 'http' => $http];
    }

    private function journaliser($source, $action, $employe, $horodatage, $idPresence, $idEvenement) {
        try {
            $details = json_encode([
                'source' => $source,
                'action' => $action,
                'horodatage' => $horodatage,
                'id_presence' => $idPresence,
                'id_evenement' => $idEvenement,
            ], JSON_UNESCAPED_UNICODE);
            $this->db->prepare(
                "INSERT INTO journal_activite (id_utilisateur, action, details, module, ip_adresse)
                 VALUES (NULL, :action, :details, 'presences', :ip)"
            )->execute([
                'action' => 'Pointage ' . $source,
                'details' => $details,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (Throwable $e) {
            // La journalisation ne doit jamais faire echouer un pointage.
        }
    }
}
