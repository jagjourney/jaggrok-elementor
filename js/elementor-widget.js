// ============================================================================
// JAGJourney ELEMENTOR WIDGET JS v1.2.6 (WORKING MODAL POPUP)
// ============================================================================

(function($) {
    'use strict';

    // Wait for Elementor Editor
    $(window).on('elementor/editor/init', function() {
        initJagGrokModal();
    });

    function initJagGrokModal() {
        // Remove existing
        $('#jaggrok-ai-modal').remove();

        // Create Modal
        $('body').append(`
            <div id="jaggrok-ai-modal" class="jaggrok-modal" style="display: none;">
                <div class="jaggrok-modal-content">
                    <div class="jaggrok-modal-header">
                        <h3><i class="eicon-brain"></i> Write with JagGrok</h3>
                        <button class="jaggrok-modal-close">&times;</button>
                    </div>
                    <div class="jaggrok-modal-body">
                        <textarea id="jaggrok-prompt-input" placeholder="Describe your page..." rows="4" style="width: 100%; padding: 10px;"></textarea>
                        <br>
                        <button id="jaggrok-generate-btn" class="elementor-button elementor-button-success" style="width: 100%; margin: 10px 0;">
                            Generate with Grok
                        </button>
                        <div id="jaggrok-modal-output" style="margin-top: 15px; padding: 10px; background: #f1f3f5; border-radius: 3px;"></div>
                    </div>
                </div>
            </div>
        `);

        // Modal CSS
        $('<style>').text(`
            #jaggrok-ai-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; }
            .jaggrok-modal-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 500px; background: white; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
            .jaggrok-modal-header { padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
            .jaggrok-modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; }
            .jaggrok-modal-body { padding: 20px; }
            #jaggrok-prompt-input { font-family: inherit; border: 1px solid #ddd; border-radius: 3px; }
            #jaggrok-modal-output.success { color: green; }
            #jaggrok-modal-output.error { color: red; }
        `).appendTo('head');

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
                $output.html('<p class="error">Please enter a prompt!</p>');
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
                        $output.html('<p class="success">✅ Generated! <br><small>' + response.data.html.substring(0, 100) + '...</small></p>');
                        $('.jaggrok-widget-output').html(response.data.html);
                    } else {
                        $output.html('<p class="error">Error: ' + response.data + '</p>');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Generate Again');
                    $output.html('<p class="error">Connection failed!</p>');
                }
            });
        });
    }
})(jQuery);