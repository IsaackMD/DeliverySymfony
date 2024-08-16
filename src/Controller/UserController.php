<?php

namespace App\Controller;

use App\Entity\CatComida;
use App\Entity\Menu;
use App\Entity\Negocio;
use App\Entity\Pedido;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Component\BrowserKit\Request;

#[Route('/user')]
class UserController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        $comidas = $this->entityManager->getRepository(Menu::class)->findAll();
        $Negocios = $this->entityManager->getRepository(Negocio::class)->findBy(['Estatus' => true]);
        // dump($Negocios);
        // die;
    
        // Obtener el carrito asociado al usuario (aquí necesitas adaptar esta parte según cómo se almacenan los carritos en tu aplicación)
        

        $categorias = $this->entityManager->getRepository(CatComida::class)->findAll();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'comidas' => $comidas,
            'Negocios' => $Negocios,
            'categorias' => $categorias

        ]);
    }
    #[Route('/Det/Com/{id}', name: 'app_det_comida')]
    public function DetComida($id): Response
    {
        $comida = $this->entityManager->getRepository(Menu::class)->findOneBy(['id' => $id]);
        $negocio = $comida->getNegocio();
        $comidas = $this->entityManager->getRepository(Menu::class)->findBy(['cat_comida' => $comida->getCatComida()]);
        // dump($comidas);
        // // dump($negocio);
        // die;
        return $this->render('user/detcomida.html.twig', [
            'comida' => $comida,
            'negocio' => $negocio,
            'comidas' => $comidas
        ]);
    }

    #[Route('/carrito', name :'app_carrito')]
    public function Carrito(): JsonResponse{
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);
        $carrito = $this->entityManager->getRepository(Pedido::class)->findById($user,1);
        $datos = [];
        foreach($carrito as $pedido){
            $pedidoMenus = $pedido->getPedidoMenus();
            foreach($pedidoMenus  as $pedidoMenu){
                $menu = $pedidoMenu->getMenu();
                $menus[] = [
                    'id' => $menu->getId(),
                    'nombre' => $menu->getNomMenu(),
                    'imagen' => $menu->getImagen(),
                    'precio' => $menu->getPrecio(),
                    'complemento' => $menu->getComplemento(),
                    'cantidad' => $pedidoMenu->getCantidad(),
                ];
            }
            
        }
        $datos[] = [
            'id' => $pedido->getId(),
            'menus' => $menus,
        ];
        return new JsonResponse($datos);

    }


    #[Route('/Delete/c{cid}/p{id}', name:'app_delete')]
    public function Delete($cid,$id): JsonResponse
    {    
        $carrito = $this->entityManager->getRepository(Pedido::class)->findOneBy(['id'=>$cid]);
        if(!$carrito){
            return new JsonResponse(['message' => 'Carrito no encontrado'], Response::HTTP_NOT_FOUND);
        }
        foreach($carrito as $pedido){
            $pedidoMenus = $pedido->getPedidoMenus();
            foreach($pedidoMenus  as $pedidoMenu){
                $menu = $pedidoMenu->getMenu();
                if($menu->getId() == $id){
                    $carrito->removeMenu($menu);
                }
            }
            
        }
        $this->entityManager->flush();
        // regresar un resultado de exito un 200
        return new JsonResponse(Response::HTTP_OK);
    }
}
