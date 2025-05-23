<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/users', name: 'admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        // Récupère tous les utilisateurs
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/{id}/edit', name: 'admin_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Utilisateur' => 'ROLE_USER',
                    'Éditeur' => 'ROLE_EDITOR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles',
            ])
            ->add('save', SubmitType::class, ['label' => 'Mettre à jour'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Rôles mis à jour avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
