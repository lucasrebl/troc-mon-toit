<?php

namespace App\Controller;

use App\Form\UserProfileType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your profile has been updated.');
            return $this->redirectToRoute('app_user_profile');
        }
        
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/bookings', name: 'app_user_bookings')]
    public function bookings(BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->findByUser($this->getUser());
        $completedBookingsToReview = $bookingRepository->findCompletedStaysWithoutReview($this->getUser());
        
        return $this->render('user/bookings.html.twig', [
            'bookings' => $bookings,
            'completedBookingsToReview' => $completedBookingsToReview,
        ]);
    }
    
    #[Route('/favorites', name: 'app_user_favorites')]
    public function favorites(): Response
    {
        $favorites = $this->getUser()->getFavorites();
        
        return $this->render('user/favorites.html.twig', [
            'favorites' => $favorites,
        ]);
    }
    
    #[Route('/reviews', name: 'app_user_reviews')]
    public function reviews(): Response
    {
        $reviews = $this->getUser()->getReviews();
        
        return $this->render('user/reviews.html.twig', [
            'reviews' => $reviews,
        ]);
    }
}