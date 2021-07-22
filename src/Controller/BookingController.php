<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\VehicleType;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\LicenseRepository;
use App\Repository\UserLicenseRepository;
use App\Repository\VehicleTypeRepository;
use App\Service\VehicleAvailability;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
    public function recommendation(Request $request, SessionInterface $session, LicenseRepository $licenseRepository, UserLicenseRepository $userLicenseRepository, VehicleTypeRepository $vehicleTypeRepository): Response
    {
        $moveScore = 0;

        if ($request->isMethod('POST')) {
            $house = htmlentities(trim($request->get('house')));
            $area = htmlentities(trim($request->get('area')));
            $heavy = htmlentities(trim($request->get('heavy')));
            $person = htmlentities(trim($request->get('person')));
            $moveScore = $house+$area+$heavy+$person;
            $session->set('moveScore', $moveScore);

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

    /**
     * @Route("/duration/{id}", name="booking_duration", methods={"GET","POST"})
     */
    public function duration(VehicleType $vehicleType ,Request $request, VehicleAvailability $vehicleAvailability, SessionInterface $session): Response
    {
        $session->set('vehicleType', $vehicleType);
        $durationHours = 0;

        if ($request->isMethod('POST')) {
            if ($request->get('departureDay') == null || $request->get('arrivalDay') == null) {
                $departure = htmlentities(trim($request->get('departure')));
                $arrival = htmlentities(trim($request->get('arrival')));
                $person = htmlentities(trim($request->get('person')));

                $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $departure . "&destinations="
                    . $arrival . "&language=fr-FR&key=AIzaSyBiutFfvHQbJUi2fZRDgQKHmZEg-Hroa78";
                $raw = file_get_contents($url);
                $json = json_decode($raw, true);
                $rows = $json['rows'];

                if (!isset($rows[0])) {
                    $travelDuration = 'distance non calculée';
                } else {
                    $elements = $rows[0]['elements'];
                    $status = $elements[0]['status'];
                    if ($status == 'NOT_FOUND') {
                        $travelDuration = 'durée non calculée';
                    } else {
                        $travelDuration = $elements[0]['duration']['value'];
                        $textTravelDuration = $elements[0]['duration']['text'];
                    }
                }

                $moveScore = $session->get('moveScore');
                $durationHours = floor($travelDuration * 2 / 3600 + $moveScore / (10 * $person));
                $session->set('durationHours', $durationHours);
                $session->set('textTravelDuration', $textTravelDuration);
                $session->set('person', $person);
            }

            if ($request->get('departureDay') != null && $request->get('arrivalDay') != null) {
                $departureDay = new DateTime(htmlentities(trim($request->get('departureDay'))));
                $arrivalDay = new DateTime(htmlentities(trim($request->get('arrivalDay'))));
                if ($departureDay > $arrivalDay) {
                    $this->addFlash('danger', 'La date de retour ne peut pas être inférieure à la date de départ.');
                } else {
                    $vehicleType = $session->get('vehicleType');
                    $vehicle = $vehicleAvailability->oneIsAvailable($vehicleType, $departureDay, $arrivalDay);
                    if ($vehicle == null) {
                        $isAvailVehicle = '0';
                    } else {
                        $isAvailVehicle = '1';
                        $session->set('vehicle', $vehicle);
                        $session->set('departureDay', $departureDay);
                        $session->set('arrivalDay', $arrivalDay);
                    }
                    //dd($vehicle);
                }
                $durationHours = $session->get('durationHours');
                $textTravelDuration = $session->get('textTravelDuration');
                $person = $session->get('person');
            }
        }

        return $this->render('booking/duration.html.twig', [
            'durationHours' => $durationHours,
            'travelDuration' => $textTravelDuration ?? '',
            'departure' => $departure ?? '',
            'arrival' => $arrival ?? '',
            'departureDay' => $departureDay ?? null,
            'arrivalDay' => $arrivalDay ?? null,
            'person' => $person ?? '',
            'availableVehicle' => $vehicle ?? '',
            'isAvailableVehicle' => $isAvailVehicle ?? '',
        ]);
    }

    /**
     * @Route("/confirm", name="booking_confirm", methods={"GET","POST"})
     */
    public function confirm(Request $request, SessionInterface $session, MailerInterface $mailer): Response
    {
        $booking = new Booking();

        $booking->setStartDate($session->get('departureDay'));
        $booking->setReturnDate($session->get('arrivalDay'));
        $vehicle = $session->get('vehicle');
        $type = $vehicle->getType();
        //$booking->setVehicle($vehicle);
        $booking->setUser($this->getUser());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($booking);
        //$vehicle->addBooking($booking);
        //$entityManager->persist($vehicle);
        //$entityManager->persist($type);
        //dd($type);
        $entityManager->flush();

        $email = (new Email())
        ->from($this->getParameter('mailer_from'))
        ->to($this->getUser()->getEmail())
        ->subject('Confirmation de réservation')
        ->html($this->renderView('booking/confirmEmail.html.twig', ['booking' => $booking]));

        $mailer->send($email);

        $this->addFlash('success', 'La confirmation de votre réservation vient de vous être envoyée par mail.');

        return $this->redirectToRoute('home');
    }
}
