<?php

namespace App\Controller;

use App\Entity\Brands;
use App\Entity\Models;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AddDataController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/api/addData', name: 'app_add_data')]
    public function index(Request $request): JsonResponse
    {
        $dataRequest = json_decode($request->getContent());
        $entityManager = $this->doctrine->getManager();

        try {
            foreach ($dataRequest as $brandName => $models) {
                $brandNew = new Brands();
                $brandNew->setName($brandName);
                $entityManager->persist($brandNew);

                foreach ($models as $model) {
                    $modelNew = new Models();
                    $modelNew->setName($model->name);
                    $modelNew->setAttributes((array)$model->attributes);
                    $modelNew->setBrand($brandNew);
                    $entityManager->persist($modelNew);
                }
            }
            $entityManager->flush();

            return $this->json([
                'message' => 'ok',
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }


//        try {
//            foreach ($dataRequest->brands as $brand) {
//
//                $brandNew = new Brands();
//                //$brandNew->setId($brand->id);
//                $brandNew->setName($brand->name);
//                $entityManager->persist($brandNew);
//            }
////            $entityManager->flush();
//
////            foreach ($dataRequest->models as $model) {
////                $brandId = $this->doctrine->getRepository(Brands::class)->find($model->brand_id);
////                $modelAttributes = (array) $model->attributes;
////
////                $modelNew = new Models();
////                $modelNew->setName($model->name);
////                $modelNew->setBrand($brandId);
////                $modelNew->setAttributes($modelAttributes);
////                $entityManager->persist($modelNew);
////            }
////
////            $entityManager->flush();
//
//            return $this->json([
//                'message' => 'ok',
//            ]);
//        } catch (\Throwable $e) {
//            return $this->json([
//                'error' => $e->getMessage(),
//            ]);
//        }


    }
}
