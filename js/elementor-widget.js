(function(window) {
    var selectorTransforms = [
        { pattern: /jaggrok_/g, replacement: 'aimentor_' },
        { pattern: /jaggrok-/g, replacement: 'aimentor-' }
    ];

    function mapSelector(selector) {
        if (typeof selector !== 'string') {
            return selector;
        }

        var mapped = selector;
        selectorTransforms.forEach(function(entry) {
            mapped = mapped.replace(entry.pattern, entry.replacement);
        });
        return mapped;
    }

    if (window.document) {
        var doc = window.document;
        var originalGetElementById = doc.getElementById.bind(doc);
        doc.getElementById = function(id) {
            return originalGetElementById(mapSelector(id));
        };

        var originalQuerySelector = doc.querySelector.bind(doc);
        doc.querySelector = function(selector) {
            return originalQuerySelector(mapSelector(selector));
        };

        var originalQuerySelectorAll = doc.querySelectorAll.bind(doc);
        doc.querySelectorAll = function(selector) {
            return originalQuerySelectorAll(mapSelector(selector));
        };
    }
})(window);

jQuery(document).on('elementor/init', function() {
    var aimentorData = (typeof aimentorAjax !== 'undefined') ? aimentorAjax : {};
    var strings = aimentorData.strings || {};
    var providerLabels = aimentorData.providerLabels || {};
    var providerSummaries = aimentorData.providerSummaries || {};
    var providersMeta = aimentorData.providersMeta || {};

    function ensureBadgeStyles() {
        if (!document.getElementById('aimentor-provider-badge-style')) {
            var style = document.createElement('style');
            style.id = 'aimentor-provider-badge-style';
            style.textContent = '.aimentor-provider-badge, .jaggrok-provider-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.05em;}';
            document.head.appendChild(style);
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
        window.AiMentorProviders = Object.assign({}, providerMap, window.AiMentorProviders || {});

        function getProviderMeta(key) {
            var meta = (window.AiMentorProviders || {})[key];
            if (!meta) {
                return {
                    label: key,
                    icon: '🤖',
                    summary: formatString(strings.contentGenerated, key),
                    badgeText: key,
                    badgeColor: '#444444'
                };
            }
            meta.key = key;
            return meta;
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
    window.AiMentorProviders = Object.assign({}, providerMap, window.AiMentorProviders || {});
    window.JagGrokProviders = window.JagGrokProviders || window.AiMentorProviders;

    function getProviderMeta(key) {
        var meta = (window.AiMentorProviders || window.JagGrokProviders || {})[key];
        if (!meta) {
            return {
                label: key,
                icon: '🤖',
                summary: formatString(strings.contentGenerated, key),
                badgeText: key,
                badgeColor: '#444444'
            };
        }

    function buildProviderOptions(defaultProvider) {
        var optionsHtml = '';
        var providers = window.AiMentorProviders || window.JagGrokProviders || {};
        var keys = Object.keys(providers);
        if (!keys.length) {
            keys = Object.keys(providerDefaults);
            providers = providerDefaults;
        }
        keys.forEach(function(key, index) {
            var meta = providers[key] || {};
            var icon = meta.icon ? '<span class=\"aimentor-provider-icon\" aria-hidden=\"true\">' + meta.icon + '</span>' : '';
            var label = escapeHtml(meta.label || key);
            var valueAttr = escapeHtml(key);
            var isChecked = (key === defaultProvider) || (!defaultProvider && index === 0);
            var checkedAttr = isChecked ? ' checked' : '';
            var iconHtml = icon || '';
            var badge = meta.badgeText ? `<span class=\"aimentor-provider-badge\" style=\"background-color:${escapeHtml(meta.badgeColor || '#444444')}\">${escapeHtml(meta.badgeText)}<\/span>` : '';
            optionsHtml += `
                                <label class=\"aimentor-provider-option\">
                                    <input type=\"radio\" name=\"aimentor-modal-provider\" value=\"${valueAttr}\"${checkedAttr}>
                                    ${iconHtml}
                                    <span class=\"aimentor-provider-name\">${label}</span>
                                    ${badge}
                                </label>`;
        });
        return optionsHtml;
    }

    function updateModalProviderSummary(provider) {
        var meta = getProviderMeta(provider);
        $('#aimentor-provider-active-icon').text(meta.icon || '🤖');
        $('#aimentor-provider-active-label').text(meta.label || provider);
        $('#aimentor-provider-summary').text(meta.summary || '');
        var badgeEl = $('#aimentor-provider-active-badge');
        if (badgeEl.length) {
            badgeEl.text(meta.badgeText || provider);
            if (meta.badgeColor) {
                badgeEl.css('background-color', meta.badgeColor);
            }
            var map = modelLabels[provider] || {};
            return map[key] || key;
        }
        if (strings.generateWith) {
            $('#aimentor-generate').text(formatString(strings.generateWith, meta.label || provider));
        } else {
            $('#aimentor-generate').text('Generate with ' + (meta.label || provider));
        }
        var headingText = strings.writeWith ? formatString(strings.writeWith, meta.label || provider) : 'Write with ' + (meta.label || provider);
        var headingEl = $('#aimentor-modal-heading-text');
        if (headingEl.length) {
            headingEl.text(headingText);
        } else {
            $('#aimentor-modal-heading').text(headingText);
        }

        function buildSummary(providerKey, task, tier) {
            var taskLabel = getTaskLabel(task);
            var tierLabel = getTierLabel(tier);
            var separator = typeof strings.summarySeparator === 'string' ? strings.summarySeparator : ' • ';
            var summary = taskLabel + separator + tierLabel;
            var modelLabel = getModelLabel(providerKey, task, tier);
            if (modelLabel) {
                var powered = strings.summaryPoweredBy ? strings.summaryPoweredBy.replace('%s', modelLabel) : '– powered by ' + modelLabel;
                summary += ' ' + powered;
            }
            return summary;
        }

    function getDefaultProvider() {
        if (typeof aimentorData.provider !== 'undefined' && aimentorData.provider) {
            return aimentorData.provider;
        }
        var providers = window.AiMentorProviders || window.JagGrokProviders || {};
        var keys = Object.keys(providers);
        return keys.length ? keys[0] : 'grok';
    }

    function openAimentorModal() {
        if (!$('#aimentor-modal').length) {
            var defaultProvider = getDefaultProvider();
            var providerOptions = buildProviderOptions(defaultProvider);
            $('body').append(`
                <div id="aimentor-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:500px;background:white;border-radius:5px;box-shadow:0 5px 15px rgba(0,0,0,0.3);">
                        <div style="padding:20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
                            <h3 id="aimentor-modal-heading" style="margin:0;"><i class="eicon-brain" aria-hidden="true"></i> <span id="aimentor-modal-heading-text"></span></h3>
                            <button id="aimentor-modal-close" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;" aria-label="${escapeHtml(strings.closeModal || 'Close modal')}">&times;</button>
                        </div>
                        <div style="padding:20px;">
                            <div class="aimentor-modal-provider" style="margin-bottom:12px;">
                                <span id="aimentor-modal-provider-label" style="display:block;font-weight:600;margin-bottom:6px;">${escapeHtml(strings.chooseProvider || 'Choose provider')}</span>
                                <div class="aimentor-provider-toggle" role="radiogroup">
                                    ${providerOptions}
                                </div>
                                <div class="aimentor-provider-active" style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                                    <span id="aimentor-provider-active-icon" aria-hidden="true"></span>
                                    <strong id="aimentor-provider-active-label"></strong>
                                    <span id="aimentor-provider-active-badge" class="aimentor-provider-badge"></span>
                                </div>
                                <p id="aimentor-provider-summary" style="margin:6px 0 0; font-size:13px;"></p>
                            </div>
                            <textarea id="aimentor-prompt" placeholder="Describe your page (e.g. Create a hero section with blue button)" rows="4" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:3px;font-family:inherit;"></textarea>
                            <br>
                            <button id="aimentor-generate" class="button button-primary" style="width:100%;margin:10px 0;padding:10px;"></button>
                            <div id="aimentor-result" style="margin-top:15px;padding:10px;background:#f1f3f5;border-radius:3px;"></div>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#aimentor-modal').show();
        $('#aimentor-modal-close').off('click').on('click', function() {
            $('#aimentor-modal').hide();
        });
        $('#aimentor-prompt').focus();
        var defaultProvider = getDefaultProvider();
        var $providerRadios = $('input[name="aimentor-modal-provider"]');
        if ($providerRadios.length && !$providerRadios.filter(':checked').length) {
            $providerRadios.filter('[value="' + defaultProvider + '"]').prop('checked', true);
        }

        function getGeneratingMessage(meta) {
            if (strings.generatingWith) {
                return strings.generatingWith.replace('%s', meta.label || meta.key || '');
            }
            return 'Generating with ' + (meta.label || 'AiMentor') + '…';
        }

        $('#aimentor-generate').off('click').on('click', function() {
            var prompt = $('#aimentor-prompt').val().trim();
            var $btn = $(this);
            var $result = $('#aimentor-result');
            var provider = $('input[name="aimentor-modal-provider"]:checked').val() || getDefaultProvider();
            var providerMeta = getProviderMeta(provider);

        function buildProviderOptions(defaultProvider) {
            var optionsHtml = '';
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            if (!keys.length) {
                keys = Object.keys(providerDefaults);
                providers = providerDefaults;
            }
            keys.forEach(function(key, index) {
                var meta = providers[key] || {};
                var icon = meta.icon ? '<span class="aimentor-provider-icon" aria-hidden="true">' + escapeHtml(meta.icon) + '</span>' : '';
                var label = escapeHtml(meta.label || key);
                var valueAttr = escapeHtml(key);
                var isChecked = (key === defaultProvider) || (!defaultProvider && index === 0);
                var checkedAttr = isChecked ? ' checked' : '';
                var badge = meta.badgeText ? '<span class="aimentor-provider-badge" style="background-color:' + escapeHtml(meta.badgeColor || '#444444') + '">' + escapeHtml(meta.badgeText) + '</span>' : '';
                optionsHtml += '<label class="aimentor-provider-option"><input type="radio" name="aimentor-modal-provider" value="' + valueAttr + '"' + checkedAttr + '>' + icon + '<span class="aimentor-provider-name">' + label + '</span>' + badge + '</label>';
            });
            return optionsHtml;
        }

        var api = window.AiMentorElementorUI || {};
        var existingState = api.state || {};
        var defaultTask = sanitizeTask(defaultsData.task || (existingState.defaults && existingState.defaults.task) || 'content', isProActive);
        var defaultTier = sanitizeTier(defaultsData.tier || (existingState.defaults && existingState.defaults.tier) || 'fast');
        api.state = {
            defaults: {
                task: defaultTask,
                tier: defaultTier
            },
            widgets: existingState.widgets || {},
            modal: existingState.modal ? {
                task: sanitizeTask(existingState.modal.task, isProActive),
                tier: sanitizeTier(existingState.modal.tier),
                provider: existingState.modal.provider || getDefaultProvider()
            } : {
                task: defaultTask,
                tier: defaultTier,
                provider: getDefaultProvider()
            }
        };

        api.buildSummary = buildSummary;
        api.getProviderMeta = getProviderMeta;

        api.initWidget = function(config) {
            if (!config || !config.widgetId) {
                return;
            }
            var allowCanvas = !!config.allowCanvas && isProActive;
            var $container = config.container ? $(config.container) : $();
            var $provider = config.providerSelector ? $(config.providerSelector) : $();
            var $prompt = config.promptSelector ? $(config.promptSelector) : $();
            var $output = config.outputSelector ? $(config.outputSelector) : $();
            var $button = config.buttonSelector ? $(config.buttonSelector) : $();
            var $task = config.taskSelector ? $(config.taskSelector) : $();
            var $tier = config.tierSelector ? $(config.tierSelector) : $();
            var $summary = config.summarySelector ? $(config.summarySelector) : $();
            var ui = {
                icon: config.providerIconSelector ? $(config.providerIconSelector) : $(),
                label: config.providerLabelSelector ? $(config.providerLabelSelector) : $(),
                summary: config.providerSummarySelector ? $(config.providerSummarySelector) : $(),
                badge: config.providerBadgeSelector ? $(config.providerBadgeSelector) : $(),
                button: $button
            };

            if (!$provider.length || !$button.length || !$prompt.length) {
                return;
            }

            $.post(aimentorData.ajaxurl, {
                action: 'aimentor_generate_page',
                prompt: prompt,
                provider: provider,
                nonce: aimentorData.nonce
            }, function(response) {
                $btn.prop('disabled', false).text(strings.generateAgain || 'Generate Again');
                if (response.success) {
                    var summary = extractResponseSummary(response, provider);
                    if (response.data.canvas_json) {
                        elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                        var successPrefix = strings.successPrefix || '✅';
                        $result.html('<p style="color:green">' + escapeHtml(successPrefix) + ' ' + escapeHtml(summary) + '</p>');
                        $('.aimentor-output').html('<p class="aimentor-provider-message">' + summary + '</p>');
                    } else {
                        var snippet = '';
                        if (response.data.html) {
                            snippet = response.data.html.substring(0, 100) + '...';
                        }
                        var snippetHtml = snippet ? '<br><small>' + escapeHtml(snippet) + '</small>' : '';
                        var successPrefix = strings.successPrefix || '✅';
                        $result.html('<p style="color:green">' + escapeHtml(successPrefix) + ' ' + escapeHtml(summary) + snippetHtml + '</p>');
                        $('.aimentor-output').html('<p class="aimentor-provider-message">' + summary + '</p>' + (response.data.html || ''));
                    }
                });
            });
        };

        api.openModal = function() {
            var allowCanvas = isProActive;
            if (!$('#aimentor-modal').length) {
                var defaultProvider = getDefaultProvider();
                var providerOptions = buildProviderOptions(defaultProvider);
                var generationLabel = escapeHtml(strings.generationType || 'Generation Type');
                var performanceLabel = escapeHtml(strings.performanceLabel || 'Performance');
                var pageCopyLabel = escapeHtml(strings.pageCopy || 'Page Copy');
                var pageLayoutLabel = escapeHtml(strings.pageLayout || 'Page Layout');
                var fastLabel = escapeHtml(strings.fastLabel || 'Fast');
                var qualityLabel = escapeHtml(strings.qualityLabel || 'Quality');
                var promptPlaceholder = escapeHtml(strings.promptPlaceholder || 'Describe your page (e.g., hero with CTA)');
                var headingText = escapeHtml(strings.writeWith ? strings.writeWith.replace('%s', getProviderMeta(defaultProvider).label || defaultProvider) : 'Write with ' + (getProviderMeta(defaultProvider).label || defaultProvider));
                var modalHtml = '' +
                    '<div id="aimentor-modal" class="aimentor-modal" role="dialog" aria-modal="true" aria-labelledby="aimentor-modal-heading-text" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">' +
                    '  <div class="aimentor-modal__content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:520px;background:white;border-radius:8px;box-shadow:0 5px 18px rgba(0,0,0,0.25);overflow:hidden;">' +
                    '    <div class="aimentor-modal__header" style="padding:20px;border-bottom:1px solid #e2e4e7;display:flex;align-items:center;justify-content:space-between;gap:12px;">' +
                    '      <h3 id="aimentor-modal-heading-text" class="aimentor-modal__title" style="margin:0;display:flex;align-items:center;gap:8px;"><span class="dashicons dashicons-art" aria-hidden="true"></span><span>' + headingText + '</span></h3>' +
                    '      <button type="button" id="aimentor-modal-close" class="aimentor-modal__close" aria-label="' + escapeHtml(strings.closeModal || 'Close modal') + '" style="background:none;border:none;font-size:24px;line-height:1;color:#6b7280;cursor:pointer;">&times;</button>' +
                    '    </div>' +
                    '    <div class="aimentor-modal__body" style="padding:20px;display:flex;flex-direction:column;gap:16px;">' +
                    '      <div class="aimentor-modal__providers" role="radiogroup" aria-label="' + escapeHtml(strings.chooseProvider || 'Choose provider') + '">' + providerOptions + '</div>' +
                    '      <div class="aimentor-provider-active" style="display:flex;align-items:center;gap:8px;">' +
                    '        <span id="aimentor-provider-active-icon" aria-hidden="true"></span>' +
                    '        <strong id="aimentor-provider-active-label"></strong>' +
                    '        <span id="aimentor-provider-active-badge" class="aimentor-provider-badge"></span>' +
                    '      </div>' +
                    '      <p id="aimentor-provider-summary" class="aimentor-provider-description" style="margin:0;font-size:13px;color:#4b5563;"></p>' +
                    '      <label for="aimentor-modal-task" class="aimentor-modal__label">' + generationLabel + '</label>' +
                    '      <select id="aimentor-modal-task" class="aimentor-modal__select" ' + (allowCanvas ? '' : 'disabled') + '>' +
                    '        <option value="content">' + pageCopyLabel + '</option>' +
                    '        <option value="canvas"' + (allowCanvas ? '' : ' disabled') + '>' + pageLayoutLabel + '</option>' +
                    '      </select>' +
                    '      <label for="aimentor-modal-tier" class="aimentor-modal__label">' + performanceLabel + '</label>' +
                    '      <select id="aimentor-modal-tier" class="aimentor-modal__select">' +
                    '        <option value="fast">' + fastLabel + '</option>' +
                    '        <option value="quality">' + qualityLabel + '</option>' +
                    '      </select>' +
                    '      <p id="aimentor-modal-summary" class="aimentor-context-summary" aria-live="polite" style="margin:0;font-weight:600;color:#111827;"></p>' +
                    '      <label for="aimentor-prompt" class="aimentor-modal__label">' + escapeHtml(strings.promptLabel || 'Prompt') + '</label>' +
                    '      <textarea id="aimentor-prompt" rows="4" placeholder="' + promptPlaceholder + '" style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:6px;font-family:inherit;"></textarea>' +
                    '      <button type="button" id="aimentor-generate" class="button button-primary" style="width:100%;padding:12px;font-size:16px;font-weight:600;">' + escapeHtml(strings.askAiMentor || 'Ask AiMentor') + '</button>' +
                    '      <div id="aimentor-result" style="min-height:38px;padding:12px;background:#f3f4f6;border-radius:6px;color:#111827;"></div>' +
                    '    </div>' +
                    '  </div>' +
                    '</div>';
                $('body').append(modalHtml);
            }

            var $modal = $('#aimentor-modal');
            var $close = $('#aimentor-modal-close');
            var $providerRadios = $('input[name="aimentor-modal-provider"]');
            var $task = $('#aimentor-modal-task');
            var $tier = $('#aimentor-modal-tier');
            var $summary = $('#aimentor-modal-summary');
            var $prompt = $('#aimentor-prompt');
            var $generate = $('#aimentor-generate');
            var $result = $('#aimentor-result');
            var ui = {
                icon: $('#aimentor-provider-active-icon'),
                label: $('#aimentor-provider-active-label'),
                summary: $('#aimentor-provider-summary'),
                badge: $('#aimentor-provider-active-badge'),
                button: $generate,
                heading: $('#aimentor-modal-heading-text')
            };

            $modal.show();
            $close.off('click').on('click', function() {
                $modal.hide();
            });

            api.state.modal.provider = api.state.modal.provider || getDefaultProvider();
            if ($providerRadios.length && !$providerRadios.filter(':checked').length) {
                $providerRadios.filter('[value="' + api.state.modal.provider + '"]').prop('checked', true);
            }

            if ($task.length) {
                $task.val(sanitizeTask(api.state.modal.task, allowCanvas));
                if (!allowCanvas) {
                    $task.prop('disabled', true);
                }
            }
            if ($tier.length) {
                $tier.val(sanitizeTier(api.state.modal.tier));
            }

            var activeProvider = $providerRadios.filter(':checked').val() || getDefaultProvider();
            api.state.modal.provider = activeProvider;
            applyProviderMeta(activeProvider, ui);
            updateSummaryText($summary, activeProvider, {
                task: sanitizeTask(api.state.modal.task, allowCanvas),
                tier: sanitizeTier(api.state.modal.tier)
            });

            $providerRadios.off('change').on('change', function() {
                var value = $(this).val();
                api.state.modal.provider = value;
                applyProviderMeta(value, ui);
                updateSummaryText($summary, value, {
                    task: sanitizeTask($task.val(), allowCanvas),
                    tier: sanitizeTier($tier.val())
                });
            });

            $task.off('change').on('change', function() {
                api.state.modal.task = sanitizeTask($(this).val(), allowCanvas);
                $(this).val(api.state.modal.task);
                updateSummaryText($summary, api.state.modal.provider, {
                    task: api.state.modal.task,
                    tier: sanitizeTier($tier.val())
                });
            });

            $tier.off('change').on('change', function() {
                api.state.modal.tier = sanitizeTier($(this).val());
                $(this).val(api.state.modal.tier);
                updateSummaryText($summary, api.state.modal.provider, {
                    task: sanitizeTask($task.val(), allowCanvas),
                    tier: api.state.modal.tier
                });
            });

            $prompt.val('').focus();
            $result.empty();

            if (!ensureAjaxConfig($modal, $generate)) {
                return;
            }

            $generate.off('click').on('click', function() {
                var promptValue = ($prompt.val() || '').trim();
                if (!promptValue) {
                    var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                    $result.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                    return;
                }

                var providerValue = api.state.modal.provider || getDefaultProvider();
                var selection = {
                    task: sanitizeTask($task.val(), allowCanvas),
                    tier: sanitizeTier($tier.val())
                };
                api.state.modal.task = selection.task;
                api.state.modal.tier = selection.tier;

                var providerMeta = applyProviderMeta(providerValue, ui);
                updateSummaryText($summary, providerValue, selection);

                var generatingMessage = getGeneratingMessage(providerMeta);
                $generate.prop('disabled', true).text(strings.generatingWith ? strings.generatingWith.replace('%s', providerMeta.label || providerMeta.key || '') : (strings.askAiMentor || 'Ask AiMentor'));
                $result.html('<p>' + escapeHtml(generatingMessage) + '</p><p>' + escapeHtml(buildSummary(providerValue, selection.task, selection.tier)) + '</p>');

                $.post(aimentorData.ajaxurl, {
                    action: 'aimentor_generate_page',
                    prompt: promptValue,
                    provider: providerValue,
                    task: selection.task,
                    tier: selection.tier,
                    nonce: aimentorData.nonce
                }, function(response) {
                    $generate.prop('disabled', false);
                    applyProviderMeta(providerValue, ui);
                    if (response && response.data && response.data.provider) {
                        providerValue = response.data.provider;
                        api.state.modal.provider = providerValue;
                        applyProviderMeta(providerValue, ui);
                    }
                    updateSummaryText($summary, providerValue, selection);

                    if (response && response.success) {
                        var summaryText = buildSummary(providerValue, selection.task, selection.tier);
                        if (response.data && response.data.canvas_json) {
                            if (window.elementorFrontend && elementorFrontend.elementsHandler) {
                                elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                            }
                            $result.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + '</p>');
                        } else {
                            var snippet = '';
                            if (response.data && response.data.html) {
                                snippet = response.data.html.substring(0, 120) + '…';
                            }
                            var snippetHtml = snippet ? '<br><small>' + escapeHtml(snippet) + '</small>' : '';
                            $result.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + snippetHtml + '</p>');
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
        };

        window.AiMentorElementorUI = api;

        if (window.elementor && window.elementor.hooks) {
            window.elementor.hooks.addAction('panel/widgets/aimentor-ai-generator/controls/write_with_aimentor/event', api.openModal);
        }
    }

    elementor.hooks.addAction( 'panel/widgets/aimentor-ai-generator/controls/write_with_jaggrok/event', openAimentorModal );
    elementor.hooks.addAction( 'panel/widgets/aimentor-ai-generator/controls/write_with_aimentor/event', openAimentorModal );
    elementor.hooks.addAction( 'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event', openAimentorModal );
    elementor.hooks.addAction( 'panel/widgets/jaggrok-ai-generator/controls/write_with_aimentor/event', openAimentorModal );
})(jQuery, window);
