<?php 

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController{

    private $repository;

    public function __construct(PropertyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 
     * @Route("/biens", name="property.index")
     * @return Response
     */
    
    public function index(ManagerRegistry $doctrine): Response
    {

        // $property = new Property();
        // $property->setTitle('Mon premier bien')
        //     ->setPrice(200000)
        //     ->setRooms(4)
        //     ->setBedrooms(3)
        //     ->setDescription('Une petite description')
        //     ->setSurface(60)
        //     ->setFloor(4)
        //     ->setHeat(1)
        //     ->setCity('Montpellier')
        //     ->setAddress('15 Boulevard Gambetta')
        //     ->setPostalCode('34000');

        // $entityManager = $doctrine->getManager();

        // // tell Doctrine you want to (eventually) save the Product (no queries yet)
        // $entityManager->persist($property);

        // // actually executes the queries (i.e. the INSERT query)
        // $entityManager->flush();
            
        $property = $this->repository->findAllVisible();
        dump($property);

        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }

    /**
     * Undocumented function
     *
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show(Property $property, string $slug): Response
    {
        // Vérifie que le slug en url correspond à ce qu'il y a en bdd
        if($property->getSlug() !== $slug ){
            //Si on est sur le bien et que le slug est modifié, on est renvoyé sur le bien avec le slug corrigé
            //Très important pour le référencement
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }
        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties'
        ]);
    }
}