<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\InviteType;
use App\Repository\TicketRepository;
use App\Utils\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/invite")
 */
class InviteController extends AbstractController
{
    /**
     * @Route("/", name="invite_index")
     */
    public function index()
    {
        return $this->render('invite/index.html.twig', [
            'controller_name' => 'InviteController',
        ]);
    }

    /**
     * @Route("/{reference}", name="invite_edit", methods={"GET","POST"})
     */
    public function invitation(Request $request, Ticket $ticket, TicketRepository $ticketRepository, Inscription $inscription):Response
    {
        $form = $this->createForm(InviteType::class, $ticket); //dd($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $inscription->invitation($ticket);

            $this->addFlash('success', "Votre invitation a été envoyée avec succès au destinataire");

            return $this->redirectToRoute('invite_edit',['reference'=>$ticket->getReference()]);
        }

        return  $this->render('invite/edit.html.twig',[
            'ticket' => $ticket,
            'tickets' => $ticketRepository->findBy(['participant'=>$ticket->getParticipant()]),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{reference}/telechargement", name="invite_download", methods={"GET","POST"})
     */
    public function download($reference)
    {
        $qrCode = new File(__DIR__.'/../../public/qrCode/'.$reference.'.png');

        return $this->file($qrCode);
    }
}
