<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Elastica\Util;

class EndpointController extends AbstractController
{
    /**
     * @Route("/endpoint", name="endpoint", methods={"POST"})
     */
    public function index(TransformedFinder $candidateFinder, SerializerInterface $serializer, Request $request): Response
    {
        $querySearch = $request->request->get('search');
        $queryPage = (int)$request->request->get('itemsPerPage');
        $queryItemsPerPage = (int)$request->request->get('page');

        $search = Util::escapeTerm($querySearch);
        $pagination = $candidateFinder->findHybridPaginated($search);

        $pagination->setMaxPerPage($queryItemsPerPage);
        $pagination->setMaxNbPages($queryPage);

        $result = $pagination->getCurrentPageResults();

        $formated = [];
        foreach ($result as $item) {
            $formated[] = $serializer->serialize($item->getTransformed(), 'json', ['groups' => 'list_candidates']);
        }

        return $this->json([
            'results' => $formated
        ]);
    }
}
