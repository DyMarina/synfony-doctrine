<?php

namespace App\Controller;

use App\Entity\VinylMix;
use App\Repository\VinylMixRepository;
use App\Service\MixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\u;

class VinylController extends AbstractController
{
    public function __construct(
        private bool $isDebug,
    )
    {}

    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        $tracks = [
            ['song' => 'Gangsta\'s Paradise', 'artist' => 'Coolio'],
            ['song' => 'Waterfalls', 'artist' => 'TLC'],
            ['song' => 'Creep', 'artist' => 'Radiohead'],
            ['song' => 'Kiss from a Rose', 'artist' => 'Seal'],
            ['song' => 'On Bended Knee', 'artist' => 'Boyz II Men'],
            ['song' => 'Fantasy', 'artist' => 'Mariah Carey'],
        ];

        return $this->render('vinyl/homepage.html.twig', [
            'title' => 'PB & Jams',
            'tracks' => $tracks,
        ]);
    }

    #[Route('/browse/{slug}', name: 'app_browse')]
    public function browse(
        VinylMixRepository $mixRepository,
        Request $request,
        string $slug = null
        ): Response
    {
        $genre = $slug ? u(str_replace('-', ' ', $slug))->title(true) : null;

        //$mixes = $this->mixRepository->findAll();
        //$mixRepository = $entityManager->getRepository(VinylMix::class);
        //$mixes = $mixRepository->findBy([], ['votes'=>'DESC']);
        //$mixes = $mixRepository->findAllOrderByVotes($slug);
        //dd($mixes);

        $queryBuilder = $mixRepository->createOrderedByVotesQueryBuilder($slug);
        $adpater = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adpater,
            $request->query->get('page', 1),
            9
        );

        return $this->render('vinyl/browse.html.twig', [
            'genre' => $genre,
            'pager' => $pagerfanta,
        ]);
    }
}
