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
        $('#listItem' + id + ' > .listItemQty').append('<input name="items[' + count + '][ItemID]" type="hidden" value="' + id + '"><input name="items[' + count + '][ItemQuantity]">');
        $('#listItem' + id + ' > .listItemQty > input:last').attr('value', value);
        ++count;
    };
    
    removeFromItems = function(id) {
        $('#listItem' + id).remove();
        $('#' + itemSelect + ' option[value="' + id + '"]').removeAttr('disabled');
    }
    
    $(document).ready(function() {
        for (i = 0; i < curValues.length; ++i)
            addToItems(curValues[i][0], curValues[i][1]);
        $('#' + itemSelect).change(function() {
            var selected = $('#' + itemSelect + ' option:selected');
           addToItems(selected.val(), 1);
        });
    });
}