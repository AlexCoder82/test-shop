<?php

namespace App\DataFixtures;

use App\Entity\Purchase;
use App\Entity\PurchaseDetail;
use App\Entity\Product;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $products = [];
        for ($i = 0; $i < 50; $i++) {

            $product = new Product();
            $product
                ->setName($faker->text(255))
                ->setDescription($faker->text(255))
                ->setPrice($faker->randomFloat(2, 49, 99));

            $manager->persist($product);

            $products[] = $product;
        }

        for ($j = 0; $j < 10; $j++) {

            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'password');

            $user
                ->setFirstname($faker->text(255))
                ->setLastname($faker->text(255))
                ->setEmail($faker->email(255))
                ->setPassword($hash);

            $manager->persist($user);



            $purchase = new Purchase();
            $purchase->setUser($user)
                ->setDate(new \DateTime());

           

            $purchase_details = [];

            for ($k = 0; $k < 10; $k++) {

                $purchase_detail = new PurchaseDetail();
                $rand_index = array_rand($products); //Selecciona un producto aleatorio
                $rand_product = $products[$rand_index]; //Selecciona un producto aleatorio
                $quantity = rand(1, 10);
                $purchase_detail               
                    ->setProduct($rand_product)
                    ->setQuantity($quantity);

                $purchase_details[] = $purchase_detail;
            
            }
          
            $manager->persist($purchase);
            
           foreach( $purchase_details as $pd){
                $pd->setPurchase($purchase);
                $manager->persist($pd);
            }
            $purchase->setDetails($purchase_details);
        }

        $manager->flush();
    }
}
