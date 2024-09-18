function focusInput(focusWas, focusTo, event){
    var eventKey = event.key;
    if(eventKey == "Enter"){
        document.querySelector(focusTo).focus();
    }
    else if(eventKey == "Escape"){
        if(focusWas !== ""){
            document.querySelector(focusWas).focus();
        }
        else{
            return;
        }
    }
}

