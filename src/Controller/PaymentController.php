<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class PaymentController extends AbstractController {
    #[Route('/payment', name: 'payment')]
    public function index(SessionInterface $session, CommandeRepository $cr): Response {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide, vous ne pouvez donc pas payer...');
            return $this->redirectToRoute('app_produits');
        }

        $commande = new Commande;
        $commande->setEtat('En cours');

        foreach ($panier as $entree) {
            $produit = $entree['produit'];

            $commande->addProduit($produit);
        }

        $cr->add($commande);

        return $this->redirectToRoute('panier');
    }
}
