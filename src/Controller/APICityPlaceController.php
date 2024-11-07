<?php

namespace App\Controller;

use App\Entity\Category;
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
        // Récupérer la ville par son ID
        $city = $cityRepository->find($cityId);

        // Si la ville n'existe pas, retourner une réponse 404
        if (!$city) {
            return new JsonResponse(['error' => 'City not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récupérer les places associées à la ville
        $places = $city->getPlaces();

        // Sérialiser les données avec les groupes de sérialisation
        $data = $serializer->serialize($places, 'json', ['groups' => 'place_list']);

        // Retourner la réponse sous format JSON
        return new JsonResponse($data, JsonResponse::HTTP_OK, [], true);
    }

}
