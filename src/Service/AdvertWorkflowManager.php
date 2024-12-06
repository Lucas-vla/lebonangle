<?php

namespace App\Service;

use App\Entity\Advert;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\ORM\EntityManagerInterface;

class AdvertWorkflowManager
{
    private WorkflowInterface $workflow;
    private MailerInterface $mailer;
    private EntityManagerInterface $em;

    public function __construct(WorkflowInterface $advertWorkflow, MailerInterface $mailer, EntityManagerInterface $em)
    {
        $this->workflow = $advertWorkflow;
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function applyTransition(Advert $advert, string $transition): void
    {
        if ($this->workflow->can($advert, $transition)) {
            $this->workflow->apply($advert, $transition);

            // Actions liées à la transition
            if ($advert->getStatus() === 'published') {
                $advert->setPublishedAt(new \DateTimeImmutable());
                $this->sendNotification($advert);
            }

            $this->em->flush();
        }
    }

    private function sendNotification(Advert $advert): void
    {
        $email = (new Email())
            ->from('no-reply@lebonangle.com')
            ->to($advert->getUser()->getEmail())
            ->subject('Votre annonce a été publiée')
            ->text(sprintf(
                'Félicitations, votre annonce "%s" a été publiée avec succès.',
                $advert->getTitle()
            ));

        $this->mailer->send($email);
    }
}
