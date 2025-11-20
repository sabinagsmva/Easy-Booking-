

$(document).ready(function() {

    function enableAutocomplete(selector, fieldName) {
        $(selector).autocomplete({
            minLength: 1,
            source: function(request, response) {
                $.ajax({
                    url: "autocomplete_api.php",
                    type: "GET",
                    dataType: "json",
                    data: {
                        term: request.term,
                        field: fieldName
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            }
        });
    }

    // Hotel search
    enableAutocomplete("#city", "city");
    enableAutocomplete("#hotel_name", "hotel_name");

    // Customer search
    enableAutocomplete("#customer_name", "customer_name");
    enableAutocomplete("#email", "email");

    // Booking search
    enableAutocomplete("#room_type", "room_type");
});
