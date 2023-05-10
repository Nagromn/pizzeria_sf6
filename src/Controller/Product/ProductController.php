<?php

namespace App\Controller\Product;

use App\Entity\Mark;
use App\Form\MarkType;
use App\Entity\Product\Product;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
      /**
       * Affichage de la liste des produits
       *
       * @param ProductRepository $productRepository
       * @param Request $request
       * @return Response
       */
      #[Route('/', name: 'index', methods: ['GET'])]
      public function index(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/index.html.twig', [
                  'products' => $productRepository->findAllProducts($request->query->getInt('page', 1))
            ]);
      }

      /**
       * Affichage de la liste des pizzas
       *
       * @param ProductRepository $productRepository
       * @param Request $request
       * @return Response
       */
      #[Route('/pizzas', name: 'product.pizzas', methods: ['GET'])]
      public function pizza(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/pizzas.html.twig', [
                  'products' => $productRepository->findAllByCategory('Pizza', $request->query->getInt('page', 1))
            ]);
      }

      /**
       * Affichage de la liste des boissons
       *
       * @param ProductRepository $productRepository
       * @param Request $request
       * @return Response
       */
      #[Route('/boissons', name: 'product.drinks', methods: ['GET'])]
      public function drinks(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/drinks.html.twig', [
                  'products' => $productRepository->findAllByCategory('Boisson', $request->query->getInt('page', 1))
            ]);
      }

      /**
       * Affichage de la liste des desserts
       *
       * @param ProductRepository $productRepository
       * @param Request $request
       * @return Response
       */
      #[Route('/desserts', name: 'product.desserts', methods: ['GET'])]
      public function desserts(
            ProductRepository $productRepository,
            Request $request
      ): Response {
            return $this->render('pages/product/desserts.html.twig', [
                  'products' => $productRepository->findAllDesserts('Dessert', $request->query->getInt('page', 1))
            ]);
      }

      /**
       * Affichage du produit sélectionné
       *
       * @param ProductRepository $productRepository
       * @param Request $request
       * @return Response
       */
      #[Route('produit/{slug}', name: 'product.show', methods: ['GET', 'POST'])]
      public function show(
            Product $product,
            Request $request,
            MarkRepository $markRepository,
            EntityManagerInterface $manager
      ): Response {
            // Création d'une nouvelle instance de la classe Mark pour stocker la note de l'utilisateur
            $mark = new Mark();

            // Création d'un formulaire pour permettre à l'utilisateur de saisir sa note
            $form = $this->createForm(MarkType::class, $mark);

            // Traitement du formulaire s'il a été soumis
            $form->handleRequest($request);

            // Récupération de l'utilisateur connecté
            $user = $this->getUser();

            // Vérification si le formulaire a été soumis et est valide
            if ($form->isSubmitted() && $form->isValid()) {

                  // Récupération de toutes les commandes de l'utilisateur
                  $orders = $user->getOrders();

                  // Attribution de l'utilisateur et du produit à l'objet Mark
                  $mark->setUser($this->getUser())
                        ->setProduct($product);

                  // Recherche d'une note existante pour l'utilisateur et le produit actuel
                  $existingMark = $markRepository->findOneBy([
                        'user' => $this->getUser(),
                        'product' => $product
                  ]);

                  foreach ($orders as $order) {
                        // Récupération des détails de la commande
                        $orderDetails = $order->getOrderDetails();
                        $isProductOrdered = false;
                        foreach ($orderDetails as $orderDetail) {
                              // Vérification si le produit de la commande correspond au produit souhaité

                              if ($orderDetail->getProduct() === $product->getTitle() && $order->isIsPaid() === true) {
                                    // L'utilisateur a déjà commandé ce produit et la commande est payée
                                    // Vous pouvez faire ce que vous voulez ici (par exemple, l'empêcher de noter à nouveau)
                                    // Mettre la variable $isProductOrdered à true
                                    $isProductOrdered = true;

                                    // Sortir des deux boucles
                                    break 2;
                              }
                        }
                  }

                  // Vérification si l'utilisateur a acheté le produit actuel
                  if (!$isProductOrdered) {
                        // L'utilisateur n'a pas encore commandé ce produit ou la commande n'est pas payée
                        // Empêcher l'utilisateur de noter le produit
                        // Afficher un message d'erreur si le produit n'a jamais été acheté
                        $this->addFlash(
                              'error',
                              'Vous devez acheter ce produit avant de pouvoir le noter.'
                        );

                        // Redirection vers la page du produit
                        return $this->redirectToRoute('product.show', [
                              'slug' => $product->getSlug()
                        ]);
                  }

                  // Vérification si l'utilisateur a déjà noté le produit
                  if (!$existingMark) {
                        $manager->persist($mark);

                        // La note de l'utilisateur existe déjà, donc on la met à jour
                  } else {
                        $existingMark->setMark(
                              $form->getData()->getMark()
                        );
                  }

                  // Enregistrement de la note dans la base de données
                  $manager->flush();

                  // Afficher un message de succès si la note a été enregistrée
                  $this->addFlash(
                        'success',
                        'Votre note a été prise en compte.'
                  );

                  // Redirection vers la page du produit
                  return $this->redirectToRoute('product.show', [
                        'slug' => $product->getSlug()
                  ]);
            }

            // Affichage de la page du produit
            return $this->render('pages/product/show.html.twig', [
                  'product' => $product,
                  'form' => $form->createView()
            ]);
      }
}
