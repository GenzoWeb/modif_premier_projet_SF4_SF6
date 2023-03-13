<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\RecipeSearch;
use App\Form\RecipeSearchType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(RecipeRepository $recipeRepo, Request $request): Response
    {
        $recipes = $recipeRepo->findLatest(4);

        $search = new RecipeSearch();
        $form = $this->createForm(RecipeSearchType::class, $search);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $nameCategory = $search->getNameCategory();
            $categoryId = null;
            if(!is_null($nameCategory)){
                $categoryId = intval($nameCategory);
            }
            
            return $this->redirect($this->generateUrl('recipes', [
                'nameRecipe' => $search->getNameRecipe(),
                'ingredient' => $search->getIngredient(),
                'nameCategory' => $categoryId
            ]));
        }

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'form'    => $form->createView()
        ]);
    }

    #[Route('/recettes', name: 'recipes')]
    public function recipes(RecipeRepository $recipeRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $search = new RecipeSearch();
        $form = $this->createForm(RecipeSearchType::class, $search);
        $form->handleRequest($request);
        
        $data = $recipeRepo->findAllQuery($search);

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('recipe/recipes.html.twig', [
            'recipes' => $recipes,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/recette/{id}', name: 'recipe_show')]
    public function show(int $id, RecipeRepository $recipeRepo, Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = $recipeRepo->find($id);

        if (!$recipe) {
            throw new NotFoundHttpException();
        }

        $comment= new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime('NOW',new \DateTimeZone('Europe/Paris'));
            $comment->setRecipe($recipe)
                    ->setAuthor($this->getUser()->getUserIdentifier())
                    ->setCreatedAt($date);
                    
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/show.html.twig', [
           'recipe' => $recipe,
           'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/categorie/{name}', name: 'recipes_category', requirements: ['name' => 'entrées|plats|desserts'])]
    public function category(string $name, RecipeRepository $recipeRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $recipeRepo->findByCategory($name);

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('recipe/category.html.twig', [
            'name' => $name,
            'recipes' => $recipes
        ]);
    }
}
