<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Repository\TicketRepository;
use App\Utils\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

/**
 * @Route("/mail")
 */
class MailController extends AbstractController
{
    /**
     * @Route("/{slug}", name="mail_send", methods={"GET"})
     */
    public function inscription(Participant $participant, \Swift_Mailer $mailer, Inscription $inscription, TicketRepository $ticketRepository)
    {

        $message = (new \Swift_Message('GALA DU GOUVERNEUR TICKET'))
            ->setFrom(['noreply@dreammakerci.com'=>'ROTARY DISTRICT 9101'])
            ->setTo($participant->getEmail())
            ->setBcc('delrodieamoikon@gmail.com')
            ->setBody(
                $this->renderView(
                    'email/inscription.html.twig',['participant'=>$participant]
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'email/inscription.txt.twig',['participant'=>$participant]
                ),
                'text/plain'
            )
        ;
        $mailer->send($message);
        //return $this->redirect("https://api.twilio.com/2010-04-01/Accounts/AC13253b5e5b4db3158a5d9f510d31289a/IncomingPhoneNumbers");
        /**
        $destinataire = "+225".$participant->getTelephone();

        $id = "ACa50080b2135b996ad0b618517522df36";
        $token = "99cc983bddcb74f9f7b1b6781081f017";
        $twilio = new Client($id, $token);
        $message = $twilio->messages->create($destinataire,
            [
                "from" => "+12055515088",
                "body" => "Gala du gouverneur votre inscription a bien été enregistrée. cliquez sur le lien pour telecharger votre qrCode http://galarotary.dreammakerci.com/ticket"
            ]
        );
        */
        //print($message->$id);

        // Envoie de whatsApp
        $ticket = $ticketRepository->findOneBy(['participant'=>$participant],['id'=>"ASC"]);
        $inscription->confirmation($ticket);

        $this->addFlash('success',"Votre inscription a bien été enregistrée. Veuillez Consulter votre adresse email pour votre code!");


        return $this->redirectToRoute('homepage');
    }
}