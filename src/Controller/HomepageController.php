<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('/homepage.html.twig', [
            'message' => 'Welcome traveler, perhaps you struggle with whatever in your life... And you think my web-site is late.
            However, my website is never late nor early. It arrives precisely on time.',
            'about' => "So what is GondorGains, this iteration being 2.0 ? Well, it all started as a `love project`. I myself struggled with my physicality for a
            long time. Recently I managed to do so much for myself, and this is something I wish others can use. The workout programs are legit and tested. Now, go at it, Gondorian",
        ]);
    }
}
