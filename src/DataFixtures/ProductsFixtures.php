<?php

namespace App\DataFixtures;


use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Monolog\DateTimeImmutable;
use Faker;

class ProductsFixtures extends Fixture 

{
    public function load(ObjectManager $manager): void
    {

        $faker= Faker\Factory::create('fr_FR');

        for($prod = 1; $prod <= 10; $prod++) {

            $product = new Products();
            $product->setName($faker->text(15));
            $product->setDescription($faker->text());
            $product->setPrice($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(0, 50));
            $product->setCreatedAt(new DateTimeImmutable('Y-m-d H;i;s'));
          
            //on va chercher une reference de categorie
            $category= $this->getReference('cat-'.rand(1,8));
            $product->setCategories($category);

            $this->setReference('prod-'.$prod, $product);
            $manager->persist($product);

           }

         $manager->flush();

    }
}
