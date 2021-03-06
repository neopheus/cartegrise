<?php
namespace App\Entity\Vehicule;

use App\Entity\Divn;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Vehicule\CaracteristiqueTechVehiculeNeufRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class CaracteristiqueTechVehiculeNeuf
{
    const CODE = [
        'TE Exclusif' => "TEEX",
        'TE Possible' => "TEPO",
        'Essieux posés en charge' => "EPEC",
        'Places modulables' => "PLMO",
        'Véhicule école' => "VECO",
        'Places médicales' => "PLME",
        'Transport handicapé' => "THAN",
        'Gazogène' => "GAZO",
        'Gaz compr.' => "GAZC",
        'Ralentisseur' => "RALE",
        'Autre G1 possible' => "AG1P",
        'Autre F3 possible' => "AF3P",
        'Autre J1 possible' => "AJ1P",
        'Autre J2 possible' => "AJ2P",
        'PL convoi 6 km/h maxi' => "PLCO",
        'Equip. accumulat.' => "EQAC",
        'Autre J3 possible' => "AJ3P",
        'V max (remorque)' => "VMAX",
    ];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Divn", inversedBy="caractTech")
     */
    private $divn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $valeur1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $valeur2;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getValeur1(): ?string
    {
        return $this->valeur1;
    }

    public function setValeur1(?string $valeur1): self
    {
        $this->valeur1 = $valeur1;

        return $this;
    }

    public function getValeur2(): ?string
    {
        return $this->valeur2;
    }

    public function setValeur2(?string $valeur2): self
    {
        $this->valeur2 = $valeur2;

        return $this;
    }

    public function getDivn(): ?Divn
    {
        return $this->divn;
    }

    public function setDivn(?Divn $divn): self
    {
        $this->divn = $divn;

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