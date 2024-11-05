<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripFilterType;
use App\Form\TripType;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use App\Service\Filters\TripFilterService;
use App\Service\Trip\NewTripService;
use Container3AjzDap\getStateRepositoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trip')]
final class TripController extends AbstractController
{
    #[Route(name: 'app_trip_index', methods: ['GET', 'POST'])]
    public function index(Request $request, TripRepository $tripRepository, TripFilterService $tripFilterService): Response
    {
        $form = $this->createForm(TripFilterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filterChoices = $form->getData();

            return $this->render('trip/index.html.twig', [
                'trips' => $tripFilterService->getTripWithFilters($filterChoices, $this->getUser()),
                'form' => $form,
                'choices' => $filterChoices,
            ]);
        }

        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_trip_new', methods: ['GET', 'POST'])]
    public function new(Request $request,NewTripService $tripService, EntityManagerInterface $entityManager): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->has('save')) {
                $tripService->NewTrip($trip, "Créée");
            } else {
                $tripService->NewTrip($trip, "Ouverte");
            }

            //On recupère le USER pour l'attribuer au Trip
            $trip->setOrganizer($this->getUser());

            $entityManager->persist($trip);
            $entityManager->flush();

            return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_show', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function show(Trip $trip): Response
    {
        return $this->render('trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trip_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function edit(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() != $trip->getOrganizer()){
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trip_delete', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function delete(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() != $trip->getOrganizer()){
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trip_index', [], Response::HTTP_SEE_OTHER);
    }
}
