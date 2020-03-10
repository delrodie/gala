<?php


namespace App\Utils;


use App\Entity\Ticket;
use App\Repository\ClubRepository;
use App\Repository\ParticipantRepository;
use App\Repository\TicketRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class Inscription
{
    public function __construct(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, ClubRepository $clubRepository, TicketRepository $ticketRepository)
    {
        $this->em = $entityManager;
        $this->participantRepository = $participantRepository;
        $this->clubRepository = $clubRepository;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Generation du code du participant
     *
     * @return int|string
     */
    public function code() : string
    {
        $participants = $this->participantRepository->findAll();
        $nombre = count($participants)+1;
        if ($nombre < 10) $res = 'P000'.$nombre;
        elseif ($nombre < 100) $res = 'P00'.$nombre;
        elseif($nombre < 1000) $res = 'P0'.$nombre;
        else $res = $nombre;

        return $res;
    }

    /**
     * Generation du slug du participant
     *
     * @param $participant
     * @return string
     */
    public function slug($participant) : string
    {
        $slugify = new Slugify();
        $str = $participant->getCode().'-'.$participant->getNom().'-'.$participant->getPrenoms();
        $slug = $slugify->slugify($str);

        return $slug;
    }

    /**
     * Augmentation du nombre de participant dans le club
     *
     * @param $participant
     * @return bool
     */
    public function addParticipant($participant) : bool
    {
        $club = $this->clubRepository->findOneBy(['id'=>$participant->getClub()]);
        $nb = $club->getNbParticipant();
        $setNombre = $nb+1;
        $club->setNbParticipant($setNombre);
        $this->em->flush();

        return true;
    }

    public function addTicket($participant)
    {
        $nombre = $participant->getNombreTicket();
        $participantCode = $participant->getCode();
        for ($i=1;$i<=$nombre; $i++)
        {
            $code = $this->generateCode(4);
            $tickets = $this->ticketRepository->findAll();
            $nbTicket = count($tickets);
            $reference = $nbTicket.''.$code.'-'.$i.''.$participantCode;
            $ticket = new Ticket();
            $ticket->setReference($reference);
            $ticket->setParticipant($participant);
            $this->em->persist($ticket);
            $this->em->flush();
        }

        return true;
    }


    /**
     * Generation du code selon le nombre de caractère souhaité
     * @param $nbChar
     * @return false|string
     */
    protected function generateCode($nbChar)
    {
        //$str = 'abcdefghijklmnopqrstuvwxyzABCEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = 'ABCEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($str),1,$nbChar);
    }
}