<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController {
    #[Route('/add-panier/{produit}', name: 'add_panier')]
    public function index(Produit $produit): Response {
        return new Response('OK pour le produit ' . $produit->getNom());
    }
}
