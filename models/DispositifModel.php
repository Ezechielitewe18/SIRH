<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Registre des appareils de pointage.
 *
 * Chaque appareil possede son propre jeton (jamais stocke en clair) et peut
 * etre revoque individuellement. Un appareil non declare ne peut pas envoyer
 * de pointage.
 */
class DispositifModel extends Model {
    protected $table = 'dispositifs';
    protected $primaryKey = 'id_dispositif';

    const TYPES = ['qr', 'rfid', 'biometrie', 'autre'];
    const STATUTS = ['actif', 'maintenance', 'hors_service'];

    /** Cree un appareil et retourne son jeton en clair UNE SEULE FOIS. */
    public function creer(array $data) {
        $code = trim((string) ($data['code'] ?? ''));
        $libelle = trim((string) ($data['libelle'] ?? ''));
        $type = (string) ($data['type'] ?? 'qr');

        if ($code === '' || !preg_match('/^[A-Za-z0-9._-]{2,50}$/', $code)) {
            return ['ok' => false, 'code' => 'code_invalide', 'message' => 'Code appareil invalide (lettres, chiffres, point, tiret ; 2 a 50 caracteres).'];
        }
        if ($libelle === '') {
            return ['ok' => false, 'code' => 'libelle_requis', 'message' => 'Le libelle de l\'appareil est obligatoire.'];
        }
        if (!in_array($type, self::TYPES, true)) {
            return ['ok' => false, 'code' => 'type_invalide', 'message' => 'Type d\'appareil invalide.'];
        }
        if ($this->find(['code' => $code])) {
            return ['ok' => false, 'code' => 'code_existe', 'message' => 'Un appareil porte deja ce code.'];
        }

        $token = bin2hex(random_bytes(24));
        $id = $this->create([
            'code' => $code,
            'libelle' => $libelle,
            'type' => $type,
            'marque' => $this->videOuNull($data['marque'] ?? null),
            'modele' => $this->videOuNull($data['modele'] ?? null),
            'numero_serie' => $this->videOuNull($data['numero_serie'] ?? null),
            'emplacement' => $this->videOuNull($data['emplacement'] ?? null),
            'jeton_hash' => $this->hacher($token),
            'statut' => 'actif',
            'notes' => $this->videOuNull($data['notes'] ?? null),
        ]);

        return [
            'ok' => true,
            'id_dispositif' => (int) $id,
            'token' => $token,
            'message' => 'Appareil cree. Conservez le jeton : il ne sera plus affiche.',
        ];
    }

    /**
     * Authentifie un appareil par son jeton.
     * Retourne la ligne de l'appareil, ou null si inconnu / revoque.
     */
    public function authentifier($token) {
        $token = trim((string) $token);
        if ($token === '') {
            return null;
        }

        $sql = "SELECT * FROM {$this->table} WHERE jeton_hash = :h LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['h' => $this->hacher($token)]);
        $row = $stmt->fetch();

        if (!$row || $row['statut'] !== 'actif') {
            return null;
        }

        $this->db->prepare("UPDATE {$this->table} SET dernier_contact = NOW() WHERE id_dispositif = :id")
                 ->execute(['id' => $row['id_dispositif']]);

        return $row;
    }

    /** Revoque un appareil : il est refuse des le message suivant. */
    public function revoquer($id_dispositif, $statut = 'hors_service', $motif = null) {
        if (!in_array($statut, ['maintenance', 'hors_service'], true)) {
            return ['ok' => false, 'message' => 'Statut de revocation invalide.'];
        }
        $dispositif = $this->findById($id_dispositif);
        if (!$dispositif) {
            return ['ok' => false, 'message' => 'Appareil introuvable.'];
        }
        $notes = trim((string) $dispositif['notes'] . "\n" . date('Y-m-d H:i') . ' — ' . ($motif ?: 'revocation'));
        $this->update($id_dispositif, ['statut' => $statut, 'notes' => $notes]);
        return ['ok' => true, 'message' => 'Appareil ' . ($statut === 'maintenance' ? 'mis en maintenance' : 'revoque') . '.'];
    }

    /** Remet un appareil en service apres une maintenance. */
    public function reactiver($id_dispositif) {
        $dispositif = $this->findById($id_dispositif);
        if (!$dispositif) {
            return ['ok' => false, 'message' => 'Appareil introuvable.'];
        }
        $notes = trim((string) $dispositif['notes'] . "\n" . date('Y-m-d H:i') . ' — remise en service');
        $this->update($id_dispositif, ['statut' => 'actif', 'notes' => $notes]);
        return ['ok' => true, 'message' => 'Appareil remis en service.'];
    }

    /** Regenere le jeton (perte, vol, suspicion). L'ancien devient inutilisable. */
    public function regenererJeton($id_dispositif) {
        if (!$this->findById($id_dispositif)) {
            return ['ok' => false, 'message' => 'Appareil introuvable.'];
        }
        $token = bin2hex(random_bytes(24));
        $this->update($id_dispositif, ['jeton_hash' => $this->hacher($token)]);
        return ['ok' => true, 'token' => $token, 'message' => 'Nouveau jeton genere. L\'ancien est invalide.'];
    }

    /** Marque la date du test de reception reussi (audit). */
    public function marquerTeste($id_dispositif) {
        return $this->update($id_dispositif, ['teste_le' => date('Y-m-d H:i:s')]);
    }

    public function lister() {
        $sql = "SELECT * FROM {$this->table} ORDER BY statut ASC, libelle ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listerActifs() {
        $sql = "SELECT * FROM {$this->table} WHERE statut = 'actif' ORDER BY libelle ASC";
        return $this->db->query($sql)->fetchAll();
    }

    private function hacher($token) {
        return hash('sha256', $token);
    }

    private function videOuNull($valeur) {
        $valeur = trim((string) $valeur);
        return $valeur === '' ? null : $valeur;
    }
}
