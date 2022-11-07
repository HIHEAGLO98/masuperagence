<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;

use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */

    private $repository;


    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(PropertyRepository $repository)
    {
        $this->repository = $repository;
        //$this->manager = $manager;
    }


    /**
     * @Route("/biens", name="property.index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request  $request):Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuerySearch($search),
            $request->query->getInt('page', 1),
            12);

        return $this->render('property/index.html.twig',
        [
            'current_menu'=>'properties',
            'properties'=>$properties,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={ "slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @param string $slug
     * @return Response
     */
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification):Response
    {
        if($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show',[
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre message a été bien envoyé');

            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
               'slug' => $property->getSlug()
            ]);
           // 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
        }

        return $this->render('property/show.html.twig', [
            'property'     => $property,
            'current_menu' => 'properties',
            'form'         => $form->createView(),
        ]);

    }

}