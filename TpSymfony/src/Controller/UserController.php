<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Preference;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('', name: 'users_list', methods: ['GET'])]
    public function list(UserRepository $repo, SerializerInterface $serializer): Response
    {
        $users = $repo->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => ['user']]);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'users_show', methods: ['GET'])]
    public function show(User $user, SerializerInterface $serializer): Response
    {
        $json = $serializer->serialize($user, 'json', ['groups' => ['user']]);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('', name: 'users_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, RoleRepository $roleRepo): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setEmail($data['email'] ?? null);
        $user->setNom($data['nom'] ?? null);
        $user->setPrenom($data['prenom'] ?? null);
        $user->setPassword(password_hash($data['password'] ?? '', PASSWORD_BCRYPT));
        $user->setCreatedAt(new \DateTime());

        // Set roles if sent
        if (!empty($data['roles'])) {
            $roles = [];
            foreach ($data['roles'] as $roleName) {
                $role = $roleRepo->findOneBy(['nom' => $roleName]);
                if ($role) {
                    $roles[] = $roleName;
                }
            }
            $user->setRoles($roles);
        }

        $em->persist($user);
        $em->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => ['user']]);
        return new Response($json, 201, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'users_update', methods: ['PUT'])]
    public function update(User $user, Request $request, EntityManagerInterface $em, RoleRepository $roleRepo, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) $user->setEmail($data['email']);
        if (isset($data['nom'])) $user->setNom($data['nom']);
        if (isset($data['prenom'])) $user->setPrenom($data['prenom']);
        if (isset($data['password'])) $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        if (isset($data['roles'])) {
            $roles = [];
            foreach ($data['roles'] as $roleName) {
                $role = $roleRepo->findOneBy(['nom' => $roleName]);
                if ($role) {
                    $roles[] = $roleName;
                }
            }
            $user->setRoles($roles);
        }

        $em->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => ['user']]);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'users_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();
        return new Response(null, 204);
    }

    #[Route('/{id}/preferences', name: 'users_preferences_show', methods: ['GET'])]
    public function showPreferences(User $user, SerializerInterface $serializer): Response
    {
        $preferences = $user->getPreference();
        $json = $serializer->serialize($preferences, 'json', ['groups' => ['preference']]);
        return new Response($json, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}/preferences', name: 'users_preferences_update', methods: ['PUT'])]
    public function updatePreferences(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $preference = $user->getPreference();

        if (!$preference) {
            $preference = new Preference();
            $user->setPreference($preference);
            $em->persist($preference);
        }

        if (isset($data['langue'])) $preference->setLangue($data['langue']);
        if (isset($data['theme'])) $preference->setTheme($data['theme']);
        if (isset($data['notifications'])) $preference->setNotifications((bool)$data['notifications']);

        $em->flush();

        return new Response('Préférences mises à jour', 200);
    }
}
