<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertyType;
use App\Entity\Equipment;
use App\Entity\Service;
use App\Form\PropertyFilterType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PropertyController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request, 
        PropertyRepository $propertyRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $filters = [
            'city' => $request->query->get('city'),
            'minPrice' => $request->query->get('minPrice'),
            'maxPrice' => $request->query->get('maxPrice'),
            'propertyType' => $request->query->get('propertyType'),
            'equipment' => $request->query->all('equipment'),
            'services' => $request->query->all('services'),
            'search' => $request->query->get('search'),
        ];

        $propertyTypes = $entityManager->getRepository(PropertyType::class)->findAll();
        $equipments = $entityManager->getRepository(Equipment::class)->findAll();
        $services = $entityManager->getRepository(Service::class)->findAll();
        
        $properties = $propertyRepository->findByFilters($filters);
        
        $filterForm = $this->createForm(PropertyFilterType::class, null, [
            'propertyTypes' => $propertyTypes,
            'equipments' => $equipments,
            'services' => $services,
        ]);
        
        $filterForm->handleRequest($request);
        
        return $this->render('property/index.html.twig', [
            'properties' => $properties,
            'filterForm' => $filterForm->createView(),
            'filters' => $filters,
        ]);
    }

    #[Route('/property/{id}', name: 'app_property_show')]
    public function show(Property $property): Response
    {
        return $this->render('property/show.html.twig', [
            'property' => $property,
        ]);
    }

    #[Route('/property/{id}/toggle-favorite', name: 'app_property_toggle_favorite')]
    #[IsGranted('ROLE_USER')]
    public function toggleFavorite(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if ($user->getFavorites()->contains($property)) {
            $user->removeFavorite($property);
            $status = 'removed';
        } else {
            $user->addFavorite($property);
            $status = 'added';
        }
        
        $entityManager->persist($user);
        $entityManager->flush();
        
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'status' => $status,
                'isFavorite' => $user->getFavorites()->contains($property),
            ]);
        }
        
        return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
    }
}