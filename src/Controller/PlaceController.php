<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/place')]
final class PlaceController extends AbstractController
{
    #[Route(name: 'app_place_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(PlaceRepository $placeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();

            return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('place/index.html.twig', [
            'places' => $placeRepository->findAll(),
            'place' => $place,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_place_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_place_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($place);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/place/create', name: 'place-create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request,
                           CityRepository $cityRepository,
                           EntityManagerInterface $entityManager,
                           ValidatorInterface $validator): JsonResponse
    {
        $csrfToken = $request->request->get('csrf_token');
        if (!$this->isCsrfTokenValid('create_lieu', $csrfToken)) {
            return new JsonResponse(['error' => 'Action non autorisée'], 403);
        }

        try {
            $place = new Place();
            $place->setName($request->request->get('name'));
            $place->setStreet($request->request->get('street'));

            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');
            $place->setLatitude((float)$latitude);
            $place->setLongitude((float)$longitude);

            $city = $cityRepository->findOneBy(['id' => $request->request->get('cityId')]);
            if (!$city) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Ville non trouvée',
                ], 408);
            }
           // $place->setCity($city);
            $city->addPlace($place);

            $errors = $validator->validate($place);
            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[] = $error->getMessage();
                }
                return new JsonResponse([
                    'success' => false,
                    'errors' => $errorsArray,
                ], 400);
            }
            $entityManager->persist($place);
            $entityManager->persist($city);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'place' => [
                    'id' => $place->getId(),
                    'name' => $place->getName(),
                    'street' => $place->getStreet(),
                    'latitude' => $place->getLatitude(),
                    'longitude' => $place->getLongitude(),
                    'postalCode' => $city->getPostalCode(),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur interne : ' . $e->getMessage(),
            ], 500);
        }
    }
}
