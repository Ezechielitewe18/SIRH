<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * QR dynamique de presence.
 *
 * Principe : chaque employe possede un secret unique (qr_secret, 256 bits) connu
 * du serveur uniquement. Le serveur fabrique un jeton temporaire signe
 * (HMAC-SHA256) valable QR_PERIODE secondes. Le telephone de l'employe affiche
 * ce jeton sous forme de QR ; la reception le scanne et le serveur revalide la
 * signature. Un code copie ou envoye a distance expire en QR_PERIODE secondes.
 *
 * Format du jeton : SIRHQR1|id_employe|periode|signature
 */
class QrcodeModel extends Model {
    protected $table = 'employes';
    protected $primaryKey = 'id_employe';

    const PREFIXE = 'SIRHQR1';

    // ── Secrets ────────────────────────────────────────────────────────────

    public function getSecret($id_employe) {
        $sql = "SELECT qr_secret FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_employe]);
        $row = $stmt->fetch();
        if (!$row || $row['qr_secret'] === null || $row['qr_secret'] === '') {
            return null;
        }
        return $row['qr_secret'];
    }

    /** Cree le secret s'il manque. Retourne toujours un secret exploitable. */
    public function ensureSecret($id_employe) {
        $secret = $this->getSecret($id_employe);
        if ($secret !== null) {
            return $secret;
        }
        $secret = bin2hex(random_bytes(32));
        $sql = "UPDATE {$this->table} SET qr_secret = :secret WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['secret' => $secret, 'id' => $id_employe]);
        return $secret;
    }

    /** Regenere le secret (telephone perdu / compromission). */
    public function regenererSecret($id_employe) {
        $secret = bin2hex(random_bytes(32));
        $sql = "UPDATE {$this->table} SET qr_secret = :secret WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['secret' => $secret, 'id' => $id_employe]);
        return $secret;
    }

    // ── Jeton ──────────────────────────────────────────────────────────────

    public function periodeCourante() {
        return (int) floor(time() / QR_PERIODE);
    }

    private function signer($id_employe, $secret, $periode) {
        return substr(hash_hmac('sha256', $id_employe . '|' . $periode, $secret), 0, 20);
    }

    /**
     * Fabrique le jeton QR de l'instant pour un employe.
     * Retour : code, expire_le (timestamp), restant (secondes), periode.
     */
    public function genererJeton($id_employe) {
        $id = (int) $id_employe;
        $secret = $this->ensureSecret($id);
        $periode = $this->periodeCourante();

        $code = self::PREFIXE . '|' . $id . '|' . $periode . '|' . $this->signer($id, $secret, $periode);
        $expireLe = ($periode + 1) * QR_PERIODE;

        return [
            'code' => $code,
            'periode' => $periode,
            'expire_le' => $expireLe,
            'restant' => max(0, $expireLe - time()),
        ];
    }

    /**
     * Valide un jeton scanne par la reception.
     * Retourne ['success' => bool, 'message' => string, 'employe' => row|null].
     */
    public function validerJeton($code) {
        $code = trim((string) $code);
        $echec = function ($message, $employe = null) {
            return ['success' => false, 'message' => $message, 'employe' => $employe];
        };

        $parts = explode('|', $code);
        if (count($parts) !== 4 || $parts[0] !== self::PREFIXE) {
            return $echec('QR code non reconnu (format invalide).');
        }

        $id = (int) $parts[1];
        $periode = (int) $parts[2];
        $signature = $parts[3];

        if ($id <= 0 || $periode <= 0 || $signature === '') {
            return $echec('QR code non reconnu (donnees invalides).');
        }

        $employe = $this->findById($id);
        if (!$employe) {
            return $echec('Aucun employe ne correspond a ce QR code.');
        }

        if (($employe['statut'] ?? '') !== 'actif') {
            return $echec($employe['prenom'] . ' ' . $employe['nom'] . ' n\'est pas un employe actif.', $employe);
        }

        $secret = $this->getSecret($id);
        if ($secret === null) {
            return $echec('QR code non configure pour ' . $employe['prenom'] . ' ' . $employe['nom'] . '.', $employe);
        }

        $periodeActuelle = $this->periodeCourante();
        if ($periode < $periodeActuelle - QR_FENETRE || $periode > $periodeActuelle + QR_FENETRE) {
            return $echec('QR code expire : demandez a l\'employe de rafraichir son ecran.', $employe);
        }

        $attendu = $this->signer($id, $secret, $periode);
        if (!hash_equals($attendu, $signature)) {
            return $echec('QR code falsifie (signature invalide) : signalez-le au RH.', $employe);
        }

        return [
            'success' => true,
            'message' => 'Authentification reussie.',
            'employe' => $employe,
            'periode' => $periode,
        ];
    }
}