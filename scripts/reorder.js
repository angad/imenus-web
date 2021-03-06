/**
 * Categories and Items reordering script file
 * To use with jquery.tablednd library
 * @author Patrick
 */


/**
 * Handle Re-Ordering
 * @param   orderTable      Table of items that can be re-ordered. First cell of each row should contain an <a> element,
                                with the last portion of the href corresponding to the ID of each item.
 * @param   reorderPostAdd  Address to POST the sorted array to
 */
function handleReOrder (orderTable, reorderPostAdd) {
    $(document).ready(function () {
        var orderData;
        var tableName = "#" + orderTable;
        var saveName = tableName + "-save";
        $(tableName).before("<br/><span id='" + orderTable + "-save' class='reorderSave'></span>");
        $(saveName).hide();
        $(tableName).find("thead > tr").first().attr("id", "header").addClass("nodrag");
        $(tableName).find("tbody > tr").each(function(idx, Elem) {
            var href = $(Elem).find("td > a").first().attr("href");
            $(Elem).attr("id", href.substr(href.lastIndexOf("/") + 1));
        });
        $(tableName).tableDnD({
            onDragStart:function(table, row) {
                orderData = "";
                $(saveName).text("Save");                
                $(saveName).show("slow");
            },
            onDrop:function(table, row) {
                orderData = $.tableDnD.serialize();
            }
        });
        $(saveName).click(function(e){
            $(this).text("Saving...");            
            $.post(reorderPostAdd, orderData, function(data) {
                $(saveName).hide("fast");
                $(saveName).text("Saved");
            })
        });
    });
}