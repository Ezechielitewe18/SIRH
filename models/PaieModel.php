<?php
require_once __DIR__ . '/../core/Model.php';

class PaieModel extends Model {
    protected $table = 'bulletins';
    protected $primaryKey = 'id_bulletin';

    public function getAllParameters() {
        $sql = "SELECT * FROM parametres_paie ORDER BY nom_parametre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getParameter($nom) {
        $sql = "SELECT * FROM parametres_paie WHERE nom_parametre = :nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        return $stmt->fetch();
    }

    /* Calcul du bulletin pour un employé donné sur un mois */
    public function calculerBulletin($idEmploye, $mois, $annee) {
        $em = new EmployeeModel();
        $emp = $em->findById($idEmploye);

        if (!$emp) {
            return ['success' => false, 'message' => 'Employé introuvable'];
        }

        $salaireBase = (float)($emp['salaire'] ?? 0);

        // Récupérer la prime logement et transport (en % du salaire base)
        $primeLogementPct = (float)($this->getParameter('Indemnité logement')['valeur'] ?? 0);
        $primeTransportPct = (float)($this->getParameter('Prime transport')['valeur'] ?? 0);

        $primeLogement = $salaireBase * $primeLogementPct / 100;
        $primeTransport = $salaireBase * $primeTransportPct / 100;
        $primes = $primeLogement + $primeTransport;

        // Heures supplémentaires durant le mois
        $heuresSup = $this->calculerHeuresSupplementaires($idEmploye, $mois, $annee);
        $tauxHoraire = $this->calculerTauxHoraire($salaireBase);
        $majorationPct = (float)($this->getParameter('Taux heure supplémentaire')['valeur'] ?? 150) / 100;
        $montantHeuresSup = $heuresSup * $tauxHoraire * $majorationPct;

        $totalBrut = $salaireBase + $primes + $montantHeuresSup;

        // Retenues (en % du brut)
        $taxePct = (float)($this->getParameter('Taxe professionnelle')['valeur'] ?? 0);
        $socialPct = (float)($this->getParameter('Prestation sociale')['valeur'] ?? 0);
        $retraitePct = (float)($this->getParameter('Pension retraite')['valeur'] ?? 0);

        $retenueTaxe = $totalBrut * $taxePct / 100;
        $retenueSocial = $totalBrut * $socialPct / 100;
        $retenueRetraite = $totalBrut * $retraitePct / 100;

        $totalRetenues = $retenueTaxe + $retenueSocial + $retenueRetraite;
        $totalNet = $totalBrut - $totalRetenues;

        return [
            'success' => true,
            'data' => [
                'salaire_base' => round($salaireBase, 2),
                'primes' => round($primes, 2),
                'prime_logement' => round($primeLogement, 2),
                'prime_transport' => round($primeTransport, 2),
                'heures_supplementaires' => round($heuresSup, 2),
                'montant_heures_sup' => round($montantHeuresSup, 2),
                'total_brut' => round($totalBrut, 2),
                'retenue_taxe' => round($retenueTaxe, 2),
                'retenue_sociale' => round($retenueSocial, 2),
                'retenue_retraite' => round($retenueRetraite, 2),
                'total_retenues' => round($totalRetenues, 2),
                'total_net' => round($totalNet, 2),
            ]
        ];
    }

    public function calculerTauxHoraire($salaireBase) {
        $heuresParMois = (float)($this->getParameter('Heures travail / mois')['valeur'] ?? 176);
        if ($heuresParMois <= 0) $heuresParMois = 176;
        return $heuresParMois > 0 ? $salaireBase / $heuresParMois : 0;
    }

