<?php

namespace App\Controller;

use App\Entity\Produit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

        if (!empty($panier[$produit->getId()])) $panier[$produit->getId()]['quantite'] = min($quantite + $panier[$produit->getId()], $produit->getStock());
        else $panier[$produit->getId()] = [
            'quantite' => min($quantite, $produit->getStock()),
            'produit' => $produit
        ];

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_produits');
    }

    #[Route('/panier', name: 'panier')]
    public function show(SessionInterface $session): Response {
        $panier = $session->get('panier', []);

        $tva = 0;
        $total = 0;
        foreach ($panier as $entree) {
            $produit = $entree['produit'];
            $tva += $produit->getPrix() * $entree['quantite'] * $produit->getTauxTva() / 100;
            $total += $produit->getPrix() * $entree['quantite'];
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
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
