<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeDemandeRepository")
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}}
 * )
 */
class TypeDemande
{
    const TYPE_CTVO = 'CTVO';
    const TYPE_DUP = 'DUP';
    const TYPE_DIVN = 'DIVN';
    const TYPE_DCA = 'DCA';
    const TYPE_DC = 'DC';
    const TYPE_DCS = 'DCS';
    const TYPE_CHOICES = [
        self::TYPE_CTVO,
        self::TYPE_DUP,
        self::TYPE_DIVN,
        self::TYPE_DCA,
        self::TYPE_DC,
        self::TYPE_DCS,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Documents", mappedBy="type")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Documents", mappedBy="typeDemande")
     */
    private $docs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="demarche")
     */
    private $commandes;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->docs = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Documents[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->addType($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            $document->removeType($this);
        }

        return $this;
    }

    /**
     * @return Collection|Documents[]
     */
    public function getDocs(): Collection
    {
        return $this->docs;
    }

    public function addDoc(Documents $doc): self
    {
        if (!$this->docs->contains($doc)) {
            $this->docs[] = $doc;
            $doc->setTypeDemande($this);
        }

        return $this;
    }

    public function removeDoc(Documents $doc): self
    {
        if ($this->docs->contains($doc)) {
            $this->docs->removeElement($doc);
            // set the owning side to null (unless already changed)
            if ($doc->getTypeDemande() === $this) {
                $doc->setTypeDemande(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setDemarche($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getDemarche() === $this) {
                $commande->setDemarche(null);
            }
        }

        return $this;
    }
}
