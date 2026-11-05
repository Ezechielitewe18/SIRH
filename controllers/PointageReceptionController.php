<?php
require_once __DIR__ . '/../models/PointageModel.php';
require_once __DIR__ . '/../models/CarteModel.php';
require_once __DIR__ . '/../models/PresenceModel.php';
require_once __DIR__ . '/../models/LogModel.php';

/**
 * Poste de reception : pointage RFID au lecteur USB.
 *
 * Un lecteur USB « clavier » se comporte comme un clavier : il tape le
 * numero de la carte puis Entree. Aucun appareil reseau n'est necessaire,
 * mais l'operateur doit etre authentifie (admin ou RH) : le moteur verifie
 * son role avant d'accepter un pointage sans appareil declare.
 */
class PointageReceptionController {
    private $pointageModel;
    private $carteModel;
    private $presenceModel;
    private $logModel;

    public function __construct() {
        $this->pointageModel = new PointageModel();
        $this->carteModel = new CarteModel();
        $this->presenceModel = new PresenceModel();
        $this->logModel = new LogModel();
    }

    public function lecteur() {
        $date = $_GET['date'] ?? date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }
        $evenements = $this->pointageModel->evenementsDuJour($date);

        require __DIR__ . '/../views/presences/lecteur.php';
    }

    /** Traite une carte lue au poste de reception. Repond en JSON. */
    public function valider() {
        header('Content-Type: application/json; charset=utf-8');

        if (!csrf_verify()) {
            echo json_encode(['ok' => false, 'message' => 'Session expiree, rechargez la page.']);
            return;
        }

        $uid = trim((string) ($_POST['uid_carte'] ?? ''));
        if ($uid === '') {
            echo json_encode(['ok' => false, 'message' => 'Aucun numero de carte recu.']);
            return;
        }

        // Anti-double lecture : le lecteur peut envoyer deux fois la carte.
        $carte = $this->carteModel->employeParUid($uid);
        if ($carte) {
            $recent = $this->presenceModel->pointageRecent((int) $carte['id_employe'], 15);
            if ($recent) {
                echo json_encode([
                    'ok' => false,
                    'code' => 'trop_rapide',
                    'message' => 'Pointage deja pris en compte il y a quelques secondes. Patientez avant de repasser la carte.',
                    'employe' => [
                        'nom' => $carte['nom'],
                        'prenom' => $carte['prenom'],
                        'matricule' => $carte['matricule'],
                    ],
                ]);
                return;
            }
        }

        $resultat = $this->pointageModel->traiter([
            'source' => 'rfid',
            'identifiant' => $uid,
            'horodatage' => date('Y-m-d H:i:s'),
            'uid_externe' => 'poste:' . bin2hex(random_bytes(12)),
            'operateur' => $_SESSION['user_id'] ?? null,
        ]);

        if (!empty($resultat['ok'])) {
            $this->logModel->log(
                'Pointage RFID ' . ($resultat['action'] ?? ''),
                trim(($resultat['employe']['prenom'] ?? '') . ' ' . ($resultat['employe']['nom'] ?? '')
                    . ' (' . ($resultat['employe']['matricule'] ?? '') . ')'),
                'presences'
            );
            $resultat['employe'] = $resultat['employe'] ?? null;
        }

        echo json_encode($resultat, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
