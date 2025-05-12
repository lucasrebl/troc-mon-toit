<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Property;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class BookingController extends AbstractController
{
    #[Route('/property/{id}/book', name: 'app_booking_new')]
    public function new(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        $booking = new Booking();
        $booking->setProperty($property);
        $booking->setUser($this->getUser());
        
        $form = $this->createForm(BookingType::class, $booking, [
            'property' => $property,
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Calculate total price
            $numberOfNights = $booking->getNumberOfNights();
            $totalPrice = $property->getPricePerNight() * $numberOfNights;
            $booking->setTotalPrice($totalPrice);
            
            // Check if property is available for these dates
            if (!$property->isAvailable($booking->getStartDate(), $booking->getEndDate())) {
                $this->addFlash('error', 'The property is not available for the selected dates.');
                return $this->redirectToRoute('app_booking_new', ['id' => $property->getId()]);
            }
            
            $entityManager->persist($booking);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your booking has been confirmed!');
            return $this->redirectToRoute('app_user_bookings');
        }
        
        return $this->render('booking/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/booking/{id}', name: 'app_booking_show')]
    public function show(Booking $booking): Response
    {
        // Security check - users can only see their own bookings
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot view this booking');
        }
        
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/booking/{id}/cancel', name: 'app_booking_cancel')]
    public function cancel(Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Security check - users can only cancel their own bookings
        if ($booking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You cannot cancel this booking');
        }
        
        // Prevent cancellation if the booking has already started
        if ($booking->getStartDate() <= new \DateTime()) {
            $this->addFlash('error', 'You cannot cancel a booking that has already started.');
            return $this->redirectToRoute('app_user_bookings');
        }
        
        $entityManager->remove($booking);
        $entityManager->flush();
        
        $this->addFlash('success', 'Your booking has been cancelled.');
        return $this->redirectToRoute('app_user_bookings');
    }
}