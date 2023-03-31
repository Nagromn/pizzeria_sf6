<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
      #[Route('/notre-carte', name: 'menu', methods: ['GET'])]
      public function index(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/menu.html.twig', [
                  'products' => $productRepository->findAllProducts($request->query->getInt('page', 1)),
                  'pizzas' => $productRepository->findAllPizza($request->query->getInt('page', 1)),
                  'drinks' => $productRepository->findAllDrinks($request->query->getInt('page', 1)),
                  'desserts' => $productRepository->findAllDesserts($request->query->getInt('page', 1))
            ]);
      }

      #[Route('produit/{slug}', name: 'product.show', methods: ['GET'])]
      public function show(Product $product): Response
      {
            return $this->render('pages/product/product.html.twig', [
                  'product' => $product
            ]);
      }
}
