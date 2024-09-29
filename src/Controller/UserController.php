<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    #[Route('/api/userMe', name: 'user_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $user = $this->getUser();

            return $this->json([
                'body' => [
                    'roles' => $user->getRoles(),
                    'username' => $user->getUsername(),
                ]
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }


    }
}
