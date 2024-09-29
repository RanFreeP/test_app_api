<?php

namespace App\DataFixtures;

use App\Entity\Brands;
use App\Entity\Models;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        $brandArray = [
            'Acura',
            'Asia',
            'Audi',
            'Bentley',
            'BMW',
            'Brilliance',
            'Bugatti',
            'Buick',
            'BYD',
            'Cadillac',
            'Changan',
            'Chery',
            'Chevrolet',
            'Chrysler',
            'Citroen',
            'Dacia',
            'Daewoo',
            'Daihatsu',
            'Datsun',
            'DeLorean',
            'Derways',
            'Dodge',
            'Dongfeng',
            'FAW',
            'Ferrari',
            'Fiat',
            'Ford',
            'Foton',
            'Freightliner',
            'Geely',
            'Genesis',
            'GMC',
            'Hafei',
            'Haima',
            'Haval',
            'Hawtai',
            'Hino',
            'Honda',
            'Hummer',
            'Hyundai',
            'Infiniti',
            'Isuzu',
            'JAC',
            'Jaguar',
            'Jeep',
            'Kia',
            'Koenigsegg',
            'Lamborghini',
            'Lancia',
            'Lexus',
            'Lifan',
            'Lincoln',
            'Lotus',
            'Luxgen',
            'Marussia',
            'Maserati',
            'Maybach',
            'Mazda',
            'Mercury',
            'MINI',
            'Mitsubishi',
            'Mitsuoka',
            'Nissan',
            'Oldsmobile',
            'Opel',
            'Pagani',
            'Peugeot',
            'Plymouth',
            'Pontiac',
            'Porsche',
            'Proton',
            'Ravon',
            'Renault',
            'Rover',
            'Saab',
            'Saturn',
            'Scion',
            'SEAT',
            'Skoda',
            'Smart',
            'SsangYong',
            'Subaru',
            'Suzuki',
            'Tesla',
            'Tianye',
            'Toyota',
            'Volkswagen',
            'Volvo',
            'Vortex',
            'Zotye',
            'ZX',
            'Аурус',
            'ГАЗ',
            'ЗАЗ',
            'ИЖ',
            'ВАЗ',
            'ЛуАЗ',
            'Москвич',
            'ТагАЗ',
            'УАЗ',
        ];
        $modelArray = [
            'Runner',
            'Allex',
            'Allion',
            'Alphard',
            'Altezza',
            'Aqua',
            'Aristo',
            'Aurion',
            'Auris',
            'Avalon',
            'Avanza',
            'Avensis',
            'Aygo',
            'bB',
            'Belta',
            'Blade',
            'Blizzard',
            'Brevis',
            'bZ3',
            'bZ4X',
            'Caldina',
            'Cami',
            'Camry',
            'Carina',
            'Cavalier',
            'Celica',
            'Celsior',
            'Century',
            'Chaser',
            'Classic',
            'Comfort',
            'COMS',
            'Corolla',
            'iX',
            'iX1',
            'iX2',
            'iX3',
            'M2',
            'M3',
            'M4',
            'M5',
            'M6',
            'M8',
            'Neue Klasse',
            'X1',
            'X2',
            'X3',
            'X4',
            'X5',
            'X6',
            'X7',
            'XM',
            'Z1',
            'Z3',
            'Z4',
            'Z8',
        ];

        foreach ($brandArray as $brand) {
            $brandNew = new Brands();
            $brandNew->setName($brand);
            $manager->persist($brandNew);
        }
        $manager->flush();

        $brandRepository = $manager->getRepository(Brands::class);
        $id_limits = $brandRepository->createQueryBuilder('entity')
            ->select('MIN(entity.id)', 'MAX(entity.id)')
            ->getQuery()
            ->getOneOrNullResult();


        $attributesFake = [
            [
                "steering_position"=> "Левый руль",
                "color"=> "Черный"
            ],
            [
                "steering_position"=> "Правый руль",
                "color"=> "Красный",
            ]
        ];



        foreach ($modelArray as $model) {
            $randomKey = rand(0,1);
            $random_possible_id = rand($id_limits[1], $id_limits[2]);
            $randomBrand = $brandRepository->find($random_possible_id);

            $modelNew = new Models();
            $modelNew->setBrand($randomBrand);
            $modelNew->setName($model);
            $modelNew->setAttributes($attributesFake[$randomKey]);
            $manager->persist($modelNew);
        }
        $manager->flush();

        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword('$2y$13$qkenW0SfC.7OTDHG/JhguO73TT6EsL4tFIa0KoBiyU1/xvnKk0LH6');
        $manager->persist($userAdmin);
        $manager->flush();

        $userManager = new User();
        $userManager->setUsername('manager');
        $userManager->setRoles(["ROLE_MANAGER"]);
        $userManager->setPassword('$2y$13$qkenW0SfC.7OTDHG/JhguO73TT6EsL4tFIa0KoBiyU1/xvnKk0LH6');
        $manager->persist($userManager);
        $manager->flush();

    }
}
