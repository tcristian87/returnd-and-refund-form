
jQuery(document).ready(function ($) {
 
        $('#refund-return-form').submit(function(e) {
         
            $('#return-submit-button').prop('disabled', true);
            e.preventDefault();

            clearErrors();
    
            var isValid = rarf_validateForm();
    
            
            if (isValid) {
                $('#is_ajax').val('1');
                let refundData = $(this).serializeArray();
                
                $.ajax({
                    url: rarf_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'return_form_handler',
                        return_form_nonce: $('input[name="return_form_nonce"]').val(),
                        formData: refundData
                    },
                    success: function(response) {
                        $('#return-submit-button').prop('disabled', false);
                        if (response.success) {
                            $('.refund-form-section').append('<p class="success-message">We will contact you to resolve the return request </p>');
                            $('#refund-return-form').remove();
                            $('.refund-form-section').append('<a href="'+ window.location.origin +'">Home</a>')
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#return-submit-button').prop('disabled', false);
                        alert('There was an error submitting the form.');
                        console.log(error);
                    }
                });
            } else {
                $('#return-submit-button').prop('disabled', false);
            }
        });
    
        function rarf_validateForm() {
            var isValid = true;
            var validationRules = [
                { selector: '#order_number', message: 'Please enter the order number.', validate: value => value.trim() !== '' },
                { selector: '#full_name', message: 'Please enter the full name.', validate: value => value.trim() !== '' },
                { selector: '#email', message: 'Please enter a valid email address.', validate: validateEmail },
                { selector: '#phone', message: 'Please enter the phone number.', validate: value => value.trim() !== '' },
                { selector: '#products', message: 'Please describe the return reason', validate: value => value.trim() !== '' },
                { selector: '.terms-and-conditions-form', message: 'You must agree to the privacy policy.', validate: () => {
                    var element = $('#privacy_policy');
                    return element.length === 0 || element.is(':checked');
                }}
            ];

            validationRules.forEach(function(rule) {
                var value = $(rule.selector).val();
                if (!rule.validate(value)) {
                    showError(rule.selector, rule.message);
                    isValid = false;
                }
            });
           
                return isValid;
        }
    
        function showError(selector, message) {
            $(selector).after('<span class="error-message">' + message + '</span>');
        }
    
        function clearErrors() {
            $('.error-message').remove();
        }
    
        function validateEmail(email) {
            var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            return re.test(String(email).toLowerCase());
        }
        
});