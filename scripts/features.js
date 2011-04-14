/**
 * Features Listing script file
 * @author Patrick
 */


/**
 * Handle Features Listing
 * @param   featureSelect   Select Form Item that has all the Features as its options
 * @param   featureTable    ID of table to use for displaying selected features
 * @param   curValues       2D Array of current values. Index 0 should correspond to Feature ID, Index 1 the current Value, and Index 2 the Fixed flag (currently unused)
 * @param   allowableRanges Array of either Maximum values for numeric features, or semi-colon delimited String Values 
 */
function handleFeatures (featureSelect, featureTable, curValues, allowableRanges) {
    var count = 0;
    addToFeatures = function(id, value) {
        if (id == undefined || isNaN(id))
            return;
        var name = $('#' + featureSelect + ' option[value="' + id + '"]').attr('disabled', 'disabled').text();
        
        var range = allowableRanges[id];
        var hasOptions = typeof(range) == 'string';
        var allowed;
        if (hasOptions)
            allowed = range.split(';');
        
        $('#' + featureTable + ' > tbody:last').append('<tr id="listFeature' + id + '"><td class="listFeatureRemove"></td><td class="listFeatureName"></td><td class="listFeatureValue"></td></tr>');
        $('#listFeature' + id + ' > .listFeatureRemove').text('Remove').css('cursor', 'pointer').click(function () {
            removeFromFeatures(id);
          });
        $('#listFeature' + id + ' > .listFeatureName').text(name.trim());
        $('#listFeature' + id + ' > .listFeatureValue').append('<input name="features[' + count + '][FeatureID]" type="hidden" value="' + id + '"><select name="features[' + count + '][Value]">');
        var selbox = $('#listFeature' + id + ' > .listFeatureValue > select:last');
        
        var numOptions = hasOptions ? allowed.length : range + 1;
        for (i = 0; i < numOptions; ++i) {
            selbox.append('<option></option>').children('option:last').attr('value', i).text(hasOptions ? allowed[i] : i);
            if (i == value)
                selbox.children('option:last').attr('selected', 'selected');
        }
        
        
        ++count;
    };
    
    removeFromFeatures = function(id) {
        $('#listFeature' + id).remove();
        $('#' + featureSelect + ' option[value="' + id + '"]').removeAttr('disabled');
    };
    
    $(document).ready(function() {
        for (i = 0; i < curValues.length; ++i)
            addToFeatures(curValues[i][0], curValues[i][1], curValues[i][2]);
        $('#' + featureSelect).change(function() {
            var selected = $('#' + featureSelect + ' option:selected');
            selected.removeAttr('selected');
            addToFeatures(selected.val(), 0);
        });
    });
}