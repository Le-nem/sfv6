<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'index_personne')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/{id<\d+>}', name: 'detail_personne')]
    public function detail(Personne $personne = null): Response
    {
        // $repository = $doctrine->getRepository(Personne::class);
        // $personne = $repository->find($id);
        if (!$personne) {
            $this->addFlash('error', "La personne n'existe pas");
            return $this->redirectToRoute('index_personne');
        }
        return $this->render('personne/detail.html.twig', ['personne' => $personne]);
    }

    #[Route('/all/{page?1}/{nbr?10}/{sortby?id}/{sort?asc}', name: 'all_personne')]
    public function all(ManagerRegistry $doctrine, $nbr, $page, $sortby, $sort): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findBy([], [$sortby => $sort], $nbr, ($page - 1) * $nbr);
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/add', name: 'add_personne')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $personne = new Personne();
        $personne->setFirstname('Lenem');
        $personne->setName('Ludo');
        $personne->setAge('35');

        $entityManager->persist($personne);
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }
}
