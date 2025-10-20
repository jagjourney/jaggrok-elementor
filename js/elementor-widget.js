// ============================================================================
// JAGJourney ELEMENTOR WIDGET JS v1.2.7 (WIDGET + POPUP 100%)
// ============================================================================

jQuery(document).ready(function($) {
    // Create Modal ONCE
    if (!$('#jaggrok-ai-modal').length) {
        $('body').append(`
            <div id="jaggrok-ai-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 500px; background: white; border-radius: 5px;">
                    <div style="padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between;">
                        <h3><i class="eicon-brain"></i> Write with JagGrok</h3>
                        <button class="jaggrok-modal-close" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                    </div>
                    <div style="padding: 20px;">
                        <textarea id="jaggrok-prompt-input" placeholder="Describe your page..." rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 3px;"></textarea>
                        <br>
                        <button id="jaggrok-generate-btn" class="elementor-button elementor-button-success" style="width: 100%; margin: 10px 0;">Generate with Grok</button>
                        <div id="jaggrok-modal-output" style="margin-top: 15px; padding: 10px; background: #f1f3f5; border-radius: 3px;"></div>
                    </div>
                </div>
            </div>
        `);
    }

    // CLICK "Write with JagGrok" - OPEN MODAL
    $(document).on('click', '.jaggrok-write-ai-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#jaggrok-ai-modal').show();
        $('#jaggrok-prompt-input').focus();
    });

    // CLOSE MODAL
    $(document).on('click', '#jaggrok-ai-modal, .jaggrok-modal-close', function(e) {
        if (e.target === this) $('#jaggrok-ai-modal').hide();
    });

    // GENERATE BUTTON
    $(document).on('click', '#jaggrok-generate-btn', function() {
        var prompt = $('#jaggrok-prompt-input').val().trim();
        var $btn = $(this);
        var $output = $('#jaggrok-modal-output');

        if (!prompt) {
            $output.html('<p style="color:red">Please enter a prompt!</p>');
            return;
        }

        $btn.prop('disabled', true).text('Generating...');
        $output.html('<p>🤖 Generating with JagGrok...</p>');

        $.ajax({
            url: jaggrokAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'jaggrok_generate_page',
                prompt: prompt,
                nonce: jaggrokAjax.nonce
            },
            success: function(response) {
                $btn.prop('disabled', false).text('Generate Again');
                if (response.success) {
                    $output.html('<p style="color:green">✅ Generated! <br><small>' + response.data.html.substring(0, 100) + '...</small></p>');
                    $('.jaggrok-widget-output').html(response.data.html);
                } else {
                    $output.html('<p style="color:red">Error: ' + response.data + '</p>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Generate Again');
                $output.html('<p style="color:red">Connection failed!</p>');
            }
        });
    });
});