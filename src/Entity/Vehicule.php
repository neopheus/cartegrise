<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VehiculeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Vehicule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cgpresent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $immatriculation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $vin;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numformule;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datecg;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Ancientitulaire", cascade={"persist", "remove"})
     */
    private $vehiculeAncientitulaire;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Cartegrise", cascade={"persist", "remove"})
     */
    private $vehiculeCartegrise;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Demande", cascade={"persist", "remove"})
     */
    private $vehiculeDemande;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="vehicules")
     */
    private $vehiculeClient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="clientVehicule")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NewTitulaire", inversedBy="vehicules")
     */
    private $Titulaire;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Demande", mappedBy="vehicule", cascade={"persist", "remove"})
     */
    private $demande;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Adresse", mappedBy="vehicules")
     */
    private $adresse;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->adresse = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCgpresent(): ?string
    {
        return $this->cgpresent;
    }

    public function setCgpresent(string $cgpresent): self
    {
        $this->cgpresent = $cgpresent;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function getNumformule(): ?int
    {
        return $this->numformule;
    }

    public function setNumformule(int $numformule): self
    {
        $this->numformule = $numformule;

        return $this;
    }

    public function getDatecg(): ?\DateTimeInterface
    {
        return $this->datecg;
    }

    public function setDatecg(\DateTimeInterface $datecg): self
    {
        $this->datecg = $datecg;

        return $this;
    }

    public function getVehiculeAncientitulaire(): ?Ancientitulaire
    {
        return $this->vehiculeAncientitulaire;
    }

    public function setVehiculeAncientitulaire(?Ancientitulaire $vehiculeAncientitulaire): self
    {
        $this->vehiculeAncientitulaire = $vehiculeAncientitulaire;

        return $this;
    }

    public function getVehiculeCartegrise(): ?Cartegrise
    {
        return $this->vehiculeCartegrise;
    }

    public function setVehiculeCartegrise(?Cartegrise $vehiculeCartegrise): self
    {
        $this->vehiculeCartegrise = $vehiculeCartegrise;

        return $this;
    }

    public function getVehiculeDemande(): ?Demande
    {
        return $this->vehiculeDemande;
    }

    public function setVehiculeDemande(?Demande $vehiculeDemande): self
    {
        $this->vehiculeDemande = $vehiculeDemande;

        return $this;
    }

    public function getVehiculeClient(): ?Client
    {
        return $this->vehiculeClient;
    }

    public function setVehiculeClient(?Client $vehiculeClient): self
    {
        $this->vehiculeClient = $vehiculeClient;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTitulaire(): ?NewTitulaire
    {
        return $this->Titulaire;
    }

    public function setTitulaire(?NewTitulaire $Titulaire): self
    {
        $this->Titulaire = $Titulaire;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;

        // set (or unset) the owning side of the relation if necessary
        $newVehicule = $demande === null ? null : $this;
        if ($newVehicule !== $demande->getVehicule()) {
            $demande->setVehicule($newVehicule);
        }

        return $this;
    }

    /**
     * @return Collection|Adresse[]
     */
    public function getAdresse(): Collection
    {
        return $this->adresse;
    }

    public function addAdresse(Adresse $adresse): self
    {
        if (!$this->adresse->contains($adresse)) {
            $this->adresse[] = $adresse;
            $adresse->setVehicules($this);
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self
    {
        if ($this->adresse->contains($adresse)) {
            $this->adresse->removeElement($adresse);
            // set the owning side to null (unless already changed)
            if ($adresse->getVehicules() === $this) {
                $adresse->setVehicules(null);
            }
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

}
