<?php
namespace App\Service\BarImageService;

class BarImageService
{
    public function getBarImage(Bar $bar): string
    {
        return $bar->getB64Image();
    }
}