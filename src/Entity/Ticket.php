<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tabble;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $place;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $flag;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $participant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invitePhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inviteMail;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $transfert;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getTabble(): ?string
    {
        return $this->tabble;
    }

    public function setTabble(?string $tabble): self
    {
        $this->tabble = $tabble;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getFlag(): ?bool
    {
        return $this->flag;
    }

    public function setFlag(?bool $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getInvite(): ?string
    {
        return $this->invite;
    }

    public function setInvite(?string $invite): self
    {
        $this->invite = $invite;

        return $this;
    }

    public function getInvitePhone(): ?string
    {
        return $this->invitePhone;
    }

    public function setInvitePhone(?string $invitePhone): self
    {
        $this->invitePhone = $invitePhone;

        return $this;
    }

    public function getInviteMail(): ?string
    {
        return $this->inviteMail;
    }

    public function setInviteMail(?string $inviteMail): self
    {
        $this->inviteMail = $inviteMail;

        return $this;
    }

    public function getTransfert(): ?int
    {
        return $this->transfert;
    }

    public function setTransfert(?int $transfert): self
    {
        $this->transfert = $transfert;

        return $this;
    }
}
