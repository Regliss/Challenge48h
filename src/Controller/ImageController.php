<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
	* @Route("/image", name="image")
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
			$image -> setRegisterDate(new \DateTime('now'));
			$image -> setUser('1');
			//image -> setUser($this -> getUser());
			$image -> uploadFile();
			$manager -> flush(); // Exécute toutes les requêtes en attentes.
			$this -> addFlash('success', 'L image N°' . $image -> getId()  . ' a bien été enregistré');
			return $this -> redirectToRoute('admin_image');
		}
		return $this -> render('admin/image_form.html.twig', [
			'imageForm' => $form -> createView()


		]);


	}
}
