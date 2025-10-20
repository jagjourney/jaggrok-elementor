// ============================================================================
// JAGJourney ELEMENTOR WIDGET JS v1.2.5 ( "Write with AI" POPUP MODAL)
// ============================================================================

jQuery(document).ready(function($) {
    // "Write with AI" Link Click - Open Modal (Like Elementor)
    $(document).on('click', '.jaggrok-write-ai', function(e) {
        e.preventDefault();
        var $widget = $(this).closest('.elementor-widget');
        var widgetId = $widget.data('id');

        // Create/Show Modal (Elementor-style popup)
        if (!$('#jaggrok-ai-modal').length) {
            $('body').append(`
                <div id="jaggrok-ai-modal" class="elementor-modal">
                    <div class="dialog-widget-content">
                        <div class="dialog-header">
                            <h2>Write with AI</h2>
                            <button class="dialog-close-button">&times;</button>
                        </div>
                        <div class="dialog-content">
                            <textarea id="jaggrok-prompt-modal" placeholder="Describe what you want to generate..." rows="4"></textarea>
                            <button class="jaggrok-generate-modal-btn elementor-button elementor-button-success">Generate with Grok</button>
                            <div id="jaggrok-modal-output"></div>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#jaggrok-ai-modal').fadeIn(200);

        // Close Modal
        $('.dialog-close-button').on('click', function() {
            $('#jaggrok-ai-modal').fadeOut(200);
        });

        // Generate in Modal
        $('.jaggrok-generate-modal-btn').off('click').on('click', function() {
            var prompt = $('#jaggrok-prompt-modal').val();
            if (!prompt) return alert('Enter a prompt!');

            $('#jaggrok-modal-output').html('<p>🤖 Generating...</p>');
            $(this).prop('disabled', true);

            $.ajax({
                url: jaggrokAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'jaggrok_generate_page',
                    prompt: prompt,
                    nonce: jaggrokAjax.nonce
                },
                success: function(response) {
                    $('.jaggrok-generate-modal-btn').prop('disabled', false);
                    if (response.success) {
                        $('#jaggrok-modal-output').html('<p>Success! Generated: ' + response.data.html.substring(0, 200) + '...</p>');
                        // Insert to widget (like Elementor)
                        $('#jaggrok-output-' + widgetId).html(response.data.html);
                    } else {
                        $('#jaggrok-modal-output').html('<p style="color:red">Error: ' + response.data + '</p>');
                    }
                }
            });
        });
    });

    // Close on outside click
    $(document).on('click', function(e) {
        if ($(e.target).is('#jaggrok-ai-modal')) {
            $('#jaggrok-ai-modal').fadeOut(200);
        }
    });
});
// Add CSS for Modal (Elementor-Style)
$('<style>').text(`
    #jaggrok-ai-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; }
    #jaggrok-ai-modal .dialog-widget-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 500px; background: white; border-radius: 5px; }
    #jaggrok-ai-modal .dialog-header { padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; }
    #jaggrok-ai-modal .dialog-content { padding: 20px; }
    #jaggrok-ai-modal textarea { width: 100%; margin-bottom: 10px; }
    #jaggrok-ai-modal .elementor-button { width: 100%; margin-bottom: 10px; }
    .dialog-close-button { background: none; border: none; font-size: 24px; cursor: pointer; }
`).appendTo('head');