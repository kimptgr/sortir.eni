<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class APICityPlaceController extends AbstractController
{
    #[Route('/api/city/{cityId}/places', name: 'api_city_places', methods: ['GET'])]
    public function getPlaces(int $cityId, SerializerInterface $serializer, CityRepository $cityRepository): JsonResponse
    {
        $city = $cityRepository->find($cityId);
        if (!$city) {
            return $this->json(['error' => 'City not found'], Response::HTTP_NOT_FOUND);
        }

        $places = $city->getPlaces();


        return $this->json($places, Response::HTTP_OK, [], ['groups' => ['place_list']]);

    }

}
