$(document).ready(function () {
    ///////////////////////////////////////////
    //  AJOUT DES INGREDIENTS DE LA RECETTE  //
    ///////////////////////////////////////////
    if (window.location.href.indexOf("/admin/recette/ajouter") != -1) {
        var container = $('div#recipe_recipeIngredients');
        var index = container.find(':input').length;

        $('#add_ingredient').click(function (e) {
            addCategory(container);
            e.preventDefault();
            return false;
        });

        if (index == 0) {
            for (var i = 0; i < 3; i++) {
                addCategory(container);
            }
        } else {
            container.children('div').each(function () {
                addDeleteLink($(this));
            });
        }

        function addCategory(contain) {
            var template = contain.attr('data-prototype')
                .replace(/__name__label__/g, '')
                .replace(/__name__/g, index)
                ;

            var prototype = $(template);
            addDeleteLink(prototype);
            contain.append(prototype);

            var container1 = $('div#recipe_recipeIngredients_' + index + '_ingredients');
            var template1 = container1.attr('data-prototype')
                .replace(/__name__label__/g, '')
                .replace(/__name__/g, index)
                ;

            var prototype1 = $(template1);
            container1.append(prototype1);
            $("#recipe_recipeIngredients legend:contains('Ingredients')").html("Nom de l'ingrédient :");
            
            var blockRIIndex = $('div#recipe_recipeIngredients_' + index);
            blockRIIndex.addClass("block-ingredients");
            var blockNameIngredient = $('div#recipe_recipeIngredients_' + index + '_ingredients').parent();
            blockNameIngredient.addClass("block-name-ingredient");
            var inputNameIngredient = $('div#recipe_recipeIngredients_' + index + '_ingredients').children();
            inputNameIngredient.addClass("input-name-ingredient");

            index++;
        }

        function addDeleteLink(proto) {
            var deleteLink = $('<i class="fas fa-times-circle" title="supprimer"></i>');
            proto.append(deleteLink);

            deleteLink.click(function (e) {
                proto.remove();
                e.preventDefault();
                return false;
            });
        }


        //////////////////////////////////////////////////////
        //  AJOUT DES ETAPES ET DESCRIPTION DE LA RECETTE  //
        /////////////////////////////////////////////////////

        var containerStep = $('div#recipe_steps');
        var indexStep = containerStep.find(':input').length;

        $('#add_step').click(function (e) {
            addCategoryStep(containerStep);
            e.preventDefault();
            return false;
        });

        if (indexStep == 0) {
            for (var i = 0; i < 3; i++) {
                addCategoryStep(containerStep);
            }
        } else {
            containerStep.children('div').each(function () {
                addDeleteLinkStep($(this));
            });
        }

        function addCategoryStep(containStep) {
            var templateStep = containStep.attr('data-prototype')
                .replace(/__name__label__/g, '')
                .replace(/__name__/g, indexStep)
                ;

            var prototypeStep = $(templateStep);
            addDeleteLinkStep(prototypeStep);
            containStep.append(prototypeStep);
            var blockRSIndex = $('div#recipe_steps_' + indexStep);
            blockRSIndex.css({ "float": "left" });

            indexStep++;
        }

        function addDeleteLinkStep(protoStep) {
            var deleteLinkStep = $('<div class="deleteLinkStep"><i class="fas fa-times-circle" title="Supprimer"></i></div>');
            protoStep.append(deleteLinkStep);

            deleteLinkStep.click(function (e) {
                protoStep.remove();
                e.preventDefault();
                indexStep--;
                return false;
            });
        }
    }
})