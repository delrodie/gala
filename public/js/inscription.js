/**
 * Formalisation du nombre de ticket
 *
 * @param number
 * @returns {number|*}
 */
function existence(number){
    if (number > 0) { return number;}
    else {return 1;}
}

function montantTotal()
{
    var ticket = document.getElementById("participant_nombreTicket").value;
    //var ticket = parseFloat(document.getElementById("formInscription").elements["participant_nombreTicket"].value.replace('%',''))

    let total = ticket * 51500; console.log(ticket);

    document.getElementById("participant_montantTotal").value = total;
}