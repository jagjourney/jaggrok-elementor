// ============================================================================
// JAGJourney ADMIN SETTINGS JS v1.3.6
// ============================================================================

jQuery(document).ready(function($) {
    $('#jaggrok-test-api').click(function() {
        var apiKey = $('#jaggrok_xai_api_key').val();
        var status = $('#jaggrok-api-status');

        if (!apiKey) return status.html('<span style="color:red">Enter API key!</span>');

        status.html('Testing...');
        $.post(jaggrokAjax.ajaxurl, {
            action: 'jaggrok_test_api',
            api_key: apiKey,
            nonce: jaggrokAjax.nonce
        }, function(response) {
            if (response.success) {
                status.html('<span style="color:green">✅ Connected!</span>');
                location.reload();
            } else {
                var message = response && response.data ? response.data : 'Unknown error';
                if (typeof message === 'object' && message !== null) {
                    message = message.message || 'Unknown error';
                }
                status.html('<span style="color:red">❌ ' + message + '</span>');
            }
        });
    });
});