/**
 * Set Meal Listing script file
 * @author Patrick
 */


/**
 * Handle Set Meal Items Listing
 * @param   itemSelect      Select Form Item that has all the Items as its options
 * @param   itemTable       ID of table to use for displaying selected items and their quantities
 * @param   curValues       2D Array of current values. Index 0 should correspond to Item ID, and Index 1 should correspond to selected quantity.
 */
function handleSetMeal (itemSelect, itemTable, curValues) {
    var count = 0;
    addToItems = function(id, value) {
        if (id == undefined)
            return;
        var name = $('#' + itemSelect + ' option[value="' + id + '"]').attr('disabled', 'disabled').text();

        $('#' + itemTable + ' > tbody:last').append('<tr id="listItem' + id + '"><td class="listItemRemove"></td><td class="listItemName"></td><td class="listItemQty"></td></tr>');
        $('#listItem' + id + ' > .listItemRemove').text('Remove').css('cursor', 'pointer').click(function () {
            removeFromItems(id);
          });
        $('#listItem' + id + ' > .listItemName').text(name.trim());
        $('#listItem' + id + ' > .listItemQty').append('<input name="items[' + count + '][ItemID]" type="hidden" value="' + id + '"><input name="items[' + count + '][ItemQuantity]">').children('input').last().attr('value', value);
        ++count;
    };
    
    removeFromItems = function(id) {
        $('#listItem' + id).remove();
        $('#' + itemSelect + ' option[value="' + id + '"]').removeAttr('disabled');
    }
    $(document).ready(function() {
        for (var i = 0; i < curValues.length; ++i)
            addToItems(curValues[i][0], curValues[i][1]);
        $('#' + itemSelect).change(function() {
            var selected = $('#' + itemSelect + ' option:selected');
           addToItems(selected.val(), 1);
        });
    });
}