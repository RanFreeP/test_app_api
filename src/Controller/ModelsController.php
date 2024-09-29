<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Entity\Models;
use App\Service\PaginateService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/api/models', name: "api_models")]
class ModelsController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }


    #[Route('/', name: 'models_index', methods: ['GET'])]
    public function index(PaginateService $paginateService, Request $request): JsonResponse
    {

        try {
            if ($request->get('page') && $request->get('page') > 0 && is_numeric($request->get('page'))) {
                $page = $request->get('page');
            } else {
                $page = 1;
            }

            $data = [];

            $models = $this->doctrine->getRepository(Models::class);

            if($request->get('brand')) {
                $brandRId = $request->get('brand');
                $paginateData = $paginateService->getPaginated(null, $page, 50,'model', $models->findOneByIdBrand($brandRId));
            } else {
                $paginateData = $paginateService->getPaginated($models, $page, 50);
            }

            foreach ($paginateData['data'] as $item) {
                $brand = $item->getBrand();
                $brandId = $brand->getId();
                $brandName = $brand->getName();

                $brandArray = [
                    'id' => $brandId,
                    'name' => $brandName,
                ];

                $data[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'brand' => $brandArray,
                    'attributes' => $item->getAttributes(),
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
            'pagesCount'=> $paginateData['pagesCount']
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'models_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        try {
            $modelRequest = json_decode($request->getContent());
            $entityManager = $this->doctrine->getManager();
            $brandId = $this->doctrine->getRepository(Brands::class)->find($modelRequest->brandId);

            if (!$brandId) {
                return $this->json([
                    'message' => 'Данный бренд не найден',
                ], 400);
            }

            $brands = $this->doctrine->getRepository(Models::class)->findAll();

            foreach ($brands as $employee) {

                if (stripos($employee->getname(), $modelRequest->name) !== false) {
                    return $this->json([
                        'message' => 'Данная модель уже есть в базе',
                    ], 400);
                }

            }

            $modelAttributes = (array)$modelRequest->attributes;

            $model = new Models();
            $model->setName($modelRequest->name);
            $model->setBrand($brandId);
            $model->setAttributes($modelAttributes);
            $entityManager->persist($model);
            $entityManager->flush();

            return $this->json([
                'body' => 'Марка "' . $model->getName() . '" упешно добавлена',
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[IsGranted('ROLE_MANAGER')]
    #[Route('/{id}', name: 'models_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $model = $this->doctrine->getRepository(Models::class)->find($id);
            if (!$model) {
                return $this->json([
                    'message' => 'Марка по id' . $id . ' не найдена',
                ], 404);
            }
            $brand = $model->getBrand();

            $brandId = $brand->getId();
            $brandName = $brand->getName();
            $brand = [
                'id' => $brandId,
                'name' => $brandName,
            ];

            $data = [
                'id' => $model->getId(),
                'name' => $model->getname(),
                'brand' => $brand,
                'attributes' => $model->getAttributes(),
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
    #[Route('/{id}', name: 'models_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        try {

            $entityManager = $this->doctrine->getManager();
            $model = $entityManager->getRepository(Models::class)->find($id);

            if (!$model) {
                return $this->json([
                    'message' => 'Марка c id №' . $id . ' не найдена',
                ], 404);
            }

            $modelRequest = json_decode($request->getContent());
            $brandId = $this->doctrine->getRepository(Brands::class)->find($modelRequest->brandId);
            $modelAttributes = (array)$modelRequest->attributes;


            $model->setName($modelRequest->name);
            $model->setBrand($brandId);
            $model->setAttributes($modelAttributes);
            $entityManager->flush();

            $brand = $model->getBrand();

            $brandId = $brand->getId();
            $brandName = $brand->getName();
            $brand = [
                'id' => $brandId,
                'name' => $brandName,
            ];

            $data = [
                'id' => $model->getId(),
                'name' => $model->getname(),
                'brand' => $brand,
                'attributes' => $model->getAttributes(),
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
    #[Route('/{id}', name: 'models_deleted', methods: ['DELETE'])]
    public function destroy(int $id): JsonResponse
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $model = $entityManager->getRepository(Models::class)->find($id);

            if (!$model) {
                return $this->json([
                    'message' => 'Модель c id №' . $id . ' не найдена',
                ], 404);
            }

            $entityManager->remove($model);
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
