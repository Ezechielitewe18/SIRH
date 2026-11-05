<?php
require_once __DIR__ . '/../core/Model.php';

/**
 * Registre des badges RFID/NFC.
 *
 * Regles :
 *  - une carte appartient a un seul employe a la fois ;
 *  - une carte n'est jamais supprimee : elle change de statut ;
 *  - une carte perdue qui reapparait doit etre reconnue, pas traitee comme
 *    une carte inconnue.
 */
class CarteModel extends Model {
    protected $table = 'cartes';
    protected $primaryKey = 'id_carte';

    const STATUTS = ['active', 'perdue', 'desactivee', 'remplacee'];

    /** Employe titulaire d'une carte active, ou null. */
    public function employeParUid($uid) {
        $uid = $this->normaliserUid($uid);
        if ($uid === '') {
            return null;
        }
        $sql = "SELECT c.id_carte, c.uid_carte, c.technologie, c.statut AS statut_carte,
                       e.*
                FROM {$this->table} c
                INNER JOIN employes e ON c.id_employe = e.id_employe
                WHERE c.uid_carte = :uid AND c.statut = 'active'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $uid]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Retrouve une carte quel que soit son statut (diagnostic). */
    public function trouverParUid($uid) {
        $uid = $this->normaliserUid($uid);
        if ($uid === '') {
            return null;
        }
        $sql = "SELECT * FROM {$this->table} WHERE uid_carte = :uid LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $uid]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Attribue une carte a un employe.
     * Si la carte existe deja mais n'est plus active, elle est reactivee
     * pour ce titulaire (l'historique reste dans le journal).
     */
    public function attribuer($id_employe, $uid_carte, $technologie = null, $libelle = null) {
        $uid = $this->normaliserUid($uid_carte);
        if ($uid === '') {
            return ['ok' => false, 'code' => 'uid_invalide', 'message' => 'Numero de carte vide ou invalide.'];
        }

        $employe = $this->db->prepare("SELECT * FROM employes WHERE id_employe = :id");
        $employe->execute(['id' => (int) $id_employe]);
        if (!$employe->fetch()) {
            return ['ok' => false, 'code' => 'employe_inconnu', 'message' => 'Employe introuvable.'];
        }

        $existante = $this->trouverParUid($uid);
        if ($existante && $existante['statut'] === 'active') {
            return [
                'ok' => false,
                'code' => 'carte_deja_active',
                'message' => 'Cette carte est deja attribuee (employe #' . $existante['id_employe'] . ').',
            ];
        }

        if ($existante) {
            $this->update($existante['id_carte'], [
                'id_employe' => (int) $id_employe,
                'statut' => 'active',
                'date_attribution' => date('Y-m-d'),
                'date_desactivation' => null,
                'remplacee_par' => null,
                'technologie' => $technologie ?: $existante['technologie'],
                'libelle' => $libelle ?: $existante['libelle'],
            ]);
            return ['ok' => true, 'id_carte' => (int) $existante['id_carte'], 'message' => 'Carte reactivee pour cet employe.'];
        }

        $id = $this->create([
            'uid_carte' => $uid,
            'technologie' => $this->videOuNull($technologie),
            'id_employe' => (int) $id_employe,
            'libelle' => $this->videOuNull($libelle),
            'statut' => 'active',
            'date_attribution' => date('Y-m-d'),
        ]);

        return ['ok' => true, 'id_carte' => (int) $id, 'message' => 'Carte attribuee.'];
    }

    /** Desactive une carte sans la supprimer. */
    public function desactiver($id_carte, $statut = 'desactivee', $notes = null) {
        if (!in_array($statut, ['perdue', 'desactivee'], true)) {
            return ['ok' => false, 'code' => 'statut_invalide', 'message' => 'Statut de desactivation invalide.'];
        }
        $carte = $this->findById($id_carte);
        if (!$carte) {
            return ['ok' => false, 'code' => 'carte_inconnue', 'message' => 'Carte introuvable.'];
        }
        $this->update($id_carte, [
            'statut' => $statut,
            'date_desactivation' => date('Y-m-d H:i:s'),
            'notes' => $this->videOuNull($notes) ?: $carte['notes'],
        ]);
        return ['ok' => true, 'message' => $statut === 'perdue' ? 'Carte declaree perdue.' : 'Carte desactivee.'];
    }

    /**
     * Remplace une carte : l'ancienne est marquee 'remplacee' et pointe vers
     * la nouvelle. Aucune ligne n'est supprimee.
     */
    public function remplacer($id_ancienne, $uid_nouvelle, $technologie = null, $libelle = null) {
        $ancienne = $this->findById($id_ancienne);
        if (!$ancienne) {
            return ['ok' => false, 'code' => 'carte_inconnue', 'message' => 'Carte a remplacer introuvable.'];
        }

        $attribution = $this->attribuer($ancienne['id_employe'], $uid_nouvelle, $technologie, $libelle);
        if (!$attribution['ok']) {
            return $attribution;
        }

        $this->update($id_ancienne, [
            'statut' => 'remplacee',
            'date_desactivation' => date('Y-m-d H:i:s'),
            'remplacee_par' => $attribution['id_carte'],
        ]);

        return [
            'ok' => true,
            'id_carte' => $attribution['id_carte'],
            'message' => 'Carte remplacee. L\'ancienne reste enregistree (statut : remplacee).',
        ];
    }

    public function listerAvecEmploye() {
        $sql = "SELECT c.*, e.nom, e.prenom, e.matricule, e.statut AS statut_employe
                FROM {$this->table} c
                INNER JOIN employes e ON c.id_employe = e.id_employe
                ORDER BY c.statut ASC, e.nom ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function cartesDeLEmploye($id_employe) {
        $sql = "SELECT * FROM {$this->table} WHERE id_employe = :id ORDER BY statut ASC, date_attribution DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int) $id_employe]);
        return $stmt->fetchAll();
    }

    /** Normalise le numero lu : majuscules, sans espaces ni separateurs. */
    private function normaliserUid($uid) {
        $uid = strtoupper(trim((string) $uid));
        $uid = preg_replace('/[\s:.\-]/', '', $uid);
        return $uid;
    }

    private function videOuNull($valeur) {
        $valeur = trim((string) $valeur);
        return $valeur === '' ? null : $valeur;
    }
}
