<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\API\UserApi;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 *  @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"register"}},
 *     forceEager= false,
 *     itemOperations={
 *     "get",
 *     "put",
 *     "get_user"={
 *         "method"="GET",
 *         "path"="/users/{id}/user",
 *         "controller"=UserApi::class,
 *         "normalization_context"={"groups"={"info_user"}},
 *         "read"= false
 *     }
 *     }
 * )
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read", "info_user"})
     */
    protected $id;

    /**
     * @Groups({"read", "info_user", "write", "register"})
     */
    protected $username;

    /**
     * @var string The email of the user.
     *
     * @Groups({"read", "info_user", "write", "register"})
     */
    protected $email;

    /**
     * @var string The email of the user.
     *
     * @Groups({"register"})
     */
    protected $password;

    /**
    * @ORM\Column(type="datetime", nullable=true)
    * @Groups({"read", "write", "register"})
    */
    private $registerDate;

    /**
     * @ORM\Column(type="string", nullable=true)
    * @Groups({"write", "register"})
     */
    private $franceConnectId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client",inversedBy="user", cascade={"persist", "remove"})
     * @Groups({"read", "info_user", "write", "register"})
     */
    private $client;


    public function __construct()
    {
        parent::__construct();
        // your own logic
        if (empty($this->registerDate)) {
            $this->registerDate = new \DateTime();
        }
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegisterDate(): ?\DateTimeInterface
    {
        return $this->registerDate;
    }

    public function setRegisterDate(?\DateTimeInterface $registerDate): self
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getFranceConnectId(): ?string
    {
        return $this->franceConnectId;
    }

    public function setFranceConnectId(?string $franceConnectId): self
    {
        $this->franceConnectId = $franceConnectId;

        return $this;
    }

}