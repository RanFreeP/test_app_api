<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Service\PaginateService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/brands', name: "api_brands")]
class BrandsController extends AbstractController
{

    private ManagerRegistry $doctrine;
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }


    #[Route('/', name: 'brands_index', methods: ['GET'])]
    public function index(PaginateService $paginateService, Request $request): JsonResponse
    {

        try {
            if ($request->get('page') && $request->get('page') > 0 && is_numeric($request->get('page'))) {
                $page = $request->get('page');
            } else {
                $page = 1;
            }

            $data = [];
            $brandsRep = $this->doctrine->getRepository(Brands::class);

            if ($request->get('all')) {
                $brands = $brandsRep->findAll();
                foreach ($brands as $item) {
                    $data[] = [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                    ];
                }
                return $this->json([
                    'body' => $data,
                ]);
            }



            $paginateData = $paginateService->getPaginated($brandsRep, $page);

            foreach ($paginateData['data'] as $item) {
                $data[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }


        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }

        return $this->json([
            'body' => $data,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'brands_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        try {
            $brandRequest = json_decode($request->getContent());
            $entityManager = $this->doctrine->getManager();

            $brands = $this->doctrine->getRepository(Brands::class)->findAll();

            foreach ($brands as $employee) {

                if (stripos($employee->getname(), $brandRequest->name) !== false) {
                    return $this->json([
                        'message' => 'Данная модель уже есть в базе',
                    ], 400);
                }

            }

            $brand = new Brands();
            $brand->setName($brandRequest->name);
            $entityManager->persist($brand);
            $entityManager->flush();

            return $this->json([
                'body' => 'Марка "' . $brand->getName() . '" упешно добавлена',
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[IsGranted('ROLE_MANAGER')]
    #[Route('/{id}', name: 'brands_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $brand = $this->doctrine->getRepository(Brands::class)->find($id);
            if (!$brand) {
                return $this->json([
                    'message' => 'Марка по id' . $id . ' не найдена',
                ], 404);
            }
            $data = [
                'id' => $brand->getId(),
                'fullname' => $brand->getname(),
            ];

            return $this->json([
                'body' => $data,
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[IsGranted('ROLE_MANAGER')]
    #[Route('/{id}', name: 'brands_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $brand = $entityManager->getRepository(Brands::class)->find($id);

            if (!$brand) {
                return $this->json([
                    'message' => 'Марка c id №' . $id . ' не найдена',
                ], 404);
            }

            $brandRequest = json_decode($request->getContent());

            $brand->setName($brandRequest->name);
            $entityManager->flush();

            $data = [
                'id' => $brand->getId(),
                'name' => $brand->getName(),
            ];

            return $this->json([
                'body' => $data,
            ]);

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'brands_deleted', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $brand = $entityManager->getRepository(Brands::class)->find($id);

            if (!$brand) {
                return $this->json([
                    'message' => 'Марка c id №' . $id . ' не найдена',
                ], 404);
            }

            $entityManager->remove($brand);
            $entityManager->flush();

            return $this->json([
                'message' => 'Запись c id №' . $id . ' успешно удалена',
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
