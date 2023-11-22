<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        require_once 'vendor/autoload.php';

        // use the factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');

        $user = new User();

        $user->setEmail('test@test.fr')
            ->setRoles((array)$faker->boolean())
            ->setNameIdentifier($faker->lastName())
            ->setFirstnameIdentifier($faker->firstName())
            ->setPseudo($faker->userName())
            ->setActivated($faker->boolean())
            ->setPictureIdentifier($faker->imageUrl());

        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);

        $manager->persist($user);

        //Create datas figures
        for ($i=0; $i < 10; $i++){
            $figure = new Figure();

            $figure->setTitle($faker->words(3, true))
                ->setContentFigure($faker->text(350))
                ->setCategory('easy')
                ->setPictureFigure($faker->imageUrl())
                ->setVideoFigure($faker->imageUrl())
                ->setDateCreate($faker->dateTimeBetween('-6 month', 'now'))
                ->setUserAssociated($user);

                $manager->persist($figure);
        }

        //Create datas comments
        for ($i=0; $i < 5; $i++){
            $comment = new Comment();

            $comment->setContentComment($faker->text(50))
                ->setDateCreate($faker->dateTimeBetween('-6 month', 'now'))
                ->setUserAssociated($user)
                ->setFigureAssociated($figure);

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
