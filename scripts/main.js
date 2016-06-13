$(window).ready(function (){
    
    $(".addChild").click(function() {
        // pagrindinis vaikinių įrašų konteineris
        childRowContainer = $(this).parent().prev(".childRowContainer");
        
        if(childRowContainer.children(".childRow:last").hasClass("hidden")) { // jeigu nėra nei vienos eilutės
            // pašaliname paslėptos eilutės požymius
            childRowContainer.children(".childRow:last, .labelLeft, .labelRight").removeClass("hidden");
            childRowContainer.children(".childRow:last").children("input[type=text], select").prop("disabled", false);
        } else {
            // klonuojame vaikinio įrašo eilutę
            rowClone = childRowContainer.children(".childRow:last").clone(true, true);
            
            // pašaliname klonuotų įvedimo elementų reikšmes
            $(rowClone).children("input[type=text]").val("");
            $(rowClone).children("select").find('option').removeAttr("selected");
            
            // pašaliname išjungtų elementų požymius
            $(rowClone).children("input[type=text], select").removeClass('disabledInput');
            $(rowClone).children(".removeChild").removeClass('hidden');
            $(rowClone).children("input.isDisabledForEditing").val(0);
            
            // klonuotą eilutę įtraukiame į pagrindinį vaikinių įrašų konteinerį
            rowClone.appendTo(childRowContainer);

            // sukuriame pagalbinį <div class="float-clear"></div> elementą formatavimui
            clearDiv = $('<div />', {"class": 'float-clear'});
            clearDiv.appendTo(childRowContainer);
        }
        
        return false;
    })

    $(".removeChild").click(function() {
        // pagrindinis vaikinių įrašų konteineris
        childRowContainer = $(this).parent().parent(".childRowContainer");
        
        if(childRowContainer.children('.childRow').size() > 1) {
            $(this).parent().next(".float-clear").remove();
            $(this).parent().remove();       
        } else { // paskutinės eilutės nepašaliname, bet paslepiame
            childRowContainer.children('.childRow, .labelLeft, .labelRight').addClass("hidden");
            childRowContainer.children(".childRow").children("input[type=text], select").prop("disabled", true);
        }
        
        return false;
    })
    
    // Datos ir laiko įskiepių nustatymas
    $.datetimepicker.setLocale('lt');
    $('.datetime').datetimepicker({
        format:'Y-m-d H:i',
        dayOfWeekStart : 1,
        startDate: '2016-01-01',
        defaultDate: '2016-01-01'
    });
    
    $('.date').datetimepicker({
        yearOffset:222,
        timepicker:false,
        format:'Y-m-d',
        formatDate:'Y-m-d',
        defaultDate: '2016-01-01'
    });
    
});

function showConfirmDialog(module, removeId) {
    var r = confirm("Ar tikrai norite pašalinti!");
    if (r === true) {
        window.location.replace("index.php?module=" + module + "&remove=" + removeId);
    }
}