<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramSearchType;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/wild", name="wild_")
 */

class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(Request $request) :Response
    {
        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $programs = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findBySearch($data['searchField']);
        }

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs, 'form' => $form->createView()]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
            ->createNotFoundException('No slug has bee sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     * Getting program's by category
     *
     * @param string $categoryName
     * @Route("/category/{categoryName}", name="show_category")
     * @return Response
     */
    public function showByCategory(?string $categoryName): Response
    {
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(array('name' => $categoryName));

        if (!$category) {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in program\'s table.'
            );
        }

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(array('category' => $category->getId('id')),
                array('id' => 'desc'),
                3,
                0
            );

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in program\'s table.'
            );
        }

        return $this->render('wild/category.html.twig', ['categoryName' => $categoryName, 'programs' => $programs]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function showByProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has bee sent to find a program in program\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(array('title' => $slug));

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(array('program' => $program));

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' , found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', ['program' => $program, 'slug' => $slug, 'seasons' => $season]);
    }

    /**
     * Getting season's program
     *
     * @param int $id
     * @Route("/season/{id}", name="season")
     * @return Response
     **/

    public function showBySeason (int $id)
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(array('id' => $id));

        $programs = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', ['season' => $season, 'program' => $programs, 'episodes' => $episodes]);
    }


    /**
     * Getting episode
     * @param Episode $episode
     * @Route("/episode/{id}", name="episode")
     * @return Response
     **/

    public function showEpisode (Episode $episode)
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', ['episode' => $episode, 'season' => $season, 'program' => $program]);
    }


}
