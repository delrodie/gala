<?php


namespace App\Utils;


use App\Entity\Ticket;
use App\Repository\ClubRepository;
use App\Repository\ParticipantRepository;
use App\Repository\TicketRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

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

    /**
     * Enregistrement des tickets
     *
     * @param $participant
     * @return bool
     */
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

            // Initialisation du Qr Code
            $lien = 'http://galarotary.dreammakerci.com/recherche/'.$reference;
            $qrCode = new QrCode($lien);
            $qrCode->setSize(400);
            // Set advanced options
            $qrCode->setWriterByName('png');
            $qrCode->setMargin(15);
            $qrCode->setEncoding('UTF-8');
            $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
            $qrCode->setForegroundColor(['r' => 10, 'g' => 4, 'b' => 249, 'a' => 0]);
            $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
            $qrCode->setLabel('Gala du Gouverneur - 11 juillet 2020', 13, __DIR__.'/../../public/fonts/Noto_Serif/NotoSerif-Italic.ttf', LabelAlignment::CENTER());
            $qrCode->setLogoPath(__DIR__.'/../../public/images/icone.png');
            $qrCode->setLogoSize(100, 100);
            $qrCode->setRoundBlockSize(true);
            $qrCode->setValidateResult(false);
            $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

            // Directly output the QR code
            header('Content-Type: '.$qrCode->getContentType());
            $qrCode->writeString();

            // Save it to a file
            $qrCode->writeFile(__DIR__.'/../../public/qrCode/'.$reference.'.png');//die('ici');

            // Create a response object
            //$response = new QrCodeResponse($qrCode);
        }

        return true;
    }

    /**
     * @param $ticket
     * @return bool
     */
    public function confirmation($ticket) : bool
    {
        $ticket->setTransfert(1);
        $ticket->setInvite($ticket->getParticipant()->getPrenoms().' '.$ticket->getParticipant()->getNom());
        $ticket->setInvitePhone($ticket->getParticipant()->getTelephone());
        $this->em->flush();

        // Envoi de WhatsApp
        $phone = $ticket->getParticipant()->getTelephone();
        $message = "GALA DU GOUVERNEUR \n\n Votre inscription au Gala du Gouverneur du 11 juillet 2020 a bien été enregistrée.\n Veuillez cliquer sur: \n - ce lien 👉http://galarotary.dreammakerci.com/invite/".$ticket->getReference()."/telechargement\n pour telecharger votre qrCode \n - ou sur celui ci-dessous(👇) pour transmettre vos tickets à vos invités.\n\n http://galarotary.dreammakerci.com/inscription/".$ticket->getParticipant()->getSlug()."#listeTickets. \n\n LE COMITE D'ORGANISATION VOUS REMERCIE!"
        ;

        $data = [
            'phone' => $phone,
            'body' => $message,
        ];
        $json = json_encode($data);
        $url = 'https://eu44.chat-api.com/instance107329/sendMessage?token=e1qhmfprcggkjbl7';
        $options = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $json
            ]
        ]);
        file_get_contents($url, false, $options);

        return true;
    }

    /**
     * Envoie de l'invitation par whatsApp
     * @param $ticket
     * @return bool
     */
    public function invitation($ticket) : bool
    {
        $ticket->setTransfert(1);
        $this->em->flush();

        // Envoi de WhatsApp
        $phone = $ticket->getInvitePhone();
        $message = "Bonjour ".$ticket->getInvite()." \n\n ".$ticket->getParticipant()->getPrenoms().' '.$ticket->getParticipant()->getNom()." vous invite au Gala du Gouverneur du 11 Juillet 2020 organisé par le ROTARY CLUB. \n Merci de cliquer sur le lien ci-dessous pour télécharger votre ticket.\n\n👉👉 http://galarotary.dreammakerci.com/invite/".$ticket->getReference()."/telechargement  \n\n Infoline +225 00 00 00 00 ";

        $data = [
            'phone' => $phone,
            'body' => $message,
        ];
        $json = json_encode($data);
        $url = 'https://eu44.chat-api.com/instance107329/sendMessage?token=e1qhmfprcggkjbl7';
        $options = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $json
            ]
        ]);
        file_get_contents($url, false, $options);

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