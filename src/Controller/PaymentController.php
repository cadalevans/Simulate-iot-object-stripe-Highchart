<?php

namespace App\Controller;

use App\Entity\User;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{

    private $doctrine;

    #[Route('/payment', name: 'payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

//Sometimes due to depency we encoutered we can use this command to install stripe
// i have encountered error but this work : composer require stripe/stripe-php --ignore-platform-req=ext-cassandra

    #[Route('/checkout', name: 'checkout')]
    public function checkout($stripeSK): Response
    {
        Stripe::setApiKey($stripeSK);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'eur',
                        'product_data' => [
                            'name' => 'Enrollment',
                        ],
                        'unit_amount'  => 7000,
                    ],
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }


    #[Route('/success', name: 'success_url')]
    public function checkoutSuccess(Request $request): Response
    {
        // Retrieve the payment intent and other relevant data from the request
        $paymentIntentId = $request->query->get('payment_intent');
        $customerId = $request->query->get('customer');

        $paymentIntent = $this->retrievePaymentIntent($paymentIntentId);

        // Check if the payment was successful
        if ($paymentIntent->status === 'succeeded') {
            // Get the current user
            $currentUser = $this->getUser();

            if ($currentUser instanceof User) {
                // Update the user's payment status and store payment-related information in the database
                $currentUser->setIsPay(true);
                $currentUser->setPaymentDate(new \DateTime());
                $currentUser->setPaymentIntent($paymentIntent);
                // Persist the changes to the database
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($currentUser);
                $entityManager->flush();

                // Return a success response
                return new Response('Payment successful');
            }
        }

        // Return a failure response if the payment was not successful or if the user is not authenticated
        return new Response('Payment unsuccessful');
    }
    private function retrievePaymentIntent(?string $paymentIntentId, string $stripeSK): ?PaymentIntent
    {
        if ($paymentIntentId === null) {
            return null; // Return null if paymentIntentId is null
        }

        // Set the Stripe API key
        Stripe::setApiKey($stripeSK);

        try {
            // Retrieve the payment intent from Stripe
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return $paymentIntent;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any errors that occur during the retrieval
            // For example, log the error or return null
            return null;
        }
    }
    #[Route('/cancel', name: 'cancel_url')]
    public function checkoutCancel(): Response
    {
        // Handle the case where the user cancels the payment
        return new Response('Payment canceled');
    }
}