/**
 * This js file includes in trainees page
 */
function validate_trainee_search_form() {
    search_company_trainee_name    = $("#search_company_trainee_name").val();
    search_company_trainee_taxcode = $("#search_company_trainee_taxcode").val();
    
    if(search_company_trainee_name == '' && search_company_trainee_taxcode == '') {     
        $("#validate_trainee_search_form_err").html('Please enter a trainee name or NRIC/FIN No.');
        return false;
    }
    else
    {
        $("#validate_trainee_search_form_err").html('');
        $("#searchCompanyTraineeForm").submit();
    }
}