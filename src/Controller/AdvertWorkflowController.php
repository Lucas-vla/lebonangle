<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Service\AdvertWorkflowManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertWorkflowController extends AbstractController
{
    private AdvertWorkflowManager $workflowManager;

    public function __construct(AdvertWorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
    }

    #[Route('/advert/{id}/publish', name: 'advert_publish', methods: ['POST'])]
    public function publish(Advert $advert): Response
    {
        $this->workflowManager->applyTransition($advert, 'publish');
        $this->addFlash('success', 'L\'annonce a été publiée avec succès.');
        return $this->redirectToRoute('advert_list');
    }

    #[Route('/advert/{id}/reject', name: 'advert_reject', methods: ['POST'])]
    public function reject(Advert $advert): Response
    {
        $this->workflowManager->applyTransition($advert, 'reject_from_draft');
        $this->addFlash('success', 'L\'annonce a été rejetée.');
        return $this->redirectToRoute('advert_list');
    }
}
