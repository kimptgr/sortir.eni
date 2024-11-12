<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Participant\ParticipantImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParticipantImportController extends AbstractController
{
    #[Route('/participant-import', name: 'app_import_users')]
    #[isGranted('ROLE_ADMIN')]
    public function importComptes(ParticipantImportService $participantImportService, Request $request): Response
    {





        if ($request->isMethod('POST') && $request->files->has('csv_file')) {
            $file = $request->files->get('csv_file');

            // Vérifier si le fichier est bien un CSV
            if ($file->getClientOriginalExtension() === 'csv') {
                $filePath = $file->getPathname(); // On récupère le chemin temporaire du fichier téléchargé

                try {
                    $participantImportService->importUsersFromCsv($filePath); // Appel du service pour importer les utilisateurs
                    $this->addFlash('success', 'Les utilisateurs ont été importés avec succès.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'Veuillez télécharger un fichier CSV valide.');
            }
        }




        return $this->render('registration/import.html.twig');
    }
}
