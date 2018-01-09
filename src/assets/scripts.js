/**
 * Subfield Repeater Dependencies.
 */
(function($) {

    /**
     * Select2
     */
    var refreshSelect2 = function() {

      /**
       * Select2 Single
       */
      if ($('.type-select2 select').length) {

        $(".type-select2 select").select2();

      }

      /**
       * Select2 Multiple
       */
      if ($('.type-select2-multiple select').length) {

        $('.type-select2-multiple select').each(function() {

          // Setup Select/Keep selected order & cache order of initial values.
          var $select2 = $(this).select2();
          var defaults = $select2.select2('data');
          
          defaults.forEach(function(obj) {

            // vars.
            var order           = $select2.data('preserved-order') || [];
            order[order.length] = obj.id;

            // Set data preserved order.
            $select2.data('preserved-order', order);

          });

          function selectionHandler(e) {

            // vars.
            var $select2   = $(this);
            var val        = e.params.data.id;
            var order      = $select2.data('preserved-order') || [];
            var $container = $select2.next('.select2-container');
            var $tags      = $container.find('li.select2-selection__choice');
            var $input     = $tags.last().next();

            switch (e.type) {

              case 'select2:select':

                order[order.length] = val;

                break;

              case 'select2:unselect':

                var found_index = order.indexOf(val);

                if (found_index >= 0) {

                  order.splice(found_index, 1);

                }

                break;

            }

            // Store for later usage.
            $select2.data('preserved-order', order);

            // Apply tag order.
            order.forEach(function (val) {

              var $el = $tags.filter(function (i, tag) {

                return $(tag).data('data').id === val;

              });

              $input.before($el);

            });

            $select2.prev('input').val(order);
          }

          $select2.on('select2:select select2:unselect', selectionHandler);

        });

      }

    }

    // Initial Select2 Call
    refreshSelect2();


    /**
     * Data Repeater
     */
    if ($('[data-repeater]').length) {

      /**
       * Update Field Names/Ids/Labels
       */
      var updateNames = function(parent) {

        // vars.
        var $additions = $('[data-subfields-parent="' + parent + '"]');
        var find       = parent + '\\[\\d\\]';

        // Loop All Repeater Addition Groups.
        $.each($additions, function(additionIndex) {

          var $inputGroups = $(this).find('.input-group');
          var replace      = parent + '[' + additionIndex + ']';

          // Loop Inputs in Addition Group.
          $.each($inputGroups, function() {

            // vars.
            var $this = $(this);
            var type  = $this.data('type');
            var name  = $this.data('key').replace(new RegExp(find, 'g'), replace);
            var input = type != 'select2-multiple' ? ':input' : ':input[type="hidden"]';

            // Data Key & Name Update.
            $this.attr('data-key', name);
            $this.find(input).attr('name', name);
            
            // Checkbox/Radio Label & ID Update.
            if (type == 'radio' || type == 'checkbox') {

              $.each($this.find(input), function () {

                var $this = $(this);

                $this.parent('label').attr('for', name + '-' + $this.val());
                $this.attr('id', name + '-' + $this.val());

              });

              return;

            }
            
            // Label Update.
            $this.find('label').attr('for', name);

          });

        });

      }

      /**
       * Add Repeater Addition.
       */
      $('[data-subfields-add]').on('click', function(ev) {

        ev.preventDefault();

        // vars.
        var parentKey           = $(this).data('subfields-add');
        var $subfieldsContainer = $('[data-subfields-container="' + parentKey + '"]');
        var buttonHTML          = '<button class="button-remove">&#10005;</button>';
        var $addtions           = $('[data-subfields-parent="' + parentKey + '"]');

        // Add button if more than 1.
        if ($addtions.length == 1) {

          $addtions.append(buttonHTML);

        }

        // Append new input group. Set temp input names.
        var $copy = $addtions.eq(0).clone();
        $copy.find(':input').attr('name', '');
        $copy.appendTo($subfieldsContainer);

        // Remove Values, Select2, Editor.
        var $newAddition = $('[data-subfields-parent="' + parentKey + '"]').last();
        $newAddition.find(':input').not('[type="radio"], [type="checkbox"]').val('');
        $newAddition.find('[type="radio"], [type="checkbox"]').prop('checked', false);
        $newAddition.find('.select2').remove();
        $newAddition.find('.wp-editor-wrap').html('<div class="notification-error">Repeater WP Editor coming soon, please use textarea for time being.</div>');

        // Update Names.
        updateNames(parentKey);

        // Refresh Sortable/Select2.
        $subfieldsContainer.sortable('refresh');
        refreshSelect2();

      });


      /**
       * Remove Repeater Addition.
       */
      $('[data-subfields-container]').on('click', '.button-remove', function(ev) {

        ev.preventDefault();

        // vars.
        var $parentContainer    = $(this).parents('[data-subfields-parent]');
        var parentKey           = $parentContainer.data('subfields-parent');
        var $subfieldsContainer = $('[data-subfields-container="' + parentKey + '"]');
        var $additions          = $('[data-subfields-parent="' + parentKey + '"]');

         // Don't remove if only one left.
        if ($additions.length == 1) {

          return;

        }

        // Remove.
        $parentContainer.remove();

        // Remove button if only 1.
        if ($additions.length == 2) {

          $additions.find('.button-remove').remove();

        }
        
        // Update Names.
        updateNames(parentKey);

        // Refresh sortable.
        $subfieldsContainer.sortable('refresh');

      });

      // Sortable on Subfields container. V2
      $('[data-repeater]').sortable( {

        update: function() {

          updateNames($(this).data('subfields-container'));

        }

      });

    }

  })(jQuery);


  /**
   * Conditional Dependencies.
   */
  (function($) {

    if ($('[data-dependency-key]').length) {

      var $dependencyInputs = [];

      /**
       * Updates Fields on Load/Input Change.
       */
      var updateFields = function() {

        $('[data-dependency-key]').each(function(){

          // Declare variables.
          var $this            = $(this);
          var key              = $this.data('dependency-key');
          var value            = typeof $this.data('dependency-value') == 'undefined' ? 0 : $this.data('dependency-value');
          var condition        = $this.data('dependency-condition') ? $this.data('dependency-condition').toLowerCase() : '';
          var $dependency      = $('[data-key=' + key + ']');
          var $dependencyInput = $dependency.find('[name=' + key + ']');
          var dependencyType   = $dependency.data('type');
          var dependencyValue  = $dependencyInput.val() && !isNaN($dependencyInput.val()) ? parseInt($dependencyInput.val()) : $dependencyInput.val();
          var isShown          = false;
          var valueIsArray     = value instanceof Array ? true : false;

          // Add object to array.
          $dependencyInputs.push($dependencyInput);

          // Checkbox check.
          if (dependencyType == 'checkbox') {

            dependencyValue = $dependencyInput.last().is(':checked') ? 1 : 0;

          }

          // Radio check.
          if (dependencyType == 'radio') {

            dependencyValue = $dependencyInput.filter(':checked').val();

          }

          // Run defined condition check between dependency & given value.
          switch (condition) {

            case '=':
            case '==':

              if ((valueIsArray && value.indexOf(dependencyValue) !== -1) || (!valueIsArray && dependencyValue == value)) {

                isShown = true;

              }

              break;

            case '!=':

              if ((valueIsArray && value.indexOf(dependencyValue) === -1) || (!valueIsArray && dependencyValue != value)) {

                isShown = true;

              }

              break;

            case '>=':

              if (dependencyValue >= value) {

                isShown = true;

              }

              break;

            case '<=':

              if (dependencyValue <= value) {

                isShown = true;

              }

              break;

            case '>':

              if (dependencyValue > value) {

                isShown = true;

              }

              break;

            case '<':

              if (dependencyValue < value) {

                isShown = true;

              }

              break;

            case 'between':

              if (valueIsArray && dependencyValue > value[0] && dependencyValue < value[1]) {

                isShown = true;

              }

              break;

            case 'outside':

              if (valueIsArray && (dependencyValue < value[0] || dependencyValue > value[1])) {

                isShown = true;

              }

              break;

            case 'contains':

              if (dependencyValue.indexOf(value) !== -1) {

                isShown = true;

              }

              break;
          }

          // Check if dependency is hidden, then don't show.
          if ($dependency.data('hidden') == true) {

            isShown = false;

          }

          // Select2/Select multiple check. Not Available, so show field.
          if (dependencyType == 'select2-multiple' || dependencyType == 'select-multiple') { 

            alert('NOTICE: Dependencies are not available for MULTIPLE OPTION SELECTS.');
            isShown = true;

          }

          // If not shown, hide siblings dependencies.
          if (isShown) {

            $this.data('hidden', false);

            // Metabox show.
            if ($this.data('type') == 'metabox') {

              $this.parents('#' + $this.data('key')).show();
              return;

            }

            // Field show.
            $this.show();

          } else {

            $this.data('hidden', true);

            // Metabox hide.
            if ($this.data('type') == 'metabox') {

              $this.parents('#' + $this.data('key')).hide();
              return;

            }

            // Field hide.
            $this.hide();

          }

        });

      }

      // Initial Update Fields Call
      updateFields();

      // Bind Change Event to Dependency Inputs
      $.each($dependencyInputs, function(){

        $(this).on('change', function(){

          updateFields();

        });

      });

    }

})(jQuery);