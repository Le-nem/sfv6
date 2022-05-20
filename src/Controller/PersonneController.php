<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        return $this->render('personne/index.html.twig', ['personnes' => $personnes, 'isPaginated' => true]);
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
        return $this->render('personne/detail.html.twig', ['personne' => $personne, 'isPaginated' => true]);
    }

    #[Route('/all/{page?1}/{nbr?10}/{sortby?id}/{sort?asc}', name: 'all_personne')]
    public function all(ManagerRegistry $doctrine, $nbr, $page, $sortby, $sort): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbrPage = ceil($nbPersonne / $nbr);
        $personnes = $repository->findBy([], [$sortby => $sort], $nbr, ($page - 1) * $nbr);

        return $this->render('personne/index.html.twig', [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbrPage' => $nbrPage,
            'page' => $page,
            'nbr' => $nbr
        ]);
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
    #[Route('/delete/{id}', name: 'delete_personne')]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($personne) {
            $manager = $doctrine->getManager();
            $manager->remove($personne);
            $manager->flush();
            $this->addFlash('success', 'Supression ok');
        } else {
            $this->addFlash('error', 'Personne innexistante');
        }
        return $this->redirectToRoute('all_personne');
    }
    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'update_personne')]
    public function updatePersonne(Personne $personne = null, $name, $firstname, $age, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($personne) {
            $manager = $doctrine->getManager();
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', 'Edition Ok');
        } else {
            $this->addFlash('error', 'Edition pas OK');
        }
        return $this->redirectToRoute('all_personne');
    }
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'stat_personne')]
    public function statPersonne(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', ['stats' => $stats[0], 'ageMin' => $ageMin, 'ageMax' => $ageMax]);
    }
}
