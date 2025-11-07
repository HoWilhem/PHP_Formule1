<?php

namespace App\Service;

use App\Entity\Pilote;
use Doctrine\ORM\EntityManagerInterface;

class PiloteSuspensionService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function appliquerPenalite(Pilote $pilote, int $pointsPerdus): void
    {
        $nouveauxPoints = max(0, $pilote->getPointsLicence() - $pointsPerdus);
        $pilote->setPointsLicence($nouveauxPoints);

        if ($nouveauxPoints < 1) {
            $pilote->setSuspendu(true);
        }

        $this->em->flush();
    }
}
