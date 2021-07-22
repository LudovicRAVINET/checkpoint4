<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\LicenseRepository;
use App\Repository\UserLicenseRepository;
use App\Repository\VehicleTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/booking")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/", name="booking_index", methods={"GET"})
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="booking_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="booking_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="booking_delete", methods={"POST"})
     */
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('booking_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/recommendation", name="booking_recommendation", methods={"GET","POST"})
     */
    public function recommendation(Request $request, LicenseRepository $licenseRepository, UserLicenseRepository $userLicenseRepository, VehicleTypeRepository $vehicleTypeRepository): Response
    {
        $moveScore = 0;

        if ($request->isMethod('POST')) {
            $house = htmlentities(trim($request->get('house')));
            $area = htmlentities(trim($request->get('area')));
            $heavy = htmlentities(trim($request->get('heavy')));
            $person = htmlentities(trim($request->get('person')));
            $moveScore = $house+$area+$heavy+$person;

            if ($moveScore < 50)
                $recommendedVehicle = 'Jumpy';

            if ($moveScore >= 50 && $moveScore < 80)
                $recommendedVehicle = 'Iveco';

            if ($moveScore >= 80 && $moveScore < 110)
                $recommendedVehicle = 'Ducato';

            if ($moveScore >= 110)
                $recommendedVehicle = 'Truck';
            //dd($request);
        }

        if ($this->getUser() !== null) {
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $truckLicense = $licenseRepository->findOneBy(['category' => 'C']);
            $userTruckLicense = $userLicenseRepository->findOneBy([
                'user' => $user,
                'license' => $truckLicense
            ]);
            $vehicles = $vehicleTypeRepository->findAll();

            return $this->render('booking/recommendation.html.twig', [
                'vehicles' => $vehicles,
                'userTruckLicense' => $userTruckLicense,
                'moveScore' => $moveScore,
                'recommendedVehicle' =>$recommendedVehicle ?? null
            ]);
        } else {
            $this->addFlash('danger', 'Connectez-vous pour accéder au guide du déménagement.');
            return $this->render('home/index.html.twig');
        }
    }
}
