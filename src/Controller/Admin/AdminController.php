<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Entity\Property;
use App\Entity\PropertyType;
use App\Entity\Review;
use App\Entity\Service;
use App\Entity\User;
use App\Form\Admin\EquipmentType;
use App\Form\Admin\PropertyType as PropertyTypeForm;
use App\Form\Admin\PropertyTypeType;
use App\Form\Admin\ReviewType as AdminReviewType;
use App\Form\Admin\ServiceType;
use App\Form\Admin\UserType;
use App\Repository\EquipmentRepository;
use App\Repository\PropertyRepository;
use App\Repository\PropertyTypeRepository;
use App\Repository\ReviewRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PropertyRepository $propertyRepository;
    private UserRepository $userRepository;
    private ReviewRepository $reviewRepository;
    private PropertyTypeRepository $propertyTypeRepository;
    private EquipmentRepository $equipmentRepository;
    private ServiceRepository $serviceRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PropertyRepository $propertyRepository,
        UserRepository $userRepository,
        ReviewRepository $reviewRepository,
        PropertyTypeRepository $propertyTypeRepository,
        EquipmentRepository $equipmentRepository,
        ServiceRepository $serviceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->propertyRepository = $propertyRepository;
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
        $this->propertyTypeRepository = $propertyTypeRepository;
        $this->equipmentRepository = $equipmentRepository;
        $this->serviceRepository = $serviceRepository;
    }

    #[Route('/', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function getPropertyCount(): Response
    {
        return new Response($this->propertyRepository->count([]));
    }

    public function getUserCount(): Response
    {
        return new Response($this->userRepository->count([]));
    }

    public function getReviewCount(): Response
    {
        return new Response($this->reviewRepository->count([]));
    }

    public function getPropertyTypeCount(): Response
    {
        return new Response($this->propertyTypeRepository->count([]));
    }

    public function getEquipmentCount(): Response
    {
        return new Response($this->equipmentRepository->count([]));
    }

    public function getServiceCount(): Response
    {
        return new Response($this->serviceRepository->count([]));
    }

    public function getLatestProperties(): Response
    {
        $properties = $this->propertyRepository->findBy([], ['id' => 'DESC'], 5);
        return $this->render('admin/_latest_properties.html.twig', [
            'properties' => $properties,
        ]);
    }
    
    // Property Type CRUD
    #[Route('/property-types', name: 'app_admin_property_types')]
    public function propertyTypes(PropertyTypeRepository $repository): Response
    {
        return $this->render('admin/property_type/index.html.twig', [
            'propertyTypes' => $repository->findAll(),
        ]);
    }
    
    #[Route('/property-types/new', name: 'app_admin_property_types_new')]
    public function newPropertyType(Request $request, EntityManagerInterface $entityManager): Response
    {
        $propertyType = new PropertyType();
        $form = $this->createForm(PropertyTypeType::class, $propertyType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($propertyType);
            $entityManager->flush();
            
            $this->addFlash('success', 'Property type created successfully.');
            return $this->redirectToRoute('app_admin_property_types');
        }
        
        return $this->render('admin/property_type/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/property-types/{id}/edit', name: 'app_admin_property_types_edit')]
    public function editPropertyType(Request $request, PropertyType $propertyType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PropertyTypeType::class, $propertyType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Property type updated successfully.');
            return $this->redirectToRoute('app_admin_property_types');
        }
        
        return $this->render('admin/property_type/edit.html.twig', [
            'propertyType' => $propertyType,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/property-types/{id}/delete', name: 'app_admin_property_types_delete')]
    public function deletePropertyType(Request $request, PropertyType $propertyType, EntityManagerInterface $entityManager): Response
    {
        if ($propertyType->getProperties()->count() > 0) {
            $this->addFlash('error', 'Cannot delete a property type that has properties assigned to it.');
            return $this->redirectToRoute('app_admin_property_types');
        }
        
        $entityManager->remove($propertyType);
        $entityManager->flush();
        
        $this->addFlash('success', 'Property type deleted successfully.');
        return $this->redirectToRoute('app_admin_property_types');
    }
    
    // Equipment CRUD
    #[Route('/equipment', name: 'app_admin_equipment')]
    public function equipment(EquipmentRepository $repository): Response
    {
        return $this->render('admin/equipment/index.html.twig', [
            'equipment' => $repository->findAll(),
        ]);
    }
    
    #[Route('/equipment/new', name: 'app_admin_equipment_new')]
    public function newEquipment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Equipment created successfully.');
            return $this->redirectToRoute('app_admin_equipment');
        }
        
        return $this->render('admin/equipment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/equipment/{id}/edit', name: 'app_admin_equipment_edit')]
    public function editEquipment(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Equipment updated successfully.');
            return $this->redirectToRoute('app_admin_equipment');
        }
        
        return $this->render('admin/equipment/edit.html.twig', [
            'equipment' => $equipment,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/equipment/{id}/delete', name: 'app_admin_equipment_delete')]
    public function deleteEquipment(Request $request, Equipment $equipment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($equipment);
        $entityManager->flush();
        
        $this->addFlash('success', 'Equipment deleted successfully.');
        return $this->redirectToRoute('app_admin_equipment');
    }
    
    // Service CRUD
    #[Route('/services', name: 'app_admin_services')]
    public function services(ServiceRepository $repository): Response
    {
        return $this->render('admin/service/index.html.twig', [
            'services' => $repository->findAll(),
        ]);
    }
    
    #[Route('/services/new', name: 'app_admin_services_new')]
    public function newService(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($service);
            $entityManager->flush();
            
            $this->addFlash('success', 'Service created successfully.');
            return $this->redirectToRoute('app_admin_services');
        }
        
        return $this->render('admin/service/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/services/{id}/edit', name: 'app_admin_services_edit')]
    public function editService(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Service updated successfully.');
            return $this->redirectToRoute('app_admin_services');
        }
        
        return $this->render('admin/service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/services/{id}/delete', name: 'app_admin_services_delete')]
    public function deleteService(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($service);
        $entityManager->flush();
        
        $this->addFlash('success', 'Service deleted successfully.');
        return $this->redirectToRoute('app_admin_services');
    }
    
    // User CRUD
    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $repository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $repository->findAll(),
        ]);
    }
    
    #[Route('/users/{id}/edit', name: 'app_admin_users_edit')]
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'User updated successfully.');
            return $this->redirectToRoute('app_admin_users');
        }
        
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/users/{id}/delete', name: 'app_admin_users_delete')]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'You cannot delete your own account.');
            return $this->redirectToRoute('app_admin_users');
        }
        
        $entityManager->remove($user);
        $entityManager->flush();
        
        $this->addFlash('success', 'User deleted successfully.');
        return $this->redirectToRoute('app_admin_users');
    }
    
    // Property CRUD
    #[Route('/properties', name: 'app_admin_properties')]
    public function properties(Request $request, PropertyRepository $repository): Response
    {
        $searchTerm = $request->query->get('search');
        
        if ($searchTerm) {
            $properties = $repository->findByFilters(['search' => $searchTerm]);
        } else {
            $properties = $repository->findAll();
        }
        
        return $this->render('admin/property/index.html.twig', [
            'properties' => $properties,
            'searchTerm' => $searchTerm,
        ]);
    }
    
    #[Route('/properties/new', name: 'app_admin_properties_new')]
    public function newProperty(Request $request, EntityManagerInterface $entityManager): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyTypeForm::class, $property);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($property);
            $entityManager->flush();
            
            $this->addFlash('success', 'Property created successfully.');
            return $this->redirectToRoute('app_admin_properties');
        }
        
        return $this->render('admin/property/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/properties/{id}/edit', name: 'app_admin_properties_edit')]
    public function editProperty(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PropertyTypeForm::class, $property);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Property updated successfully.');
            return $this->redirectToRoute('app_admin_properties');
        }
        
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/properties/{id}/delete', name: 'app_admin_properties_delete')]
    public function deleteProperty(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        // Check for bookings
        if ($property->getBookings()->count() > 0) {
            $this->addFlash('error', 'Cannot delete a property that has bookings.');
            return $this->redirectToRoute('app_admin_properties');
        }
        
        $entityManager->remove($property);
        $entityManager->flush();
        
        $this->addFlash('success', 'Property deleted successfully.');
        return $this->redirectToRoute('app_admin_properties');
    }
    
    // Review CRUD
    #[Route('/reviews', name: 'app_admin_reviews')]
    public function reviews(ReviewRepository $repository): Response
    {
        return $this->render('admin/review/index.html.twig', [
            'reviews' => $repository->findAll(),
        ]);
    }
    
    #[Route('/reviews/{id}/edit', name: 'app_admin_reviews_edit')]
    public function editReview(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminReviewType::class, $review);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Review updated successfully.');
            return $this->redirectToRoute('app_admin_reviews');
        }
        
        return $this->render('admin/review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/reviews/{id}/delete', name: 'app_admin_reviews_delete')]
    public function deleteReview(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($review);
        $entityManager->flush();
        
        $this->addFlash('success', 'Review deleted successfully.');
        return $this->redirectToRoute('app_admin_reviews');
    }
}