    public function calculerHeuresSupplementaires($idEmploye, $mois, $annee) {
        // Heures de travail par jour (ex: 8h)
        $heureDebut = new DateTime(HEURE_DEBUT);
        $heureFin = new DateTime(HEURE_FIN);
        $ecart = $heureDebut->diff($heureFin);
        $heuresNormalesParJour = $ecart->h;

        $pm = new PresenceModel();
        $sql = "SELECT * FROM presences 
                WHERE id_employe = :id AND MONTH(date_presence) = :mois AND YEAR(date_presence) = :annee
                AND validation IN ('auto','validee')
                AND heure_arrivee IS NOT NULL AND heure_depart IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idEmploye, 'mois' => $mois, 'annee' => $annee]);
        $presences = $stmt->fetchAll();

        $totalHeuresSup = 0;
        foreach ($presences as $p) {
            $arrivee = new DateTime($p['heure_arrivee']);
            $depart = new DateTime($p['heure_depart']);
            $diff = $arrivee->diff($depart);
            $heuresTravaillees = $diff->h + $diff->i / 60;

            if ($heuresTravaillees > $heuresNormalesParJour) {
                $totalHeuresSup += ($heuresTravaillees - $heuresNormalesParJour);
            }
        }
        return $totalHeuresSup;
    }

    public function genererBulletin($idEmploye, $mois, $annee) {
        $resultat = $this->calculerBulletin($idEmploye, $mois, $annee);
        if (!$resultat['success']) {
            return $resultat;
        }

        $d = $resultat['data'];

        // Vérifier si un bulletin existe déjà
        $existant = $this->find(['id_employe' => $idEmploye, 'mois' => $mois, 'annee' => $annee]);
        if (!empty($existant)) {
            $id = $existant[0]['id_bulletin'];
            $this->update($id, [
                'salaire_base' => $d['salaire_base'],
                'primes' => $d['primes'],
                'heures_supplementaires' => $d['heures_supplementaires'],
                'montant_heures_sup' => $d['montant_heures_sup'],
                'total_brut' => $d['total_brut'],
                'total_retenues' => $d['total_retenues'],
                'total_net' => $d['total_net'],
                'statut' => 'brouillon',
                'date_generation' => date('Y-m-d H:i:s')
            ]);
            return ['success' => true, 'message' => 'Bulletin régénéré', 'data' => $d];
        }

        $id = $this->create([
            'id_employe' => $idEmploye,
            'mois' => $mois,
            'annee' => $annee,
            'salaire_base' => $d['salaire_base'],
            'primes' => $d['primes'],
            'heures_supplementaires' => $d['heures_supplementaires'],
            'montant_heures_sup' => $d['montant_heures_sup'],
            'total_brut' => $d['total_brut'],
            'total_retenues' => $d['total_retenues'],
            'total_net' => $d['total_net'],
            'statut' => 'brouillon',
            'date_generation' => date('Y-m-d H:i:s')
        ]);

        return ['success' => true, 'message' => 'Bulletin généré', 'bulletin_id' => $id, 'data' => $d];
    }

    public function findAllWithEmployee($mois = null, $annee = null) {
        $sql = "SELECT b.*, e.nom, e.prenom, e.matricule, s.nom_service
                FROM bulletins b
                INNER JOIN employes e ON b.id_employe = e.id_employe
                LEFT JOIN services s ON e.id_service = s.id_service";
        $params = [];
        $where = [];
        if ($mois) { $where[] = 'b.mois = :mois'; $params['mois'] = $mois; }
        if ($annee) { $where[] = 'b.annee = :annee'; $params['annee'] = $annee; }
        if (!empty($where)) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY b.annee DESC, b.mois DESC, e.nom ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function validerBulletin($id) {
        return $this->update($id, ['statut' => 'valide']);
    }

    public function getArchiveMois() {
        $sql = "SELECT annee, mois, COUNT(*) as nb_bulletins,
                       SUM(total_brut) as total_brut,
                       SUM(total_net) as total_net,
                       SUM(CASE WHEN statut = 'paye' THEN 1 ELSE 0 END) as payes,
                       SUM(CASE WHEN statut = 'valide' THEN 1 ELSE 0 END) as valides,
                       SUM(CASE WHEN statut = 'brouillon' THEN 1 ELSE 0 END) as brouillons
                FROM bulletins
                GROUP BY annee, mois
                ORDER BY annee DESC, mois DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getPaieEvolution($annee) {
        $sql = "SELECT mois, SUM(total_net) as total_net, SUM(total_brut) as total_brut
                FROM bulletins
                WHERE annee = :annee
                GROUP BY mois
                ORDER BY mois ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['annee' => $annee]);
        return $stmt->fetchAll();
    }

    public function payerBulletin($id) {
        return $this->update($id, ['statut' => 'paye']);
    }
}
