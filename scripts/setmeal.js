function handleSetMeal (itemSelect, itemTable, curValues) {
    var count = 0;
    addToTable = function(id, value) {
        if (id == undefined)
            return;
        var name = $('#' + itemSelect + ' option[value="' + id + '"]').attr('disabled', 'disabled').text();
        $('#' + itemTable + ' > tbody:last').append('<tr id="listItem' + id + '"><td class="listItemRemove">Remove</td><td class="listItemName">' + name.trim() + '</td><td class="listItemQty"><input name="items[' + count + '][ItemID]" type="hidden" value="' + id + '"><input name="items[' + count + '][ItemQuantity]" value="' + value + '"></td></tr>');
        $('#listItem' + id + ' > td:first').css('cursor', 'pointer').click(function () {
            removeFromTable(id);
          });
        ++count;
    };
    
    removeFromTable = function(id) {
        $('#listItem' + id).remove();
        $('#' + itemSelect + ' option[value="' + id + '"]').removeAttr('disabled');
    }
    
    $(document).ready(function() {
        for (i = 0; i < curValues.length; ++i)
            addToTable(curValues[i][0], curValues[i][1]);
        $('#' + itemSelect).change(function() {
            var selected = $('#' + itemSelect + ' option:selected');
           addToTable(selected.val(), 1);
        });
    });
}