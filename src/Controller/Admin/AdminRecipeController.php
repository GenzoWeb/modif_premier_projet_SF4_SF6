<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Entity\RecipeSearch;
use App\Form\RecipeSearchType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminRecipeController extends AbstractController
{

    #[Route('/recettes', name: 'admin.recipes')]
    public function index(PaginatorInterface $paginator, Request $request, RecipeRepository $recipeRepo): Response
    {
        $search = new RecipeSearch();
        $form = $this->createForm(RecipeSearchType::class, $search);
        $form->handleRequest($request);
        
        $data = $recipeRepo->findAllQuery($search);

        $recipes = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'form'    => $form->createView()
        ]);
    }

    #[Route('/recette/ajouter', name: 'admin.recipe.new')]
    public function new(Request $request, EntityManagerInterface $manager)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $numberSteps = count($recipe->getSteps());     
 
            for($i = 0; $i < $numberSteps; $i++)
            {
                $recipe->getSteps()[$i]->setNumberStep($i + 1);
            }
            $recipe->setCreatedAt(new \DateTime());

            $manager->persist($recipe);
            $manager->flush();
            $this->addflash('success', 'Recette ajoutée avec succés');

            return $this->redirectToRoute('admin.recipes');
        }

        return $this->render('admin/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }

    #[Route('/recette/supprimer/{id}', name: 'admin.recipe.delete', methods:['POST'])]
    public function delete(Recipe $recipe, Request $request, EntityManagerInterface $manager) : Response
    {
        
        if ($this->isCsrfTokenValid('delete' . $recipe->getId(), $request->get('_token')))
        {
            foreach($recipe->getRecipeIngredients() as $recipeIngredient) 
            {
                foreach($recipeIngredient->getIngredients() as $ingredient) 
                {
                    $manager->remove($ingredient);
                }
                $manager->remove($recipeIngredient);
            }
              
            $manager->remove($recipe);      
            $manager->flush();
            $this->addflash('success', 'Recette supprimée avec succés');
        }
        
        return $this->redirectToRoute('admin.recipes');
    }

    #[Route('/recette/editer/{id}', name: 'admin.recipe.edit', methods:['GET|POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $recipe->setUpdatedAt(new \DateTime());
            $manager->flush();
            $this->addflash('success', 'Recette modifiée avec succés');
            
            return $this->redirectToRoute('admin.recipes');
        }
        
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
        ]);
    }
}
