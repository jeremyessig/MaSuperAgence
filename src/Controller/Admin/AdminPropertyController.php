<?php
namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController{

    private $repository;
    private $em;

    public function __construct(PropertyRepository $repository, ManagerRegistry $doctrin)
    {
        $this->repository = $repository;
        // Attention: changement par rapport à Grafikart, il faut appeler le Manager ainsi pour flush les données
        $this->em = $doctrin->getManager();
    }

    /**
     * Undocumented function
     * 
     * @Route("/admin", name="admin.property.index")
     * @return Response
     */
    public function index(): Response
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));

    }

    /**
     * Créer un nouveau bien
     *
     * @Route("/admin/property/create", name="admin.property.new")
     * @return Response
     */
    public function new(Request $request): Response
    {
        // Créer un nouveau bien
        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property);

        //Gère la requête en POST et sauvegarde en base de données
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // On persiste la nouvelle property en base de données
            $this->em->persist($property);
            // On injecte la property en base de donnée
            $this->em->flush();
            $this->addFlash('success', 'Bien créé avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView() // Créé un objet de type vue pour l'envoyer à la vue
        ]);

    }

    /**
     * 
     * @Route("/admin/property/edit/{id}", name="admin.property.edit", methods="POST|GET")
     * @return Response
     */
    public function edit(Property $property, Request $request): Response
    {
        // On récupère le bien en question dans les paramètres de la fonction

        $form = $this->createForm(PropertyType::class, $property);

        //Gère la requête en POST et sauvegarde en base de données
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView() // Créé un objet de type vue pour l'envoyer à la vue
        ]);
    }

    /**
     * Undocumented function
     *
     * @Route("/admin/property/delete/{id}", name="admin.property.delete", methods="POST")
     * @return Response
     */
    public function delete(Property $property, Request $request): Response
    {
        // Token pour éviter les injections et failles de sécurité
        // On récupère le CsrfToken de l'objet AbstractController -> on récupère le nom du token et le token
        if($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))){
            $this->em->remove($property);
            $this->em->flush($property);
            $this->addFlash('success', 'Bien supprimé avec succès');
        }

        return $this->redirectToRoute('admin.property.index');
    }

}