<?php

namespace App\Controller;

use App\Entity\License;
use App\Entity\User;
use App\Entity\UserLicense;
use App\Form\UserType;
use App\Repository\LicenseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="profile_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="profile_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="profile_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="profile_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/userAccess/{id}", name="profile_user_access", methods={"GET","POST"})
     */
    public function userAccess(Request $request, User $user, LicenseRepository $licenseRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre compte a bien été mis à jour.');
        }

        $allLicenses = $licenseRepository->findAll();

        return $this->renderForm('profile/userAccess.html.twig', [
            'user' => $user,
            'form' => $form,
            'allLicenses' => $allLicenses
        ]);
    }

    /**
     * @Route("/deleteUserLicense/{id}", name="profile_delete_user_licence", methods={"POST"})
     */
    public function deleteUserLicense(Request $request, FileUploader $fileUploader, UserLicense $userLicense): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userLicense->getId(), $request->request->get('_token'))) {
            $oldPicture = $userLicense->getPicture();
            $oldFile = $fileUploader->getTargetDirectory() . '/' . $oldPicture;
            if ($oldPicture != null && file_exists($oldFile)) {
                unlink($oldFile);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userLicense);
            $entityManager->flush();
            $this->addFlash('success', 'Votre permis a bien été supprimé.');
        } else {
            $this->addFlash('danger', 'Votre permis n\'a pas été supprimé.');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        return $this->redirectToRoute('profile_user_access', ['id' => $user->getId()]);
    }

    /**
     * @Route("/addUserLicense/{id}", name="profile_add_licence", methods={"POST"})
     */
    public function addUserLicense(Request $request, User $user, FileUploader $fileUploader, LicenseRepository $licenseRepository): Response
    {
        $licenseId = htmlentities(trim($request->get('category')));
        $file = $request->files->get('file');
        $license = $licenseRepository->find($licenseId);

        //dd($file);

        $userLicense = new UserLicense();
        $userLicense->setUser($user);
        $userLicense->setLicense($license);

        /** @var UploadedFile $pictureFile */
        $pictureFile = $file;
        if (!empty($pictureFile)) {
            $pictureFileName = $fileUploader->upload($pictureFile);
            $userLicense->setPicture($pictureFileName);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userLicense);
        $entityManager->flush();
        $this->addFlash('success', 'Votre permis a bien été ajouté.');
        
        return $this->redirectToRoute('profile_user_access', ['id' => $user->getId()]);
    }
}
