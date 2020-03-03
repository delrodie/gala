<?php


namespace App\Controller;


use App\Entity\Participant;
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
    public function inscription(Participant $participant, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('ROTARY: GALA DU GOUVERNEUR'))
            ->setFrom('delrodieamoikon@gmail.com')
            ->setTo($participant->getEmail())
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
        $this->addFlash('success',"Votre inscription a bien été enregistrée. Consultez votre adresse email pour votre code!");
        return $this->redirectToRoute('homepage');
    }
}