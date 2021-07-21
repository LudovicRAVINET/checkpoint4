<?php

namespace App\Controller;

use App\Entity\VehicleType;
use App\Form\VehicleTypeType;
use App\Repository\VehicleTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vehicle/type")
 */
class VehicleTypeController extends AbstractController
{
    /**
     * @Route("/", name="vehicle_type_index", methods={"GET"})
     */
    public function index(VehicleTypeRepository $vehicleTypeRepository): Response
    {
        return $this->render('vehicle_type/index.html.twig', [
            'vehicle_types' => $vehicleTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="vehicle_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $vehicleType = new VehicleType();
        $form = $this->createForm(VehicleTypeType::class, $vehicleType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vehicleType);
            $entityManager->flush();

            return $this->redirectToRoute('vehicle_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vehicle_type/new.html.twig', [
            'vehicle_type' => $vehicleType,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="vehicle_type_show", methods={"GET"})
     */
    public function show(VehicleType $vehicleType): Response
    {
        return $this->render('vehicle_type/show.html.twig', [
            'vehicle_type' => $vehicleType,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="vehicle_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, VehicleType $vehicleType): Response
    {
        $form = $this->createForm(VehicleTypeType::class, $vehicleType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vehicle_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('vehicle_type/edit.html.twig', [
            'vehicle_type' => $vehicleType,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="vehicle_type_delete", methods={"POST"})
     */
    public function delete(Request $request, VehicleType $vehicleType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicleType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vehicleType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vehicle_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
