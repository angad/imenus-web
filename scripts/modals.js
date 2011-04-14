/**
 * Modal Prompts script file
 * Uses http://simplemodal.googlecode.com/files/jquery.simplemodal.1.4.1.min.js
 * @author Patrick
 * EFFECTS: a.modalconfirm links will have modal prompts based on their data-modaltext property.
 *          Prompts will use #modalprompt for the prompt, #modaltext for the text, and #modalconfirm for the confirm button
 *          img.zooming images will have modal prompts that zoom in on the image when clicked 
 */
$(document).ready(function () {
    $("a.modalconfirm").click(function(e) {
        e.preventDefault();
        $("#modalconfirm").attr('data-href', $(this).attr('href'));
        $("#modaltext").text($(this).attr('data-modaltext'));
        $("#modalprompt").modal({
            overlayClose: true
        });
    });
    $("#modalconfirm").click(function() {
        $.modal.close();
        window.location.href = $(this).attr('data-href');
    });
    $("img.zooming").click(function() {
        $.modal("<img src='" + $(this).attr('src') + "'>", {overlayClose : true, overlayCss: {backgroundColor:"black"}});
    });
});