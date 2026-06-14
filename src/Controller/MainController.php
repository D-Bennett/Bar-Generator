<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\BarImageService\BarImageService;
use App\Service\BarImageService\Bar;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(BarImageService $barImageService): Response
    {

        $barImages = [

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_RED,
                value: -4,
                unit: 'mm',
                boundaries: [2, 4, 6, 8],
                boundaryLabels: ['4', '6'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_AMBER,
                value: 4,
                unit: 'mm',
                boundaries: [2, 4, 6, 8],
                boundaryLabels: ['4', '6'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_AMBER,
                value: 6,
                unit: 'mm',
                boundaries: [2, 4, 6, 8],
                boundaryLabels: ['4', '6'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_GREEN,
                value: 12,
                unit: 'mm',
                boundaries: [2, 4, 6, 8],
                boundaryLabels: ['4', '6'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_RED,
                value: 12,
                unit: 'mm',
                boundaries: [8, 6, 4, 2],
                boundaryLabels: ['6', '4'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_AMBER,
                value: 6,
                unit: 'mm',
                boundaries: [8, 6, 4, 2],
                boundaryLabels: ['6', '4'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_AMBER,
                value: 4,
                unit: 'mm',
                boundaries: [8, 6, 4, 2],
                boundaryLabels: ['6', '4'],
            )),

            $barImageService->getBarImage(new Bar(
                width: 900,
                height: 60,
                barColor: BAR::COLOUR_GREEN,
                value: -6,
                unit: 'mm',
                boundaries: [8, 6, 4, 2],
                boundaryLabels: ['6', '4'],
            )),

        ];

        return $this->render('app/index.html.twig', [
            'barImages' => $barImages,
        ]);
    }
}