<?php 

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;

class ProductController extends Controller
{
	public function listAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$repository = $em->getRepository('AppBundle:Product');

		//$request->query == $_GET
		$minPrice = $request->query->get('min-price', 0);
		if ($minPrice > 0){
			$products = $repository->findProductsWithPriceGreaterThan($minPrice);
		} else {
			$products = $repository->findAll();
		}


		return $this->render('AppBundle:Product:list.html.twig', [
			'product_list' => $products
		]);
	}

	public function createAction()
	{
		$product = new Product();
		$product->setTitle("Symfony book");
		$product->setPrice(19.99);
		$product->setDescription("Learn this awesome PHP framework!");

		$em = $this->getDoctrine()->getManager();
		$em->persist($product);
		$em->flush();

		return new Response('Product '.$product->getId().' is saved');
	}

	public function getProductById($productId)
	{
		$em = $this->getDoctrine()->getManager();
		$product = $em->getRepository('AppBundle:Product')
			->find($productId);

		if(!$product){
			throw $this->createNotFoundException(
				'Product # '.$productId.' was not found'
			);
		}

		return $product;
	}

	public function showAction($productId)
	{
		$product = $this->getProductById($productId);

		return $this->render('AppBundle:Product:product.html.twig', [ 
            'product' => $product,
        ]);
	}

	public function updateAction(Request $request)
	{
		$productId = $request->attributes->get('productId');
		$product = $this->getProductById($productId);

		$price = $request->query->get('price', 0);
		if ($price > 0){
			$product->setPrice($price);
		} else {
			$product->setTitle("Modified title but not price");
		}

		$em = $this->getDoctrine()->getManager();
		$em->flush();

		return $this->redirectToRoute('show', [
			'productId' => $productId
		]);
	}

	public function deleteAction(Request $request)
	{
		$productId = $request->attributes->get('productId');
		$product = $this->getProductById($productId);

		$em = $this->getDoctrine()->getManager();
		$em->remove($product);
		$em->flush();

		return $this->redirectToRoute('show', [
			'productId' => $productId
		]);	
	}

}