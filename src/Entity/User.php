<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements AdvancedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(min = 5,max = 100, minMessage="Username should be more than {{ limit }} characters")
     * @Assert\NotBlank(message="Username can not be blank")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\Length(min = 5,max = 20, minMessage="Name should be more than {{ limit }} characters")
     * @Assert\NotBlank(message="Password can not be blank")
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $successLogin;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $failedLogins = [];

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getFailedLogins()
    {
        return $this->failedLogins;
    }

    public function getSuccessLogin()
    {
        return $this->successLogin;
    }

    public function setSuccessLogin($time)
    {
        $this->successLogin = $time;
        return $this;
    }

    public function clearFailedLogins()
    {
        foreach ($this->failedLogins as $key => $fail) {
            if ($fail < (time() - 30)) {
                unset($this->failedLogins[$key]);
            }
        }
    }

    public function addFailedLogins($time)
    {
        $this->clearFailedLogins();
        if (count($this->failedLogins) < 5) $this->failedLogins[] = $time;
    }

    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        $this->clearFailedLogins();
        if (count($this->failedLogins) >= 5) {
            return false;
        }
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return true;
    }
}