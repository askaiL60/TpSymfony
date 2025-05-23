<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Personne;
use Doctrine\ORM\EntityManagerInterface;

final class PersonneController extends AbstractController
{
    #[Route('/personne', name: 'app_personne')]
    public function index(): Response
    {
        return $this->render('personne/index.html.twig', [
            'controller_name' => 'PersonneController',
            'nom' => 'ASSANI Aslan'
        ]);
    }

    #[Route('/ajouter-personne', name: 'app_personne_ajouter')]
    public function ajouterPersonne(EntityManagerInterface $em): JsonResponse
    {
        $personne = new Personne();
        $personne->setNom('assani');
        $personne->setPrenom('aslan');
        $personne->setEmail('assani.aslan@gmail.com');

        $em->persist($personne); // enregistre dans la base de donnée
        $em->flush();

        return new JsonResponse(['success' => true, 'personne' => [
            'id' => $personne->getId(),
            'nom' => $personne->getNom(),
            'prenom' => $personne->getPrenom(),
            'email' => $personne->getEmail(),
        ]]);
    }

    #[Route('/liste-personnes', name: 'app_personne_liste')]
    public function listePersonnes(EntityManagerInterface $em): JsonResponse
    {
        $personnes = $em->getRepository(Personne::class)->findAll();

        $data = [];

        foreach ($personnes as $personne) {
            $data[] = [
                'id' => $personne->getId(),
                'nom' => $personne->getNom(),
                'prenom' => $personne->getPrenom(),
                'email' => $personne->getEmail(),
            ];
        }

        return new JsonResponse($data);
    }
}
