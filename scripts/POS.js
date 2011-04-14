/**
 * POS / Bill Display script file
 * @author Patrick
 */


/**
 * Handle POS / Bill Display
 * @param   amountDisc      Array of pre-defined Discounts (in $)
 * @param   percentDisc     Array of pre-defined Discounts (in %)
 * @param   headerIDs       Array of IDs to assign the header cells of the table (CodeIgniter Table CSS workaround)
 * @param   GSTrate         GST Rate in %. Specify 0 for no GST.
 * @param   ServiceCharge   Service Charge in %. Specify 0 for no Service Charge.
 * @param   receiptTime     Time to print at the top of the receipt
 * @param   restName        Name of Restaurant
 * @param   restAdd         Address of Restaurant
 * @param   receiptTable    Table Number
 *
 * REQUIRES
 *  First table in #contentarea should contain the ordered items
 *  Table should have one thead section with one tr containing four headers: Quantity, Item, Price, Amount / Item Sub-total
 *  Table should have one tbody section with one tr per Item / Set Meal.
 */
function handlePOS(amountDisc, percentDisc, headerIDs, GSTrate, ServiceCharge, receiptTime, restName, restAdd, receiptTable, receiptRemarks) {
    $(document).ready(function () {
        var GSTrow;
        var SCrow;
        var OverallDiscRow;
        var OverallDiscAmt = 0;
        var OverallDiscPercent = 0;
        var TotalRow;
        
        var updateAmts = function () {
            var subtotal = 0;
            $('#contentarea > table:first > tbody > tr').each(function (idx, Elem) {
                if ($(Elem).attr('data-finalAmt') != undefined)
                    subtotal += parseFloat($(Elem).attr('data-finalAmt'));
            });
            subtotal = Math.round(subtotal * 100) / 100;
            if (SCrow != undefined)
                if (ServiceCharge > 0) {
                    var SCamount = Math.round(ServiceCharge * subtotal) / 100;
                    subtotal += SCamount;
                    $(SCrow).children('.itemTitle').text('Service Charge ' + ServiceCharge + '%');
                    $(SCrow).children('.itemValue').text(sprintf('$%01.2f', SCamount));
                    $(SCrow).show();
                } else
                    $(SCrow).hide();
            if (GSTrow != undefined)
                if (GSTrate > 0) {
                    var GSTamount = Math.round(GSTrate * subtotal) / 100;
                    subtotal += GSTamount;
                    $(GSTrow).children('.itemTitle').text('GST ' + GSTrate + '%');
                    $(GSTrow).children('.itemValue').text(sprintf('$%01.2f', GSTamount));
                    $(GSTrow).show();
                } else
                    $(GSTrow).hide();
            if (OverallDiscRow != undefined) {
                if (OverallDiscPercent > 0) {
                    var DiscAmt = Math.round(OverallDiscPercent * subtotal) / 100;
                    subtotal -= DiscAmt;
                    $(OverallDiscRow).children('.itemTitle').text('Overall Discount of ' + OverallDiscPercent + '%');
                    $(OverallDiscRow).children('.itemValue').text(sprintf('-$%01.2f', DiscAmt));
                    $(OverallDiscRow).show();
                } else if (OverallDiscAmt > 0) {
                    var DiscAmt = Math.round(OverallDiscAmt * 100) / 100;
                    subtotal -= DiscAmt;
                    $(OverallDiscRow).children('.itemTitle').text(sprintf('Overall Discount of $%01.2f', DiscAmt));
                    $(OverallDiscRow).children('.itemValue').text(sprintf('-$%01.2f', DiscAmt));
                    $(OverallDiscRow).show();
                } else
                    $(OverallDiscRow).hide();
            }
            if (TotalRow != undefined) {
                $(TotalRow).children('.itemTitle').text('Total');
                $(TotalRow).children('.itemValue').text(sprintf('$%01.2f', subtotal));
            }
        };
        
        remarks = (receiptRemarks == '' ? '' : '<span id="receiptRemarks">Remarks: ' + receiptRemarks + '</span>');
        
        $('#contentarea > table:first').before('<span class="info"><span id="receiptTime">' + receiptTime + '</span><br />' + 
                                                '<span id="restName">' + restName + '</span><br />' +
                                                '<span id="restAdd">' + restAdd + '</span><br />' +
                                                '<span id="receiptTable">Table ' + receiptTable + '</span><br />'
                                                + remarks + '</span>');
        $('#contentarea > table:first > thead > tr:last').append('<th colspan="3" class="options">Discounts</th>').children().each(function (idx, Elem) {
            $(Elem).attr('id', headerIDs[idx]);
        });
        
        var rowCount = $('#contentarea > table:first > tbody > tr').length; 
        
        TotalRow = $('#contentarea > table:first > tbody').append('<tr><td colspan="3" class="itemTitle"></td><td class="itemValue"></td></tr>').children().last();
        SCrow = $(TotalRow).before('<tr><td colspan="3" class="itemTitle"></td><td class="itemValue"></td><td colspan="3" class="options"></td></tr>').prev();
        GSTrow = $(TotalRow).before('<tr><td colspan="3" class="itemTitle"></td><td class="itemValue"></td><td colspan="3" class="options"></td></tr>').prev();
        OverallDiscRow = $(TotalRow).before('<tr><td colspan="3" class="itemTitle"></td><td class="itemValue"></td><td colspan="3" class="options"></td></tr>').prev();
        
        $('#contentarea > table:first > tbody > tr').each(function (idx, Elem) {
            
            if (idx >= rowCount && idx != rowCount + 3)
                return;
            
            var totalRow = (idx == rowCount + 3);
            
            var amtCell = $(Elem).children().last();
            
            var amt = parseFloat($(amtCell).text().replace('$', ''));
            if (!totalRow)
                $(Elem).attr('data-origAmt', amt).attr('data-finalAmt', amt);
            var cellCount = $(Elem).children().length;
            
                            
            var discountRow;
            if (totalRow)
                discountRow = $(Elem).prev();
            else
                discountRow = $(Elem).after('<tr><td colspan="' + (cellCount - 1) + '"></td></tr>').next();
            $(discountRow).hide();
            
            var discountText = $(discountRow).children().first();
            
            if (!totalRow)
                $(discountRow).append('<td></td>');
            var discounted = $(discountRow).children().last();
            
            if (!totalRow)
                $(discountRow).append('<td colspan="3" class="options"></td>')
            
            var i;
            for (i = 0; i < amountDisc.length; ++i)
                $(discountRow).children().last().append((i ? ' / ' : '') + '<a>' + sprintf('$%01.2f', amountDisc[i]) + '</a>').children('a').last().click((function (amtDisc) {
                    return function () {
                        $(amtBox).val(amtDisc).change();
                    };
                })(amountDisc[i])).css('cursor', 'pointer');
    
            for (i = 0; i < percentDisc.length; ++i)
                $(discountRow).children().last().append(' / <a>' + sprintf('%01f%%', percentDisc[i]) + '</a>').children('a').last().click((function (pcntDisc) {
                    return function () {
                        $(percentBox).val(pcntDisc).change();
                    };
                })(percentDisc[i])).css('cursor', 'pointer');
            
            $(discountRow).children().last().append(' /<br />$<input size="2" /> ');
            var amtBox = $(discountRow).children().last().children('input').last();
            
            $(discountRow).children().last().append(' / <input size="2" />%');
            var percentBox = $(discountRow).children().last().children('input').last();
            $(percentBox).css('text-align', 'right');
            
            $(amtBox).change((function (totalRow) {
                return function () {
                    var val = parseFloat($(amtBox).val().trim());
                    if (isNaN(val) || val == 0) {
                        $(this).val('-');
                        if (totalRow)
                            OverallDiscAmt = 0;
                        else if (isNaN($(percentBox).val())) {
                            $(Elem).attr('data-finalAmt', $(Elem).attr('data-origAmt'));
                            $(discountText).text('');
                            $(discounted).text('');
                        }
                    } else {
                        $(percentBox).val('-');
                        if (val > amt)
                            $(amtBox).val(sprintf('%01.2f', amt));
                        else if (val < 0)
                            $(amtBox).val('0.00');
                        var calcDisc = Math.round(parseFloat($(amtBox).val()) * 100) / 100;
                        if (totalRow) {
                            OverallDiscAmt = calcDisc;
                            OverallDiscPercent = 0;
                        } else {
                            $(discountText).text(sprintf('Discount of $%01.2f', calcDisc));
                            $(discounted).text(sprintf('-$%01.2f', calcDisc));
                            $(Elem).attr('data-finalAmt', $(Elem).attr('data-origAmt') - calcDisc);
                        }
                    }
                    updateAmts();
                }
            })(totalRow));
            
            $(percentBox).change((function (totalRow) {
                return function () {
                    var val = $(percentBox).val();
                    if (isNaN(val) || val == 0) {
                        $(this).val('-');
                        if (totalRow)
                            OverallDiscPercent = 0;
                        else if (isNaN($(amtBox).val())) {
                            $(Elem).attr('data-finalAmt', $(Elem).attr('data-origAmt'));
                            $(discountText).text('');
                            $(discounted).text('');
                        }
                    } else {
                        $(amtBox).val('-');
                        if (val > 100)
                            $(percentBox).val(100);
                        else if (val < 0)
                            $(percentBox).val(0);
                        var discPercent = parseFloat($(percentBox).val());
                        var calcDisc = Math.round(discPercent * amt) / 100;
                        if (totalRow) {
                            OverallDiscPercent = discPercent;
                            OverallDiscAmt = 0;
                        } else {
                            $(discountText).text(sprintf('Discount of %01f%%', discPercent));
                            $(discounted).text(sprintf('-$%01.2f', calcDisc));
                            $(Elem).attr('data-finalAmt', $(Elem).attr('data-origAmt') - calcDisc);
                        }
                    }
                    updateAmts();
                }
            })(totalRow));
            
            $(Elem).append('<td class="options"><a>Add</a></td>');
            $(Elem).children().last().click(function () {
                $(discountRow).show('fast');
            }).css('cursor', 'pointer');
            
            $(Elem).append('<td class="options"><a>Remove</a></td>');
            $(Elem).children().last().click(function () {
                $(amtBox).val('-');
                $(percentBox).val('-').change();
                $(discountRow).hide('fast');
            }).css('cursor', 'pointer');
            
            $(Elem).append('<td class="options"><a>FREE</a></td>');
            $(Elem).children().last().click (function () {
                $(discountRow).show('fast');
                $(percentBox).val(100).change();
            }).css('cursor', 'pointer');
        });
        
        updateAmts();
    });
}