<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/personne')]
class PersonneController extends AbstractController
{

    public function __construct(private LoggerInterface $logger, private Helpers $helper)
    {}


    #[Route('/', name: 'index_personne')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', ['personnes' => $personnes, 'isPaginated' => true]);
    }

    #[Route('/pdf/{id<\d+>}',name:'pdf_personne')]
    public function generatePdfPersonne(Personne $personne=null, PdfService $pdf)
    {
        $html=$this->render('personne/detail.html.twig', ['personne' => $personne]);
        $pdf->showPdfFile($html);
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
        echo($this->helper->SayCc());
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

    #[Route('/edit/{id?0}', name: 'edit_personne')]
    public function addPersonne(Personne $personne = null, ManagerRegistry $doctrine, Request $request, UploaderService $upload): Response
    {
        if (!$personne) {
            $personne = new Personne();
        }
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updateAt');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $directory = $this->getParameter('personne_directory');
                $personne->setImage($upload->uploadFile($image,$directory));
            }
            $entityManager = $doctrine->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();
            $this->addFlash('success', $personne->getName() . " a été a editer");
            return $this->redirectToRoute('all_personne');
        } else {
            return $this->render('personne/add-personne.html.twig', ['form' => $form->createView()]);
        }
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
