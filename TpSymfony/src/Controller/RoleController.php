<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/roles')]
class RoleController extends AbstractController
{
    #[Route('', name: 'roles_list', methods: ['GET'])]
    public function list(RoleRepository $repo, SerializerInterface $serializer): Response
    {
        $roles = $repo->findAll();
        $json = $serializer->serialize($roles, 'json', ['groups' => ['role']]);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('', name: 'roles_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);
        $role = new Role();
        $role->setNom($data['nom'] ?? null);

        $em->persist($role);
        $em->flush();

        $json = $serializer->serialize($role, 'json', ['groups' => ['role']]);
        return new Response($json, 201, ['Content-Type' => 'application/json']);
    }
}
