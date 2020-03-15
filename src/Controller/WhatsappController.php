<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/whatsapp")
 */
class WhatsappController extends AbstractController
{
    /**
     * @Route("/", name="whatsapp")
     */
    public function index(Request $request)
    {
        if ($request->isMethod('POST')){
            $telephone = $request->get('phone');
            $message = "Bonjour ".$request->get('body')." \n Je t'invite au Gala du Gouverneur du 11 Juillet 2019. \n
            Merci de cliquer sur le lien en dessous pour télécharger ton pass. \n \n ... Par ton parrain";
            $data = [
                'phone' => $telephone,
                'body' => $message
            ];
            $json = json_encode($data);
            $url = 'https://eu44.chat-api.com/instance106660/sendMessage?token=o73y7vrsx8bqahjg';
            $options = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => $json
                ]
            ]);
            $result = file_get_contents($url, false, $options);
            return $this->redirectToRoute('whatsapp');
            //return $this->redirect("https://eu44.chat-api.com/instance106660/sendMessage?token=o73y7vrsx8bqahjg");
        }
        return $this->render('whatsapp/index.html.twig', [
            'controller_name' => 'WhatsappController',
        ]);
    }
}
