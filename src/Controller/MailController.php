<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mail")
 */
class MailController extends AbstractController
{
    /**
     * @Route("/{slug}", name="mail_send", methods={"GET"})
     */
    public function inscription(Participant $participant, \Swift_Mailer $mailer, TicketRepository $ticketRepository)
    {

        $tickets = $ticketRepository->findBy(['participant'=>$participant->getId()]);
        foreach ($tickets as $ticket){
            $message = (new \Swift_Message('GALA DU GOUVERNEUR TICKET NO:'.$ticket->getId()))
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
        }

        $this->addFlash('success',"Votre inscription a bien été enregistrée. Veuillez Consulter votre adresse email pour votre code!");


        return $this->redirectToRoute('homepage');
    }
}