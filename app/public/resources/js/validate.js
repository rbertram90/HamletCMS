/**
--- function validate ---

@description Validates forms client-side by passing in a JSON string describing the constraints to be applied to the field.
@param - fld <object> the DOM object to be validated
@param - options <object> JSON formatted object with the following options:

    * fieldlength <int>
    * email <boolean>
    * password <boolean>
    
@return - <boolean> does the field meet all requirements set - true if valid
**/
function validate(fld,options) {
    // Default to valid
    var res = true;
    
    if(options.fieldlength) {
        // Character count must be MORE THAN options.fieldlength
        if(fld.value.length < options.fieldlength) res = false;
    }
    
    if(options.greaterthan) {
        // Value must be MORE THAN options.greaterthan
        if(0 + fld.value <= 0 + options.greaterthan) res = false;
    }
    
    if(options.lessthan) {
        // Value must be LESS THAN options.lessthan
        if(0 + fld.value >= 0 + options.lessthan) res = false;
    }
    
    if(options.email) {
        // Validate e-mail
        
        // Email address matching RegExp
        var regExpr = /([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]{2,4})/;
        
        // Check against entered characters
        if(!regExpr.exec(fld.value)) res = false;
    }
    
    if(options.password) {
        // Check password strength
        // Must be at least 8 characters
        if(fld.value.length < 8) res = false;
    }
    
    // Apply style
    if(!res) {
        fld.style.color = "#c00";
        fld.style.borderColor = "#c00";
        // Set the valid attribute of the form
        $(fld).attr("data-valid","false");
    } else {
        fld.style.color = "#000";
        fld.style.borderColor = "#999999";
        $(fld).attr("data-valid","true");
    }
}

// Check form checks all the input fields in the form,
// note that if nothing has been entered this will not work!
function checkForm(frm) {
    // Convert form to jQuery element
    $form = $(frm);
    
    // Set switch
    lbFrmValid = true;
    
    // Loop through all inputs
    $form.find(":input").each(function(index) {
        
        // Is the data valid? (boolean t/f)
        lbValid = $(this).attr("data-valid");
        
        if(lbValid == "false") {
    
            // Found a field which is not valid
            $(this).css("border","1px solid #c00");
            if ($(this).attr("data-notValidText") !== undefined) alert($(this).attr("data-notValidText"));
            lbFrmValid = false;
            
            // Exit .each loop
            return false;
        }
    });
    
    // Don't submit form if not valid
    if(!lbFrmValid)
        return false;
}