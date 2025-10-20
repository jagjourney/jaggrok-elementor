// ============================================================================
// JAGJourney ELEMENTOR WIDGET JS v1.2.0 (REAL AI GENERATION)
// ============================================================================

jQuery(document).on('elementor/init', function() {
    // Generate button handler
    elementor.hooks.addAction( 'panel/widgets/jaggrok-ai-generator/controls/generate_button/event', function( controlView ) {
        var settings = controlView.container.settings;
        var prompt = settings.get( 'prompt' );
        var proFeatures = settings.get( 'pro_features' ) || 'no';

        // Show loading
        controlView.$el.find( '.jaggrok-generated-content' ).html( '<p>🤖 Generating with Grok...</p>' );

        jQuery.ajax({
            url: jaggrokAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'jaggrok_generate_page',
                prompt: prompt,
                pro_features: proFeatures,
                nonce: jaggrokAjax.nonce
            },
            success: function( response ) {
                if ( response.success ) {
                    if ( response.data.canvas_json ) {
                        // Pro: Insert Elementor JSON
                        elementorFrontend.elementsHandler.addElements( response.data.canvas_json );
                    } else {
                        // Free: Insert HTML
                        controlView.$el.find( '.jaggrok-generated-content' ).html( response.data.html );
                    }
                } else {
                    controlView.$el.find( '.jaggrok-generated-content' ).html( '<p style="color:red">Error: ' + response.data + '</p>' );
                }
            }
        });
    });
});