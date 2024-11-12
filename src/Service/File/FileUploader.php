<?php



// src/Service/File/FileUploader.php
namespace App\Service\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    // Le répertoire cible pour stocker les fichiers téléchargés
    private string $targetDirectory;

    // Le constructeur prend le répertoire cible et une interface de slugger pour formater les noms de fichiers
    public function __construct(string $targetDirectory, private SluggerInterface $slugger)
    {
        // Initialisation du répertoire cible
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Upload un fichier et retourne son nom unique.
     *
     * @param UploadedFile $file - Fichier à uploader.
     * @return string - Le nom unique du fichier après l'upload.
     */
    public function upload(UploadedFile $file): string
    {




        // Extraire le nom de fichier d'origine sans extension
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Crée un nom de fichier sûr en utilisant le slugger pour éviter les caractères spéciaux
        $safeFilename = $this->slugger->slug($originalFilename);

        // Ajouter un identifiant unique pour éviter les doublons et conserver l'extension d'origine
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            // Déplace le fichier dans le répertoire cible en utilisant le nom unique généré
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // En cas d'erreur pendant le déplacement, on peut logger l'erreur ou lever une exception
            throw new \Exception('Une erreur est survenue pendant le téléchargement du fichier.');
        }

        // Retourne le nom du fichier pour stockage en BDD
        return $fileName;
    }


    public function delete(?string $filename){
        if(null!=$filename){
            if(file_exists('/'.$filename)){
                unlink('/'.$filename);
            }
        }
    }


    /**
     * Retourne le répertoire cible pour les téléchargements.
     *
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
