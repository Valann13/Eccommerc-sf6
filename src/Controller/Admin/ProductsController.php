<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{


    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(('ROLE_ADMIN'));

        //on crée un nouveau produit
        $product = new Products();

        // on crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //on traite la requete du formulaire
        $productForm->handleRequest($request);

        // on verifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // On arrondit  le prix en centime
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            // on stock dans la BDD
            $em->persist($product);
            $em->flush();
            //$this->addFlash('success','produit ajouté avec succès');

            // On redirige
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()

            //autre facon 
            //return $this->renderForm('admin/product/add.html.twig', compact('productForm'))
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em): Response
    {
        //on verifie si l'utilsateur peux editer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // On divise  le prix par 100
        $prix = $product->getPrice() / 100;
        $product->setPrice($prix);

        // on crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //on traite la requete du formulaire
        $productForm->handleRequest($request);

        // on verifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // On arrondit  le prix en centime
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            // on stock dans la BDD
            $em->persist($product);
            $em->flush();
            //$this->addFlash('success','produit modifié avec succès');

            // On redirige
            return $this->redirectToRoute('admin_products_index');
        }
        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView()

        ]);
    }
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product): Response

    {
        //on verifie si l'utilsateur peux editer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }
}
