<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ReviewController extends AbstractController
{
    #[Route('/booking/{id}/review', name: 'app_review_new')]
    public function new(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Security check - users can only review their own bookings
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot review this booking');
        }
        
        // Check if the stay has been completed
        if (!$booking->isCompletedStay()) {
            $this->addFlash('error', 'You can only review a property after your stay has ended.');
            return $this->redirectToRoute('app_user_bookings');
        }
        
        // Check if user has already reviewed this booking
        if ($booking->getHasReviewed()) {
            $this->addFlash('error', 'You have already reviewed this stay.');
            return $this->redirectToRoute('app_user_bookings');
        }
        
        $review = new Review();
        $review->setUser($this->getUser());
        $review->setProperty($booking->getProperty());
        
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            
            // Mark the booking as reviewed
            $booking->setHasReviewed(true);
            $entityManager->persist($booking);
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Your review has been submitted. Thank you!');
            return $this->redirectToRoute('app_property_show', ['id' => $booking->getProperty()->getId()]);
        }
        
        return $this->render('review/new.html.twig', [
            'booking' => $booking,
            'property' => $booking->getProperty(),
            'form' => $form->createView(),
        ]);
    }
}