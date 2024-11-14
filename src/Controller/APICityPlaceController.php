<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class APICityPlaceController extends AbstractController
{
    #[Route('/api/city/{cityId}', name: 'api_city_places', methods: ['GET'])]
    public function getPlaces(int $cityId, CityRepository $cityRepository): JsonResponse
    {
        $city = $cityRepository->find($cityId);
        if (!$city) {
            return $this->json(['error' => 'City not found'], Response::HTTP_NOT_FOUND);
        }

        $places = $city->getPlaces();


        return $this->json($places, Response::HTTP_OK, [], ['groups' => ['place_list']]);

    }

    #[Route('/api/places/{placeId}', name: 'api_places_info', methods: ['GET'])]
    public function getPlaceInfo(int $placeId, PlaceRepository $placeRepository): JsonResponse
    {
        $place = $placeRepository->find($placeId);
        if (!$place) {
            return $this->json(['error' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($place, Response::HTTP_OK, [], ['groups' => ['place_info']]);

    }

    #[Route('/api/places', name: 'api_place_create', methods: ['POST'])]
    public function createPlace(Request $request): JsonResponse
    {
        $place = new Place();
        $place->setName($request->request->get('name'));
        $place->setStreet($request->request->get('street'));
        $place->setPostalCode($request->request->get('postal_code'));
        $place->setLatitude($request->request->get('latitude'));
        $place->setLongitude($request->request->get('longitude'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($place);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'place' => [
                'id' => $place->getId(),
                'name' => $place->getName(),
                'street' => $place->getStreet(),
                'postal_code' => $place->getPostalCode(),
                'latitude' => $place->getLatitude(),
                'longitude' => $place->getLongitude(),
            ],
        ]);
    }

}
