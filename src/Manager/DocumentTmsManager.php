<?php
/*
 * @Author: Patrick &lt;&lt; rapaelec@gmail.com &gt;&gt; 
 * @Date: 2019-04-24 01:39:17 
 * @Last Modified by: Patrick << rapaelec@gmail.com >>
 * @Last Modified time: 2019-07-30 07:40:33
 */

 namespace App\Manager;

 use App\Entity\Commande;
 use App\Entity\TypeDemande;
 use App\Entity\UserInfos;
 use App\Entity\NewTitulaire;
 use App\Entity\Ancientitulaire;
 use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
 class DocumentTmsManager
 {
    public function __construct(
		TokenStorageInterface $tokenStorage
	)
	{
		$this->tokenStorage = $tokenStorage;
	}
    public function getParamsByCommande(Commande $commande, $type = "Cerfa")
    {
        switch ($commande->getDemarche()->getType()) {
            case TypeDemande::TYPE_DUP: 
                return $this->getParamDup($commande, $type);
                break;
            case TypeDemande::TYPE_DIVN: 
                return $this->getParamDivn($commande, $type);
                break;
            case TypeDemande::TYPE_CTVO: 
                return $this->getParamCtvo($commande, $type);
                break;
            case TypeDemande::TYPE_DCA: 
                return $this->getParamDca($commande, $type);
                break;
            case TypeDemande::TYPE_DCS: 
                return $this->getParamDcs($commande, $type);
                break;
            case TypeDemande::TYPE_DC: 
                return $this->getParamDc($commande, $type);
                break;
            default:
                return false;
        }
        
    }

    public function getParamDup(Commande $commande, $type = "Cerfa")
    {
        $client = $this->tokenStorage->getToken()->getUser()->getClient();
        $adresse = $client->getClientAdresse();
		$carInfo = $commande->getCarInfo();
		$now = new \DateTime();
        return [
			"Type"     => $type,
			"Demarche" => [
				$commande->getDemarche()->getType() => [
					'TypeDemarche' => $commande->getDemarche()->getType(),
					"MotifDuplicata" => $commande->getDemande()->getDuplicata()->getMotifDemande(),
					"DateDemarche" => $now->format('Y-m-d H:i:s'),
					"Titulaire" => [
						"DroitOpposition" => true,
						"NomPrenom" => $client->getClientNomPrenom(),
					],
					"Acquereur" => [
						"Adresse" => [
							"TypeVoie" => $adresse->getTypevoie(),
							"NomVoie" => $adresse->getNom(),
							"CodePostal" => $adresse->getCodepostal(),
							"Ville" => $adresse->getVille(),
							"Pays" => $adresse->getPays(),
						],
						"PersonneMorale" => [
							"SocieteCommerciale" =>true,
						]
					],
					"Vehicule" => [
						"VIN" => $carInfo->getVin(),
						"Immatriculation" => $commande->getImmatriculation(),
						"Marque" => $carInfo->getMarque(),
						"CIPresent" => false, // à voir si la carte grise n'est pas en sa possesion
					],
				],
			],
		];
    }

    public function getParamDivn(Commande $commande, $type = "Cerfa")
    {
        $client = $this->tokenStorage->getToken()->getUser()->getClient();
        $adresse = $client->getClientAdresse();
        $carInfo = $commande->getCarInfo();

        // check if persone moral or not: 
            if (
                NewTitulaire::TYPE_PERS_MORALE == 
                $commande->getDemande()->getDivn()->getAcquerreur()->getType()
            ) {
                $acquerreur =[
						"Adresse" => [
							"TypeVoie" => $adresse->getTypevoie(),
							"NomVoie" => $adresse->getNom(),
							"CodePostal" => $adresse->getCodepostal(),
							"Ville" => $adresse->getVille(),
							"Pays" => $adresse->getPays(),
                        ],
                        "PersoneMorale" => [
                            'RaisonSociale' => '',
                            "SocieteCommerciale" =>true,
                        ]
					] ;
            } else {
                $acquerreur =[
						"Adresse" => [
							"TypeVoie" => $adresse->getTypevoie(),
							"NomVoie" => $adresse->getNom(),
							"CodePostal" => $adresse->getCodepostal(),
							"Ville" => $adresse->getVille(),
                            "Pays" => $adresse->getPays(),
                            "Sexe" => "M",
                        ],
                        "PersoneMorale" => [
                            'RaisonSociale' => '',
                            "SocieteCommerciale" =>true,
                            "Sexe" => "M"
                        ]
                    ];
            }

        return [
			"Type"     => $type,
			"Demarche" => [
				$commande->getDemarche()->getType() => [
                    'ID' => '',
					'TypeDemarche' => $commande->getDemarche()->getType(),
					"Titulaire" => [
						"DroitOpposition" => true,
                        "NomPrenom" => $client->getClientNomPrenom(),
					],
					"Acquereur" => $acquerreur,
					"Vehicule" => [
						"VIN" => $carInfo->getVin(),
						"Immatriculation" => $commande->getImmatriculation(),
						"D1_Marque" => $carInfo->getMarque(),
						"D2_Version" => $carInfo->getModel(),
						"CIPresent" => false, // à voir si la carte grise n'est pas en sa possesion
					],
				],
			],
		];
	}
	
	public function getParamCtvo(Commande $commande, $type = "Cerfa")
    {
        $client = $commande->getFirstClient();
        $adresse = $client->getClientAdresse();
        $carInfo = $commande->getCarInfo();
		$now = new \DateTime();
		$ctvo = $commande->getDemande()->getCtvo();

        // check if persone moral or not: 
            if (
                NewTitulaire::TYPE_PERS_MORALE == 
                $commande->getDemande()->getCtvo()->getAcquerreur()->getType()
            ) {
                $acquerreur =[
						"Adresse" => [
							"TypeVoie" => $adresse->getTypevoie(),
							"NomVoie" => $adresse->getNom(),
							"CodePostal" => $adresse->getCodepostal(),
							"Ville" => $adresse->getVille(),
							"Pays" => $adresse->getPays(),
                        ],
                        "PersoneMorale" => [
                            'RaisonSociale' => '',
                            "SocieteCommerciale" =>true,
                        ]
					] ;
            } else {
                $acquerreur =[
						"Adresse" => [
							"TypeVoie" => $adresse->getTypevoie(),
							"NomVoie" => $adresse->getNom(),
							"CodePostal" => $adresse->getCodepostal(),
							"Ville" => $adresse->getVille(),
                            "Pays" => $adresse->getPays(),
                            "Sexe" => "M",
                        ],
                        "PersoneMorale" => [
                            'RaisonSociale' => '',
                            "SocieteCommerciale" =>true,
                            "Sexe" => "M"
                        ]
                    ];
            }

        return [
			"Type"     => $type,
			"Demarche" => [
				$commande->getDemarche()->getType() => [
                    'ID' => '',
					'TypeDemarche' => $commande->getDemarche()->getType(),
					"DateDemarche" => $now->format('Y-m-d H:i:s'),
					"NbCotitulaires" => $ctvo->countCotitulaire(),
					"Titulaire" => [
						"DroitOpposition" => true,
                        "NomPrenom" => $client->getClientNomPrenom(),
					],
					"Acquereur" => $acquerreur,
					"Vehicule" => [
						"VIN" => $carInfo->getVin(),
						"Immatriculation" => $commande->getImmatriculation(),
						"D1_Marque" => $carInfo->getMarque(),
						"D2_Version" => $carInfo->getModel(),
						"CIPresent" => false, // à voir si la carte grise n'est pas en sa possesion
					],
				],
			],
		];
    }
	
	public function getParamDca(Commande $commande, $type = "Cerfa")
    {
        $client = $this->tokenStorage->getToken()->getUser()->getClient();
        $adresse = $client->getClientAdresse();
        $carInfo = $commande->getCarInfo();
		$now = new \DateTime();
		$dca = $commande->getDemande()->getChangementAdresse();
		$ancienAdresse = $dca->getAncienAdresse();
		$nouvelleAdresse = $dca->getNouveauxTitulaire()->getAdresseNewTitulaire();

        return [
			"Type"     => $type,
			"Demarche" => [
				$commande->getDemarche()->getType() => [
                    'ID' => '',
					'TypeDemarche' => $commande->getDemarche()->getType(),
					"DateDemarche" => $now->format('Y-m-d H:i:s'),
					"Titulaire" => [
						"PersonnePhysique" => [
							"SocieteCommerciale" =>true,
							"DroitOpposition" => true,
							"NomPrenom" => $client->getClientNomPrenom(),
							"NomNaissance" => $client->getClientNom(),
							"Prenom" => $client->getClientPrenom(),
							"Sexe" => "M",
							"DateNaissance" => $client->getClientDateNaissance()->format('dm-Y'),
							"LieuNaissance" => $client->getClientLieuNaissance(),
						],
						"AncienneAdresse" => [
							"TypeVoie" => $ancienAdresse->getComplement(),
							"NomVoie" => $ancienAdresse->getNom(),
							"CodePostal" => $ancienAdresse->getCodepostal(),
							"Ville" => $ancienAdresse->getVille(),
						],
						"NouvelleAdresse" => [
							"TypeVoie" => $nouvelleAdresse->getComplement(),
							"NomVoie" => $nouvelleAdresse->getNom(),
							"CodePostal" => $nouvelleAdresse->getCodepostal(),
							"Ville" => $nouvelleAdresse->getVille(),
						]
					],
					"Vehicule" => [
						"VIN" => $carInfo->getVin(),
						"Immatriculation" => $commande->getImmatriculation(),
						"Marque" => $carInfo->getMarque(),
						"Couleur" => 'Noir',
						"D2_Version" => $carInfo->getModel(),
						"CIPresent" => false, // à voir si la carte grise n'est pas en sa possesion
					],
				],
			],
		];
    }
	
	public function getParamDcs(Commande $commande, $type = "Cerfa")
    {
        $client = $this->tokenStorage->getToken()->getUser()->getClient();
        $adresse = $client->getClientAdresse();
        $carInfo = $commande->getCarInfo();
		$now = new \DateTime();
		$dca = $commande->getDemande()->getChangementAdresse();
		$ancienAdresse = $dca->getAncienAdresse();
		$nouvelleAdresse = $dca->getNouveauxTitulaire()->getAdresseNewTitulaire();

        return [
			"Type"     => $type,
			"Demarche" => [
				$commande->getDemarche()->getType() => [
                    'ID' => '',
					'TypeDemarche' => $commande->getDemarche()->getType(),
					"DateDemarche" => $now->format('Y-m-d H:i:s'),
					"Titulaire" => [
						"PersonnePhysique" => [
							"SocieteCommerciale" =>true,
							"DroitOpposition" => true,
							"NomPrenom" => $client->getClientNomPrenom(),
							"NomNaissance" => $client->getClientNom(),
							"Prenom" => $client->getClientPrenom(),
							"Sexe" => "M" == $client->getClientGenre()? "M" : "F",
							"DateNaissance" => $client->getClientDateNaissance()->format('dm-Y'),
							"LieuNaissance" => $client->getClientLieuNaissance(),
						],
						"AncienneAdresse" => [
							"TypeVoie" => $ancienAdresse->getComplement(),
							"NomVoie" => $ancienAdresse->getNom(),
							"CodePostal" => $ancienAdresse->getCodepostal(),
							"Ville" => $ancienAdresse->getVille(),
						],
						"NouvelleAdresse" => [
							"TypeVoie" => $nouvelleAdresse->getComplement(),
							"NomVoie" => $nouvelleAdresse->getNom(),
							"CodePostal" => $nouvelleAdresse->getCodepostal(),
							"Ville" => $nouvelleAdresse->getVille(),
						]
					],
					"Vehicule" => [
						"VIN" => $carInfo->getVin(),
						"Immatriculation" => $commande->getImmatriculation(),
						"Marque" => $carInfo->getMarque(),
						"Couleur" => 'Noir',
						"D2_Version" => $carInfo->getModel(),
						"CIPresent" => false, // à voir si la carte grise n'est pas en sa possesion
					],
				],
			],
		];
	}
	
	public function getParamDc(Commande $commande, $type = "Cerfa")
    {
        $client = $this->tokenStorage->getToken()->getUser()->getClient();
        $carInfo = $commande->getCarInfo();
		$now = new \DateTime();
		$dc = $commande->getDemande()->getCession();
		$vendeur = $dc->getVendeur();
		$nouvelleAdresse = $dc->getAcheteur()->getAdresse();
		$acheteur = $dc->getAcheteur();

		$acquerreurValue = [];

		if (UserInfos::USER_PARTICULAR == $vendeur->getParticulierOrSociete()) {
			$acquerreurValue = [
					"PersonnePhysique" => [
						"Nom" => $acheteur->getNomPrenom(),
						"Prenom" => "",
						"Sexe" => UserInfos::GENRE_MALE === $acheteur->getGenre()? "M" : "F",
					],
					"Adresse" => [
						"TypeVoie" => $nouvelleAdresse->getComplement(),
						"NomVoie" => $nouvelleAdresse->getNom(),
						"CodePostal" => $nouvelleAdresse->getCodepostal(),
						"Ville" => $nouvelleAdresse->getVille(),
					]
				];
		} else {
				$acquerreurValue = [
					"PersonneMoral" => [
						"SocieteCommerciale" =>true,
						"DroitOpposition" => true,
					],
				];
		}

        return [
			"Type"     => $type,
			"Demarche" => [
				$commande->getDemarche()->getType() => [
                    'ID' => '',
					'TypeDemarche' => $commande->getDemarche()->getType(),
					"DateDemarche" => $now->format('Y-m-d H:i:s'),
					"DateCession" => $dc->getDateHeureDeLaVente()->format('Y-m-d H:i:s'),
					"Titulaire" => [
						"PersonnePhysique" => [
							"SocieteCommerciale" =>true,
							"DroitOpposition" => true,
							"NomPrenom" => $client->getClientNomPrenom(),
							"NomNaissance" => $client->getClientNom(),
							"Prenom" => $client->getClientPrenom(),
							"Sexe" => "M" == $client->getClientGenre()? "M" : "F",
							"DateNaissance" => $client->getClientDateNaissance()->format('dm-Y'),
							"LieuNaissance" => $client->getClientLieuNaissance(),
						],
					],
					"Acquereur" => $acquerreurValue,
					"Vehicule" => [
						"VIN" => $carInfo->getVin(),
						"Immatriculation" => $commande->getImmatriculation(),
						"CIPresent" => false, // à voir si la carte grise n'est pas en sa possesion
					],
				],
			],
		];
    }
 }
