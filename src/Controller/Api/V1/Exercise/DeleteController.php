<?php
namespace App\Controller\Api\V1\Exercise;

use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/exercises/{exercise}", name="app_api_v1_exercise_delete", methods={"DELETE"})
 */
class DeleteController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Request $request, Exercise $exercise): Response
    {
        $this->em->remove($exercise);
        $this->em->flush();

        return new Response('', Response::HTTP_OK);
    }
}
