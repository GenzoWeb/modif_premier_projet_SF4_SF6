<?php

namespace App\Entity;

class RecipeSearch
{
    private ?string $ingredient = null;

    private ?string $nameRecipe = null;

    private ?string $nameCategory = null;

    public function getIngredient(): ?string
    {
        return $this->ingredient;
    }

    public function setIngredient(?string $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getNameRecipe(): ?string
    {
        return $this->nameRecipe;
    }

    public function setNameRecipe(?string $nameRecipe): self
    {
        $this->nameRecipe = $nameRecipe;

        return $this;
    }

    public function getNameCategory(): ?string
    {
        return $this->nameCategory;
    }

    public function setNameCategory(?string $nameCategory): self
    {
        $this->nameCategory = $nameCategory;

        return $this;
    }
}
