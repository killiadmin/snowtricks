<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\User;
use App\Service\UtilsService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;
    private UtilsService $utilsService;

    public function __construct(UserPasswordEncoderInterface $encoder, UtilsService $utilsService)
    {
        $this->encoder = $encoder;
        $this->utilsService = $utilsService;
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
        $user = new User();

        $user->setEmail('k.filatre@snowtricks.fr')
            ->setRoles(['ROLES_ADMIN'])
            ->setNameIdentifier('Filatre')
            ->setFirstnameIdentifier('Killian')
            ->setPseudo('Killiadmin')
            ->setActivated(1)
            ->setPictureIdentifier('admin_avatar.webp');

        $password = '$2y$10$/mXnNxvxqPns9hDAq0KNl.s2eVFe6hI3KbRg4sx3Tzcz/DD1D/pDq';
        $user->setPassword($password);
        $manager->persist($user);

        // Generate Figures for each User

        $datas = [
            [
                'title' => 'Half-pipe',
                'videos' => ['poRWToYtvsM', 'EmOeY7HT95I'],
                'pictures' => ['7c94410c49a5e708008100d2bb81bb5a.webp', 'c91e6cfd21ac88a237ba8d667aefc4d5.webp']
            ],
            [
                'title' => 'Le Indy Grab',
                'videos' => ['L4bIunv8fHM', 'lunYxCQrs1E'],
                'pictures' => ['fd70371dfad9918bcaab2ec5b46d8c23.webp', '2e71236c09dd5677ccaaaabfa05c40bc.webp']
            ],
            [
                'title' => 'Big air',
                'videos' => ['xIgDzNiI1x8', 'owLivG0ESwQ'],
                'pictures' => ['411f817bb08378df773351e7181f0a4e.webp', '5ef1e7f0b71d8fef558c2daf5630f662.webp']
            ],
            [
                'title' => 'Mc Twist',
                'videos' => ['k-CoAquRSwY', 'hgy-Ff2DS6Y'],
                'pictures' => ['0eb693d86ab7fd3e34d44fbe0d862f98.webp', 'bfb35ecdc98a8cde6afbde41fae14fce.webp']
            ],
            [
                'title' => 'Kicker',
                'videos' => ['Uf_huAKz90k', 'hHkQ-OzCIW0'],
                'pictures' => ['a915fff2dfc550ad79ce9e6f61f67229.webp', '475ab858b757df1aae69f8e609dd4009.webp']
            ],
            [
                'title' => 'Underflip',
                'videos' => ['-mMvG4nuGCM', 'KSdx9gNmqlc'],
                'pictures' => ['782a98cea865c928ef297c4ea56a886c.webp', 'f812248a8d04cb447e8d199d6e3889d0.webp']
            ],
            [
                'title' => 'Noseslide',
                'videos' => ['oAK9mK7wWvw', 'KqSi94FT7EE'],
                'pictures' => ['94d294c31948c6ae02aa06688442a336.webp', 'e85daece18738b1b0b2f907e1dc7105c.webp']
            ],
            [
                'title' => 'Ollie/Nollie',
                'videos' => ['aAzP3wNT220', 'H_tSuAipjWc'],
                'pictures' => ['b06957ff6a7a02726b2f199b9ca60d2e.webp', '65aec7127e5baf1af81c6b2ae8089523.webp']
            ],
            [
                'title' => 'Embase',
                'videos' => ['mBB7CznvSPQ', 'v4b8PmL3wjw'],
                'pictures' => ['025dff32ca95a8251811f1787adbb837.webp', '09026fa9bf992f057978056973dd014c.webp']
            ],
            [
                'title' => 'Jib',
                'videos' => ['Scpvby37V_E', '8WAnK76q2zo'],
                'pictures' => ['0aee6c739d9deb5b699837dce2386e49.webp', 'b50328e722a21735cb2cf2d801a81f67.webp']
            ]
        ];

        foreach ($datas as $data) {
            // Création de l'entité Figure
            $figure = new Figure();
            $figure->setTitle($data['title'])
                ->setContentFigure($faker->text(3000))
                ->setCategory($faker->randomElement(['easy', 'medium', 'hard']))
                ->setDateCreate($faker->dateTimeBetween('-6 month', 'now'))
                ->setUserAssociated($user)
                ->setSlug($this->utilsService->generateSlug($data['title']))
                ->setDateUpdate($faker->dateTimeBetween('-1 month', 'now'));

            $manager->persist($figure);

            // Génération de l'entité Media pour chaque vidéo
            foreach ($data['videos'] as $video) {
                $media = new Media();
                $media->setMedFigureAssociated($figure)
                    ->setMedType('video')
                    ->setMedVideo($video);

                $manager->persist($media);
            }

            // Génération de l'entité Media pour chaque image
            foreach ($data['pictures'] as $picture) {
                $media = new Media();
                $media->setMedFigureAssociated($figure)
                    ->setMedType('image')
                    ->setMedImage($picture);

                $manager->persist($media);
            }

            // Generation des Commentaires pour chaque Figure
            for ($k = 0; $k < 20; $k++) {
                $comment = new Comment();
                $comment->setContentComment($faker->text(50))
                    ->setDateCreate($faker->dateTimeBetween('-6 month', 'now'))
                    ->setUserAssociated($user)
                    ->setFigureAssociated($figure);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
