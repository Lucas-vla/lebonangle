<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/ads')]
class AdvertController extends AbstractController
{
    #[Route('/', name: 'ad_index', methods: ['GET'])]
    public function index(AdvertRepository $adRepository, Request $request): Response
    {
        $query = $adRepository->createQueryBuilder('a')
            ->select('a', 'COUNT(p.id) AS photo_count')
            ->leftJoin('a.pictures', 'p')
            ->groupBy('a.id')
            ->getQuery();

        return $this->render('advert/index.html.twig', [
            'ads' => $query->getResult(),
        ]);
    }

    #[Route('/{id}', name: 'ad_show', methods: ['GET'])]
    public function show(Advert $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    #[Route('/{id}/publish', name: 'ad_publish', methods: ['POST'])]
    public function publish(Advert $ad, EntityManagerInterface $entityManager): Response
    {
        $ad->setState('published');
        $entityManager->flush();

        return $this->redirectToRoute('ad_index');
    }

    #[Route('/{id}/reject', name: 'ad_reject', methods: ['POST'])]
    public function reject(Advert $ad, EntityManagerInterface $entityManager): Response
    {
        $ad->setState('rejected');
        $entityManager->flush();

        return $this->redirectToRoute('ad_index');
    }
}
