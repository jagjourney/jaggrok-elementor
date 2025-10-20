// ============================================================================
// JAGJourney ELEMENTOR WIDGET JS v1.2.3 (BUTTON CLICK FIXED)
// ============================================================================

jQuery(document).ready(function($) {
    // Generate button click handler
    $(document).on('click', '.jaggrok-generate-btn', function() {
        var $btn = $(this);
        var widgetId = $btn.attr('id').replace('jaggrok-btn-', '');
        var prompt = $('#jaggrok-prompt-' + widgetId).val();
        var $output = $('#jaggrok-output-' + widgetId);

        if (!prompt) {
            $output.html('<p style="color:red">Please enter a prompt!</p>');
            return;
        }

        $btn.prop('disabled', true).html('<i class="eicon-loading"></i> Generating...');
        $output.html('<p>🤖 Generating with Grok AI...</p>');

        $.ajax({
            url: jaggrokAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'jaggrok_generate_page',
                prompt: prompt,
                nonce: jaggrokAjax.nonce
            },
            success: function(response) {
                $btn.prop('disabled', false).html('<i class="eicon-brain"></i> Generate Again');
                if (response.success) {
                    $output.html('<div class="jaggrok-success">' + response.data.html + '</div>');
                } else {
                    $output.html('<p style="color:red">Error: ' + response.data + '</p>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="eicon-brain"></i> Generate Again');
                $output.html('<p style="color:red">Connection failed!</p>');
            }
        });
    });
});