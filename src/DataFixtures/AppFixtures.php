<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use App\Service\SlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;
    private SlugService $slugService;

    public function __construct(UserPasswordEncoderInterface $encoder, SlugService $slugService)
    {
        $this->encoder = $encoder;
        $this->slugService = $slugService;
    }

    /**
     * Load data fixtures into the database.
     *
     * @param ObjectManager $manager The object manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        require_once 'vendor/autoload.php';

        // use the factory to create a Faker\Generator instance
        $faker = Factory::create('fr_FR');

        // Generate Users
        for ($i = 0; $i < 50; $i++) {
            $user = new User();

            $user->setEmail($faker->email)
                ->setRoles((array)$faker->boolean())
                ->setNameIdentifier($faker->lastName())
                ->setFirstnameIdentifier($faker->firstName())
                ->setPseudo($faker->userName())
                ->setActivated($faker->boolean())
                ->setPictureIdentifier($faker->imageUrl());

            $password = $this->encoder->encodePassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($user);

            // Generate Figures for each User
            for ($j = 0; $j < 2; $j++) {
                $figure = new Figure();

                $title = $faker->words(3, true);

                $figure->setTitle($title)
                    ->setContentFigure($faker->text(3000))
                    ->setCategory($faker->randomElement(['easy', 'medium', 'hard']))
                    /*->setPictureFigure('/img/figure-0001.jpeg')*/
                    ->setVideoFigure($faker->imageUrl())
                    ->setDateCreate($faker->dateTimeBetween('-6 month', 'now'))
                    ->setUserAssociated($user)
                    ->setSlug($this->slugService->generateSlug($title))
                    ->setDateUpdate($faker->dateTimeBetween('-1 month', 'now'));

                $manager->persist($figure);

                // Generate Comments for each Figure
                for ($k = 0; $k < 20; $k++) {
                    $comment = new Comment();

                    $comment->setContentComment($faker->text(50))
                        ->setDateCreate($faker->dateTimeBetween('-6 month', 'now'));

                    $commentUser = $user;
                    $comment->setUserAssociated($commentUser);

                    $commentFigure = $figure;
                    $comment->setFigureAssociated($commentFigure);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
