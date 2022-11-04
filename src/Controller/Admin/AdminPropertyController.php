<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
    /**
     * @return PropertyRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request  $request):Response
    {
       // $properties = $this->repository->findAll();

        $properties = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
            12);

        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    /**
     * @Route("admin/property", name="admin.property.new")
     */

    public function createNew(Request $request)
    {
        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien créer avec succès');

            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/create.html.twig',[
            'property' => $property,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     */
    public function  edit(Property  $property, Request $request)
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*if($property->getImageFile() instanceof UploadedFile){
                $cacheManager->remove($helper->asset($property, 'imageFile'));
            }*/
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig',[
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/property/delete/{id}", name="admin.property.delete", methods="DELETE")
     * @param Property $property
     * @return Response
     */
    public function deleteProperty(Property $property):Response
    {
        $this->em->remove($property);
        $this->em->flush();
        $this->addFlash('success', 'La propriété a été bien supprimée');
        return $this->redirectToRoute('admin.property.index');
    }

}