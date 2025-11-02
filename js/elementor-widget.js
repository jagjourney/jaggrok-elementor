jQuery(document).on('elementor/init', function() {
    var jaggrokData = (typeof jaggrokAjax !== 'undefined') ? jaggrokAjax : {};
    var strings = jaggrokData.strings || {};
    var providerLabels = jaggrokData.providerLabels || {};
    var providerSummaries = jaggrokData.providerSummaries || {};
    var providersMeta = jaggrokData.providersMeta || {};

    function ensureBadgeStyles() {
        if (!document.getElementById('jaggrok-provider-badge-style')) {
            var style = document.createElement('style');
            style.id = 'jaggrok-provider-badge-style';
            style.textContent = '.jaggrok-provider-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.05em;}';
            document.head.appendChild(style);
        }
    }
    ensureBadgeStyles();

    function formatString(template, value) {
        if (!template) {
            return value;
        }
        return template.replace('%s', value);
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    var providerDefaults = {};
    if (providersMeta && Object.keys(providersMeta).length) {
        Object.keys(providersMeta).forEach(function(key) {
            var meta = providersMeta[key] || {};
            var label = meta.label || providerLabels[key] || key;
            providerDefaults[key] = Object.assign({
                label: label,
                icon: meta.icon || '🤖',
                summary: meta.summary || providerSummaries[key] || formatString(strings.contentGenerated, label || key),
                badgeText: meta.badgeText || label,
                badgeColor: meta.badgeColor || '#444444'
            }, meta);
        });
    }

    if (!Object.keys(providerDefaults).length) {
        providerDefaults = {
            grok: {
                label: providerLabels.grok || 'xAI Grok',
                icon: '🚀',
                summary: providerSummaries.grok || formatString(strings.contentGenerated, providerLabels.grok || 'xAI Grok'),
                badgeText: 'xAI',
                badgeColor: '#1E1E1E'
            },
            openai: {
                label: providerLabels.openai || 'OpenAI',
                icon: '🔷',
                summary: providerSummaries.openai || formatString(strings.contentGenerated, providerLabels.openai || 'OpenAI'),
                badgeText: 'OpenAI',
                badgeColor: '#2B8CFF'
            }
        };
    }

    var providerMap = Object.assign({}, providerDefaults);
    if (providerLabels && Object.keys(providerLabels).length) {
        Object.keys(providerLabels).forEach(function(key) {
            var label = providerLabels[key];
            var meta = providerMap[key] || {};
            meta.label = label;
            meta.summary = providerSummaries[key] || meta.summary || formatString(strings.contentGenerated, label);
            providerMap[key] = meta;
        });
    }
    window.JagGrokProviders = Object.assign({}, providerMap, window.JagGrokProviders || {});

    function getProviderMeta(key) {
        var meta = window.JagGrokProviders && window.JagGrokProviders[key];
        if (!meta) {
            return {
                label: key,
                icon: '🤖',
                summary: formatString(strings.contentGenerated, key),
                badgeText: key,
                badgeColor: '#444444'
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
            var badge = meta.badgeText ? `<span class=\"jaggrok-provider-badge\" style=\"background-color:${escapeHtml(meta.badgeColor || '#444444')}\">${escapeHtml(meta.badgeText)}<\/span>` : '';
            optionsHtml += `
                                <label class=\"jaggrok-provider-option\">
                                    <input type=\"radio\" name=\"jaggrok-modal-provider\" value=\"${valueAttr}\"${checkedAttr}>
                                    ${iconHtml}
                                    <span class=\"jaggrok-provider-name\">${label}</span>
                                    ${badge}
                                </label>`;
        });
        return optionsHtml;
    }

    function updateModalProviderSummary(provider) {
        var meta = getProviderMeta(provider);
        $('#jaggrok-provider-active-icon').text(meta.icon || '🤖');
        $('#jaggrok-provider-active-label').text(meta.label || provider);
        $('#jaggrok-provider-summary').text(meta.summary || '');
        var badgeEl = $('#jaggrok-provider-active-badge');
        if (badgeEl.length) {
            badgeEl.text(meta.badgeText || provider);
            if (meta.badgeColor) {
                badgeEl.css('background-color', meta.badgeColor);
            }
        }
        if (strings.generateWith) {
            $('#jaggrok-generate').text(formatString(strings.generateWith, meta.label || provider));
        } else {
            $('#jaggrok-generate').text('Generate with ' + (meta.label || provider));
        }
        var headingText = strings.writeWith ? formatString(strings.writeWith, meta.label || provider) : 'Write with ' + (meta.label || provider);
        var headingEl = $('#jaggrok-modal-heading-text');
        if (headingEl.length) {
            headingEl.text(headingText);
        } else {
            $('#jaggrok-modal-heading').text(headingText);
        }
    }

    function extractResponseSummary(response, fallbackProvider) {
        var providerKey = (response && response.data && response.data.provider) ? response.data.provider : fallbackProvider;
        var meta = getProviderMeta(providerKey);
        if (response && response.data && response.data.provider_label) {
            return strings.contentGenerated ? formatString(strings.contentGenerated, response.data.provider_label) : 'Content generated with ' + response.data.provider_label + '.';
        }
        return meta.summary || (strings.contentGenerated ? formatString(strings.contentGenerated, meta.label || providerKey) : 'Content generated with ' + (meta.label || providerKey) + '.');
    }

    function getDefaultProvider() {
        if (typeof jaggrokData.provider !== 'undefined' && jaggrokData.provider) {
            return jaggrokData.provider;
        }
        var providers = window.JagGrokProviders || {};
        var keys = Object.keys(providers);
        return keys.length ? keys[0] : 'grok';
    }

    elementor.hooks.addAction( 'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event', function() {
        if (!$('#jaggrok-modal').length) {
            var defaultProvider = getDefaultProvider();
            var providerOptions = buildProviderOptions(defaultProvider);
            $('body').append(`
                <div id="jaggrok-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:500px;background:white;border-radius:5px;box-shadow:0 5px 15px rgba(0,0,0,0.3);">
                        <div style="padding:20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
                            <h3 id="jaggrok-modal-heading" style="margin:0;"><i class="eicon-brain" aria-hidden="true"></i> <span id="jaggrok-modal-heading-text"></span></h3>
                            <button id="jaggrok-modal-close" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;" aria-label="${escapeHtml(strings.closeModal || 'Close modal')}">&times;</button>
                        </div>
                        <div style="padding:20px;">
                            <div class="jaggrok-modal-provider" style="margin-bottom:12px;">
                                <span id="jaggrok-modal-provider-label" style="display:block;font-weight:600;margin-bottom:6px;">${escapeHtml(strings.chooseProvider || 'Choose provider')}</span>
                                <div class="jaggrok-provider-toggle" role="radiogroup">
                                    ${providerOptions}
                                </div>
                                <div class="jaggrok-provider-active" style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                                    <span id="jaggrok-provider-active-icon" aria-hidden="true"></span>
                                    <strong id="jaggrok-provider-active-label"></strong>
                                    <span id="jaggrok-provider-active-badge" class="jaggrok-provider-badge"></span>
                                </div>
                                <p id="jaggrok-provider-summary" style="margin:6px 0 0; font-size:13px;"></p>
                            </div>
                            <textarea id="jaggrok-prompt" placeholder="Describe your page (e.g. Create a hero section with blue button)" rows="4" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:3px;font-family:inherit;"></textarea>
                            <br>
                            <button id="jaggrok-generate" class="button button-primary" style="width:100%;margin:10px 0;padding:10px;"></button>
                            <div id="jaggrok-result" style="margin-top:15px;padding:10px;background:#f1f3f5;border-radius:3px;"></div>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#jaggrok-modal').show();
        $('#jaggrok-modal-close').off('click').on('click', function() {
            $('#jaggrok-modal').hide();
        });
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

        $('#jaggrok-generate').off('click').on('click', function() {
            var prompt = $('#jaggrok-prompt').val().trim();
            var $btn = $(this);
            var $result = $('#jaggrok-result');
            var provider = $('input[name="jaggrok-modal-provider"]:checked').val() || getDefaultProvider();
            var providerMeta = getProviderMeta(provider);

            if (!prompt) {
                var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                $result.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                return;
            }

            $btn.prop('disabled', true).text(strings.generatingWith ? formatString(strings.generatingWith, providerMeta.label || provider) : 'Generating...');
            var generatingLine = strings.generatingWith ? formatString(strings.generatingWith, providerMeta.label || provider) : 'Generating with ' + (providerMeta.label || provider) + '...';
            $result.html('<p>🤖 ' + escapeHtml(generatingLine) + '</p>');

            $.post(jaggrokData.ajaxurl, {
                action: 'jaggrok_generate_page',
                prompt: prompt,
                provider: provider,
                nonce: jaggrokData.nonce
            }, function(response) {
                $btn.prop('disabled', false).text(strings.generateAgain || 'Generate Again');
                if (response.success) {
                    var summary = extractResponseSummary(response, provider);
                    if (response.data.canvas_json) {
                        elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                        var successPrefix = strings.successPrefix || '✅';
                        $result.html('<p style="color:green">' + escapeHtml(successPrefix) + ' ' + escapeHtml(summary) + '</p>');
                        $('.jaggrok-output').html('<p class="jaggrok-provider-message">' + summary + '</p>');
                    } else {
                        var snippet = '';
                        if (response.data.html) {
                            snippet = response.data.html.substring(0, 100) + '...';
                        }
                        var snippetHtml = snippet ? '<br><small>' + escapeHtml(snippet) + '</small>' : '';
                        var successPrefix = strings.successPrefix || '✅';
                        $result.html('<p style="color:green">' + escapeHtml(successPrefix) + ' ' + escapeHtml(summary) + snippetHtml + '</p>');
                        $('.jaggrok-output').html('<p class="jaggrok-provider-message">' + summary + '</p>' + (response.data.html || ''));
                    }
                } else {
                    var message = response && response.data ? response.data : 'Unknown error';
                    if (typeof message === 'object' && message !== null) {
                        message = message.message || 'Unknown error';
                    }
                    var errorPrefix = strings.errorPrefix || 'Error:';
                    $result.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(message) + '</p>');
                }
            });
        });
    });
});
