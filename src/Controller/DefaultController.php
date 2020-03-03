<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Utils\Inscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET","POST"}))
     */
    public function index(Request $request, Inscription $inscription)
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //Generation du code du participant
            $code = $inscription->code();
            $participant->setCode($code);
            // Generation du slug
            $slug = $inscription->slug($participant);
            $participant->setSlug($slug);
            $entityManager->persist($participant);
            $entityManager->flush();

            $inscription->addParticipant($participant);

            return $this->redirectToRoute('inscription_show',['slug'=>$participant->getSlug()]);
        }


        return $this->render('default/index.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/inscription/{slug}", name="inscription_show", methods={"GET"})
     */
    public function show(Participant $participant) : Response
    {
        return $this->render("default/inscription_show.html.twig",[
            'participant' => $participant
        ]);
    }
}
