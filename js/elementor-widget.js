jQuery(document).on('elementor/init', function() {
    var providerLabels = (typeof jaggrokAjax !== 'undefined' && jaggrokAjax.providerLabels) ? jaggrokAjax.providerLabels : {};
    var providerSummaries = (typeof jaggrokAjax !== 'undefined' && jaggrokAjax.providerSummaries) ? jaggrokAjax.providerSummaries : {};
    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    var providerDefaults = {
        grok: {
            label: providerLabels.grok || 'xAI Grok',
            icon: '🚀',
            summary: providerSummaries.grok || 'Content generated with xAI Grok.'
        },
        openai: {
            label: providerLabels.openai || 'OpenAI',
            icon: '🔷',
            summary: providerSummaries.openai || 'Content generated with OpenAI.'
        }
    };
    var providerMap = Object.assign({}, providerDefaults);
    if (providerLabels && Object.keys(providerLabels).length) {
        Object.keys(providerLabels).forEach(function(key) {
            if (!providerMap[key]) {
                providerMap[key] = {
                    label: providerLabels[key],
                    icon: '🤖',
                    summary: providerSummaries[key] || ('Content generated with ' + providerLabels[key] + '.')
                };
            } else {
                providerMap[key].label = providerLabels[key];
                providerMap[key].summary = providerSummaries[key] || providerMap[key].summary;
            }
        });
    }
    window.JagGrokProviders = Object.assign({}, providerMap, window.JagGrokProviders || {});

    function getProviderMeta(key) {
        var meta = window.JagGrokProviders && window.JagGrokProviders[key];
        if (!meta) {
            return {
                label: key,
                icon: '🤖',
                summary: 'Content generated with ' + key + '.'
            };
        }
        return meta;
    }

    function buildProviderOptions(defaultProvider) {
        var optionsHtml = '';
        var providers = window.JagGrokProviders || {};
        var keys = Object.keys(providers);
        if (!keys.length) {
            keys = Object.keys(providerDefaults);
            providers = providerDefaults;
        }
        keys.forEach(function(key, index) {
            var meta = providers[key] || {};
            var icon = meta.icon ? '<span class=\"jaggrok-provider-icon\" aria-hidden=\"true\">' + meta.icon + '</span>' : '';
            var label = escapeHtml(meta.label || key);
            var valueAttr = escapeHtml(key);
            var isChecked = (key === defaultProvider) || (!defaultProvider && index === 0);
            var checkedAttr = isChecked ? ' checked' : '';
            var iconHtml = icon || '';
            optionsHtml += `
                                <label class=\"jaggrok-provider-option\">
                                    <input type=\"radio\" name=\"jaggrok-modal-provider\" value=\"${valueAttr}\"${checkedAttr}>
                                    ${iconHtml}
                                    <span class=\"jaggrok-provider-name\">${label}</span>
                                </label>`;
        });
        return optionsHtml;
    }

    function updateModalProviderSummary(provider) {
        var meta = getProviderMeta(provider);
        $('#jaggrok-provider-active-icon').text(meta.icon || '🤖');
        $('#jaggrok-provider-active-label').text(meta.label || provider);
        $('#jaggrok-provider-summary').text(meta.summary || '');
        $('#jaggrok-generate').text('Generate with ' + (meta.label || provider));
    }

    function extractResponseSummary(response, fallbackProvider) {
        var providerKey = (response && response.data && response.data.provider) ? response.data.provider : fallbackProvider;
        var meta = getProviderMeta(providerKey);
        if (response && response.data && response.data.provider_label) {
            return 'Content generated with ' + response.data.provider_label + '.';
        }
        return meta.summary || ('Content generated with ' + (meta.label || providerKey) + '.');
    }

    function getDefaultProvider() {
        if (typeof jaggrokAjax !== 'undefined' && jaggrokAjax.provider) {
            return jaggrokAjax.provider;
        }
        var providers = window.JagGrokProviders || {};
        var keys = Object.keys(providers);
        return keys.length ? keys[0] : 'grok';
    }

    // Bind to "Write with JagGrok" button event
    elementor.hooks.addAction( 'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event', function( controlView ) {
        // Create modal if not exists
        if (!$('#jaggrok-modal').length) {
            var defaultProvider = getDefaultProvider();
            var providerOptions = buildProviderOptions(defaultProvider);
            $('body').append(`
                <div id="jaggrok-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:500px;background:white;border-radius:5px;box-shadow:0 5px 15px rgba(0,0,0,0.3);">
                        <div style="padding:20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
                            <h3 style="margin:0;"><i class="eicon-brain"></i> Write with JagGrok</h3>
                            <button onclick="$('#jaggrok-modal').hide()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;">&times;</button>
                        </div>
                        <div style="padding:20px;">
                            <div class="jaggrok-modal-provider" style="margin-bottom:12px;">
                                <span style="display:block;font-weight:600;margin-bottom:6px;">Choose provider</span>
                                <div class="jaggrok-provider-toggle" role="radiogroup">
                                    ${providerOptions}
                                </div>
                                <div class="jaggrok-provider-active" style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                                    <span id="jaggrok-provider-active-icon" aria-hidden="true"></span>
                                    <strong id="jaggrok-provider-active-label"></strong>
                                </div>
                                <p id="jaggrok-provider-summary" style="margin:6px 0 0; font-size:13px;"></p>
                            </div>
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
        var defaultProvider = getDefaultProvider();
        var $providerRadios = $('input[name="jaggrok-modal-provider"]');
        if ($providerRadios.length && !$providerRadios.filter(':checked').length) {
            $providerRadios.filter('[value="' + defaultProvider + '"]').prop('checked', true);
        }
        var activeProvider = $providerRadios.filter(':checked').val() || defaultProvider;
        updateModalProviderSummary(activeProvider);

        $providerRadios.off('change').on('change', function() {
            updateModalProviderSummary($(this).val());
        });

        // Generate button
        $('#jaggrok-generate').off('click').on('click', function() {
            var prompt = $('#jaggrok-prompt').val().trim();
            var $btn = $(this);
            var $result = $('#jaggrok-result');
            var provider = $('input[name="jaggrok-modal-provider"]:checked').val() || getDefaultProvider();

            if (!prompt) {
                $result.html('<p style="color:red">Please enter a prompt!</p>');
                return;
            }

            $btn.prop('disabled', true).text('Generating...');
            var providerMeta = getProviderMeta(provider);
            $result.html('<p>🤖 Generating with ' + (providerMeta.label || provider) + '...</p>');

            $.post(jaggrokAjax.ajaxurl, {
                action: 'jaggrok_generate_page',
                prompt: prompt,
                provider: provider,
                nonce: jaggrokAjax.nonce
            }, function(response) {
                $btn.prop('disabled', false).text('Generate Again');
                if (response.success) {
                    var summary = extractResponseSummary(response, provider);
                    if (response.data.canvas_json) {
                        elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                        $result.html('<p style="color:green">✅ ' + summary + '</p>');
                        $('.jaggrok-output').html('<p class="jaggrok-provider-message">' + summary + '</p>');
                    } else {
                        var snippet = '';
                        if (response.data.html) {
                            snippet = response.data.html.substring(0, 100) + '...';
                        }
                        var snippetHtml = snippet ? '<br><small>' + snippet + '</small>' : '';
                        $result.html('<p style="color:green">✅ ' + summary + snippetHtml + '</p>');
                        $('.jaggrok-output').html('<p class="jaggrok-provider-message">' + summary + '</p>' + (response.data.html || ''));
                    }
                } else {
                    var message = response && response.data ? response.data : 'Unknown error';
                    if (typeof message === 'object' && message !== null) {
                        message = message.message || 'Unknown error';
                    }
                    $result.html('<p style="color:red">Error: ' + message + '</p>');
                }
            });
        });
    });
});