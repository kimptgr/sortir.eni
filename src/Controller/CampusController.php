<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/campus')]
#[IsGranted("ROLE_ADMIN")]
final class CampusController extends AbstractController
{
    #[Route(name: 'app_campus_index', methods: ['GET', 'POST'])]
    public function index(CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campus/campusList.html.twig', [
            'campuses' => $campusRepository->findAll(),
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campus/campusEdit.html.twig', [
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campus_delete', methods: ['POST'])]
    public function delete(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
    }
}
