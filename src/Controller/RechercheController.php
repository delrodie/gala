<?php

namespace App\Controller;

use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recherche")
 */
class RechercheController extends AbstractController
{
    /**
     * @Route("/{code}", name="recherche_index")
     */
    public function index($code, TicketRepository $ticketRepository)
    {
        return $this->render('recherche/index.html.twig', [
            'ticket' => $ticketRepository->findOneBy(['reference'=>$code]),
        ]);
    }
}
