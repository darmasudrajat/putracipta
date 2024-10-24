<?php

namespace App\Controller\Admin;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Admin\User;
use App\Form\Admin\UserType;
use App\Grid\Admin\UserGridType;
use App\Repository\Admin\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/_list', name: 'app_admin_user__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function _list(Request $request, UserRepository $userRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(UserGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $users) = $userRepository->fetchData($criteria);

        return $this->renderForm("admin/user/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'users' => $users,
        ]);
    }

    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function index(): Response
    {
        return $this->render("admin/user/index.html.twig");
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function new(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->encodePassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password);
            $userRepository->add($user, true);

            if ($request->query->has('referral_id')) {
                $referralId = $request->query->get('referral_id');
                return $this->forward('App\Controller\ReferralController::assign', ['userId' => $user->getId(), 'referralId' => $referralId]);
            } else {
                return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER_MANAGEMENT')]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
