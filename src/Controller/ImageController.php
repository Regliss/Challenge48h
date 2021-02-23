<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Image;
use App\Entity\User;

use App\Form\ImageType;

class ImageController extends AbstractController
{
    /**
	* @Route("/admin/image", name="admin_image")
	*
	*/
	public function adminImage(){
		//1 : Récupérer tous les images
		$repo = $this -> getDoctrine() -> getRepository(Image::class);
		$images = $repo -> findAll();

		//2 : Afficher dans une vue
		return $this -> render('image/image_list.html.twig', ['images' => $images]);
	}

    /**
	* @Route("/image", name="image_add")
	*
	*/
	public function imageAdd(Request $request){
		$manager =$this -> getDoctrine() -> getManager();
		$image = new Image; // objet vide de l'entity image.

		// formulaire...
		$form = $this -> createForm(ImageType::class, $image);

		// traitement des infos du formulaire
		$form -> handleRequest($request); // lier définitivement le $image aux infos du formulaire (récupère les donnée saisies en $image)

		if($form -> isSubmitted() && $form -> isValid()){
			$manager -> persist($image); // Enregistre le image dans le system
			// $image -> setUser('1');
			$image -> uploadFile();
			$manager -> flush(); // Exécute toutes les requêtes en attentes.
			$this -> addFlash('success', 'L image N°' . $image -> getId()  . ' a bien été enregistré');
			return $this -> redirectToRoute('admin_image');
		}
		return $this -> render('image/image_form.html.twig', [
			'imageForm' => $form -> createView()


		]);


	}

	/**
	* @Route("/admin/image/delete/{id}", name="admin_image_delete")
	*
	*/
	public function adminImageDelete($id){
		
		//1 : Manager
		$manager = $this -> getDoctrine() -> getManager();
		//2 : Récupérer l'entrée à suppr
		$image = $manager -> find(Image::class, $id);
		//3 : Suppr
		$manager -> remove($image);
		$manager -> flush();
		//4 : Message
		$this -> addFlash('success', 'L image N°' . $id . ' a bien été supprimé !');
		//5 : Redirection
		return $this -> redirectToRoute('admin_image');
	}

	/**
	* @Route("/admin/image/update/{id}", name="admin_image_update")
	*
	*/
	public function adminImageUpdate($id, Request $request){
		//1 : Récupérer le manager
		$manager = $this -> getDoctrine() -> getManager();
		//2 : Récupérer l'objet
		$image = $manager -> find(Image::class, $id);
		$form = $this -> createForm(ImageType::class, $image);
		//Notre objet hydrate le formulaire
		$form -> handleRequest($request);
		
		if($form -> isSubmitted() && $form -> isValid()){
			//3 : Modifier (Formulaire)
		//$image -> setTitle('Nouveax titre'); //test FORMULAIRE...
		$manager -> persist($image);
		if($image -> getFile()){
			$image -> removeFile();
			$image -> uploadFile();
		}

		$manager -> flush();
		//4 : Message
		$this -> addFlash('success', 'L image N°' . $id . ' a bien été modifié !');
		return $this -> redirectToRoute('admin_image');
		}
		//5 : Vue 
		return $this -> render('image/image_form.html.twig', ['imageForm' => $form -> createView()]);
		// test : localhost:8000/admin/image/update/id

		}
}
