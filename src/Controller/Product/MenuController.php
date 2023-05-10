<?php

namespace App\Controller\Product;

use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
      /**
       * Affichage des différents produits de la carte
       *
       * @param ProductRepository $productRepository
       * @param Request $request
       * @return Response
       */
      #[Route('/notre-carte', name: 'menu', methods: ['GET'])]
      public function index(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            $products = $productRepository->findAllProducts($request->query->getInt('page', 1));
            $pizzas = $productRepository->findAllByCategory('Pizza', $request->query->getInt('page', 1));
            $drinks = $productRepository->findAllByCategory('Boisson', $request->query->getInt('page', 1));
            $desserts = $productRepository->findAllByCategory('Dessert', $request->query->getInt('page', 1));

            return $this->render('pages/product/menu.html.twig', [
                  'products' => $products,
                  'pizzas' => $pizzas,
                  'drinks' => $drinks,
                  'desserts' => $desserts,
            ]);
      }
}
