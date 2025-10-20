// ============================================================================
// JAGJourney ADMIN SETTINGS JS v1.0.0
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
                status.html('<span style="color:red">❌ ' + response.data + '</span>');
            }
        });
    });
});