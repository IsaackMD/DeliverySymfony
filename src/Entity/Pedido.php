<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'pedidos')]
    private Collection $Usuario;

    #[ORM\Column(length: 255)]
    private ?string $Estatus = null;


    #[ORM\Column]
    private ?float $Precio = null;

    /**
     * @var Collection<int, PedidoMenu>
     */
    #[ORM\OneToMany(targetEntity: PedidoMenu::class, mappedBy: 'Pedido')]
    private Collection $pedidoMenus;

    public function __construct()
    {
        $this->Usuario = new ArrayCollection();
        $this->pedidoMenus = new ArrayCollection();
        
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsuario(): Collection
    {
        return $this->Usuario;
    }

    public function addUsuario(User $usuario): static
    {
        if (!$this->Usuario->contains($usuario)) {
            $this->Usuario->add($usuario);
        }

        return $this;
    }

    public function removeUsuario(User $usuario): static
    {
        $this->Usuario->removeElement($usuario);

        return $this;
    }



    public function getEstatus(): ?string
    {
        return $this->Estatus;
    }

    public function setEstatus(string $Estatus): static
    {
        $this->Estatus = $Estatus;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->Precio;
    }

    public function setPrecio(float $Precio): static
    {
        $this->Precio = $Precio;

        return $this;
    }

    /**
     * @return Collection<int, PedidoMenu>
     */
    public function getPedidoMenus(): Collection
    {
        return $this->pedidoMenus;
    }

    public function addPedidoMenu(PedidoMenu $pedidoMenu): static
    {
        if (!$this->pedidoMenus->contains($pedidoMenu)) {
            $this->pedidoMenus->add($pedidoMenu);
            $pedidoMenu->setPedido($this);
        }

        return $this;
    }

    public function removePedidoMenu(PedidoMenu $pedidoMenu): static
    {
        if ($this->pedidoMenus->removeElement($pedidoMenu)) {
            // set the owning side to null (unless already changed)
            if ($pedidoMenu->getPedido() === $this) {
                $pedidoMenu->setPedido(null);
            }
        }

        return $this;
    }
}
