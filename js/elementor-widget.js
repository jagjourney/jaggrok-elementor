jQuery(document).on('elementor/init', function() {
    // Bind to "Write with JagGrok" button event
    elementor.hooks.addAction( 'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event', function( controlView ) {
        // Create modal if not exists
        if (!$('#jaggrok-modal').length) {
            $('body').append(`
                <div id="jaggrok-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:500px;background:white;border-radius:5px;box-shadow:0 5px 15px rgba(0,0,0,0.3);">
                        <div style="padding:20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
                            <h3 style="margin:0;"><i class="eicon-brain"></i> Write with JagGrok</h3>
                            <button onclick="$('#jaggrok-modal').hide()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;">&times;</button>
                        </div>
                        <div style="padding:20px;">
                            <textarea id="jaggrok-prompt" placeholder="Describe your page (e.g. Create a hero section with blue button)" rows="4" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:3px;font-family:inherit;"></textarea>
                            <br>
                            <button id="jaggrok-generate" class="button button-primary" style="width:100%;margin:10px 0;padding:10px;">Generate with Grok</button>
                            <div id="jaggrok-result" style="margin-top:15px;padding:10px;background:#f1f3f5;border-radius:3px;"></div>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#jaggrok-modal').show();
        $('#jaggrok-prompt').focus();

        // Generate button
        $('#jaggrok-generate').off('click').on('click', function() {
            var prompt = $('#jaggrok-prompt').val().trim();
            var $btn = $(this);
            var $result = $('#jaggrok-result');

            if (!prompt) {
                $result.html('<p style="color:red">Please enter a prompt!</p>');
                return;
            }

            $btn.prop('disabled', true).text('Generating...');
            $result.html('<p>🤖 Generating with JagGrok...</p>');

            $.post(jaggrokAjax.ajaxurl, {
                action: 'jaggrok_generate_page',
                prompt: prompt,
                nonce: jaggrokAjax.nonce
            }, function(response) {
                $btn.prop('disabled', false).text('Generate Again');
                if (response.success) {
                    $result.html('<p style="color:green">✅ Generated!<br><small>' + response.data.html.substring(0,100) + '...</small></p>');
                    $('.jaggrok-output').html(response.data.html);
                } else {
                    $result.html('<p style="color:red">Error: ' + response.data + '</p>');
                }
            });
        });
    });
});