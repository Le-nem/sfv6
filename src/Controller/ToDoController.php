<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoController extends AbstractController
{
    #[Route('/todo', name: 'app_to_do')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('todos')) {
            $todos = array(
                'achat' => 'acheter clé usb',
                'cours' => 'Finaliser mes cours',
                'correction' => 'Corriger mes examens'
            );
            $session->set('todos', $todos);
            $this->addFlash('info', "La liste viens d'etre initialiser");
        }

        return $this->render('to_do/index.html.twig', [
            'controller_name' => 'ToDoController',
        ]);
    }
    #[Route('/todo/add/{name}/{content})', name: 'todo.add')]
    public function addToDo(Request $request, $name, $content)
    {
        $session = $request->getSession();
        if ($session->has('todos')) {
            $todos = $session->get('todos');
            if (isset($todos[$name])) {
                $this->addFlash('error', "Le todo d'id $name existe deja dans la liste");
            } else {
                $todos[$name] = $content;
                $this->addFlash('success', "L'élément $content a été ajouter");
            }
        } else {
            $this->addFlash('error', "La liste n'a pas encore été initialisé");
        }
        return $this->redirectToRoute('todo.add');
    }
}
