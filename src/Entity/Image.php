<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    private $file;//Correspond au fichier envoyé dans le formulaire(pas besoin d'etre mappé)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getFile(){
        return $this-> file;
    }

    public function setFile(UploadedFile $file){
        $this -> file = $file;
        return $this;
    }

    public function uploadFile(){
        $name = $this -> file -> getClientOriginalName();
        $newName = $this -> renameFile($name);
        //----
        $this -> image = $newName;
        //----
        //One enregistre la photo sur le serveur;
        $this -> file -> move($this -> dirPhoto(), $newName);
    }

    public function removeFile(){
        if(file_exists($this -> dirPhoto() . $this -> image) ){
            unlink($this -> dirPhoto() . $this -> image);
        }
    }

    public function renameFile($name){
        return 'photo_' . time() . '_' . rand(1, 99999) . '_' . $name;
        // chat.jpg
        // photo_158000000000_45239_chat.jpg
    }

    public function dirPhoto(){
        return __DIR__ . '/../../public/photo/';
    }
}
