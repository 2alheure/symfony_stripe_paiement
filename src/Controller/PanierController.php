<?php

namespace App\Controller;

use App\Entity\Produit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @IsGranted("ROLE_USER")
 */
class PanierController extends AbstractController {
    #[Route('/add-panier/{produit}', name: 'add_panier')]
    public function index(Produit $produit, SessionInterface $session, Request $request): Response {

        $quantite = $request->request->get('qtte');
        if ($quantite <= 0) throw new BadRequestHttpException;

        $panier = $session->get('panier', []);

        if (!empty($panier[$produit->getId()]))
            $panier[$produit->getId()] = min($quantite + $panier[$produit->getId()], $produit->getStock());
        else $panier[$produit->getId()] = min($quantite, $produit->getStock());

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_produits');
    }

    #[Route('/panier', name: 'panier')]
    public function show(SessionInterface $session, ProduitRepository $pr): Response {
        $panier = $session->get('panier', []);

        $ids = array_keys($panier);
        $produits = $pr->getAllProduits($ids);

        $tva = 0;
        $total = 0;
        $printablePanier = []; // L'équivalent de l'ancien panier pour l'affichage
        foreach ($panier as $id => $quantite) {
            $produit = $produits[$id];
            $tva += $produit->getPrix() * $quantite * $produit->getTauxTva() / 100;
            $total += $produit->getPrix() * $quantite;

            $printablePanier[$id] = [
                'quantite' => $quantite,
                'produit' => $produit
            ];
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $printablePanier,
            'total' => $total,
            'tva' => $tva,
        ]);
    }

    #[Route('/vider-panier', name: 'vider_panier')]
    public function vider(SessionInterface $session): Response {
        $session->set('panier', []);

        return $this->redirectToRoute('app_produits');
    }
}
