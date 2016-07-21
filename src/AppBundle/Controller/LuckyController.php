<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends Controller {

    /**
     * @Route("/lucky/number/{count}")
     * @return Response
     */
    public function numberAction($count) {
        $numbers = [];
        for ($i = 0; $i < $count; $i++) {
            $numbers[] = rand(0, 100);
        }

        $numbersList = implode(', ', $numbers);

        /*
        $html = $this->container->get('templating')->render(
            'lucky/number.html.twig',
            ['luckyNumberList' => $numbersList]
        );

        return new Response($html);
        */

        return $this->render(
            'lucky/number.html.twig',
            ['luckyNumberList' => $numbersList]
        );
    }

    /**
     * @Route("/api/lucky/number")
     * @return Response
     */
    public function apiNumberAction()
    {
        $data = array(
            'lucky_number' => rand(0, 100),
        );

        return new JsonResponse($data);
    }

}