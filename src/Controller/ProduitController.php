<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController {
    #[Route('/produits', name: 'app_produits')]
    public function index(ProduitRepository $pr): Response {
        return $this->render('produit/index.html.twig', [
            'produits' => $pr->findAll(),
        ]);
    }
}
