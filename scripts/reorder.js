function handleReOrder (reorderPostAdd) {
    $(document).ready(function () {
        var orderData;
        $("#order-save").hide();
        $("#order").find("thead > tr").first().attr("id", "header").addClass("nodrag");
        $("#order").find("tbody > tr").each(function(idx, Elem) {
            var href = $(Elem).find("td > a").first().attr("href");
            $(Elem).attr("id", href.substr(href.lastIndexOf("/") + 1));
        });
        $("#order").tableDnD({
            onDragStart:function(table, row) {
                orderData = "";
                $("#order-save").show("slow");
            },
            onDrop:function(table, row) {
                orderData = $.tableDnD.serialize();
            }
        });
        $("#order-save").click(function(e){
            $.post(reorderPostAdd, orderData, function(data) {
                $("#order-save").hide("fast");
            })
        });
    });
}