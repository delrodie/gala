<?php


namespace App\Utils;


use App\Repository\ClubRepository;
use App\Repository\ParticipantRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class Inscription
{
    public function __construct(EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, ClubRepository $clubRepository)
    {
        $this->em = $entityManager;
        $this->participantRepository = $participantRepository;
        $this->clubRepository = $clubRepository;
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

    public function addParticipant($participant)
    {
        $club = $this->clubRepository->findOneBy(['id'=>$participant->getClub()]);
        $nb = $club->getNbParticipant();
        $setNombre = $nb+1;
        $club->setNbParticipant($setNombre);
        $this->em->flush();

        return true;
    }
}