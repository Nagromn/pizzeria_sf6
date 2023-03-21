<?php

namespace App\Controller\Product;

use App\Form\ProductType;
use App\Entity\Product\Product;
use App\Repository\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
      #[Route('/', name: 'index', methods: ['GET'])]
      public function index(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/index.html.twig', [
                  'products' => $productRepository->findAllPizza($request->query->getInt('page', 1))
            ]);
      }

      #[Route('/pizzas', name: 'product.pizzas', methods: ['GET'])]
      public function pizza(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/pizzas.html.twig', [
                  'products' => $productRepository->findAllPizza($request->query->getInt('page', 1))
            ]);
      }

      #[Route('/boissons', name: 'product.drinks', methods: ['GET'])]
      public function drinks(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/drinks.html.twig', [
                  'products' => $productRepository->findAllDrinks($request->query->getInt('page', 1))
            ]);
      }

      #[Route('/desserts', name: 'product.desserts', methods: ['GET'])]
      public function desserts(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/desserts.html.twig', [
                  'products' => $productRepository->findAllDesserts($request->query->getInt('page', 1))
            ]);
      }

      #[Route('produit/{slug}', name: 'product.show', methods: ['GET'])]
      public function show(Product $product): Response
      {
            return $this->render('pages/product/product.html.twig', [
                  'product' => $product
            ]);
      }

      #[Route('/produit/edition/{id}', name: 'product.edit', methods: ['GET', 'POST'])]
      public function edit(
            Product $product,
            Request $request,
            EntityManagerInterface $manager
      ): Response {
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                  $product = $form->getData();

                  $manager->persist($product);
                  $manager->flush();

                  $this->addFlash(
                        'success',
                        'Le produit a été modifié avec succès.'
                  );

                  return $this->redirectToRoute('product.index');
            }

            return $this->render('pages/product/edit_product.html.twig', [
                  'form' => $form->createView()
            ]);
      }

      #[Route('produit/suppression/{id}', 'product.delete', methods: ['GET'])]
      public function delete(Product $product, EntityManagerInterface $manager): Response
      {
            $manager->remove($product);
            $manager->flush();

            $this->addFlash(
                  'success',
                  "Le produit a été supprimé avec succès."
            );

            return $this->redirectToRoute('product.index');
      }

      // On récupère les derniers id des produits enregistrés
      #[Route('/', name: 'index', methods: ['GET'])]
      public function findLastId(ProductRepository $productRepository, Request $request): Response
      {
            return $this->render('pages/product/index.html.twig', [
                  'products' => $productRepository->findLastId($request->query->getInt('page', 1))
            ]);
      }
}
