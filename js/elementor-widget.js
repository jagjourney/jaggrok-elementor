(function($, window) {
    if (!$) {
        return;
    }

    $(document).on('elementor/init', function() {
        var aimentorData = (typeof window.aimentorAjax !== 'undefined') ? window.aimentorAjax : {};
        var strings = aimentorData.strings || {};
        var providerLabels = aimentorData.providerLabels || {};
        var providerSummaries = aimentorData.providerSummaries || {};
        var providersMeta = aimentorData.providersMeta || {};
        var defaultsData = aimentorData.defaults || {};
        var modelPresets = aimentorData.modelPresets || {};
        var modelLabels = aimentorData.modelLabels || {};
        var isProActive = !!aimentorData.isProActive;

        function ensureBadgeStyles() {
            if (!document.getElementById('aimentor-provider-badge-style')) {
                var style = document.createElement('style');
                style.id = 'aimentor-provider-badge-style';
                style.textContent = '.aimentor-provider-badge, .jaggrok-provider-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.05em;}';
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

        function sanitizeTask(value, allowCanvas) {
            var val = value === 'canvas' ? 'canvas' : 'content';
            if (!allowCanvas && val === 'canvas') {
                return 'content';
            }
            return val;
        }

        function sanitizeTier(value) {
            return value === 'quality' ? 'quality' : 'fast';
        }

        function getModelKey(provider, task, tier) {
            if (!modelPresets || !modelPresets[provider] || !modelPresets[provider][task]) {
                return '';
            }
            return modelPresets[provider][task][tier] || '';
        }

        function getModelLabel(provider, task, tier) {
            var key = getModelKey(provider, task, tier);
            if (!key) {
                return '';
            }
            var map = modelLabels[provider] || {};
            return map[key] || key;
        }

        function getTaskLabel(task) {
            if (task === 'canvas') {
                return strings.layoutLabel || strings.pageLayout || 'Layout';
            }
            return strings.copyLabel || strings.pageCopy || 'Copy';
        }

        function getTierLabel(tier) {
            return tier === 'quality' ? (strings.qualityLabel || 'Quality') : (strings.fastLabel || 'Fast');
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

        function applyProviderMeta(providerKey, ui) {
            var meta = getProviderMeta(providerKey);
            if (ui.icon && ui.icon.length) {
                ui.icon.text(meta.icon || '🤖');
            }
            if (ui.label && ui.label.length) {
                ui.label.text(meta.label || providerKey);
            }
            if (ui.summary && ui.summary.length) {
                ui.summary.text(meta.summary || '');
            }
            if (ui.badge && ui.badge.length) {
                ui.badge.text(meta.badgeText || providerKey);
                if (meta.badgeColor) {
                    ui.badge.css('background-color', meta.badgeColor);
                }
            }
            if (ui.button && ui.button.length) {
                var buttonText = strings.askAiMentorWith ? strings.askAiMentorWith.replace('%s', meta.label || providerKey) : (strings.askAiMentor || 'Ask AiMentor');
                ui.button.text(buttonText);
                var aria = strings.askAiMentor ? strings.askAiMentor : buttonText;
                ui.button.attr('aria-label', aria);
            }
            if (ui.heading && ui.heading.length) {
                var headingText = strings.writeWith ? strings.writeWith.replace('%s', meta.label || providerKey) : 'Write with ' + (meta.label || providerKey);
                ui.heading.text(headingText);
            }
            return meta;
        }

        function ensureAjaxConfig($container, $button) {
            if (aimentorData && aimentorData.ajaxurl && aimentorData.nonce) {
                return true;
            }
            var message = strings.missingConfig || 'AiMentor AJAX configuration is missing. Please ensure the plugin assets are enqueued properly.';
            var noticeHtml = '<div class="notice notice-error aimentor-missing-config jaggrok-missing-config"><p>' + escapeHtml(message) + '</p></div>';
            if ($container && $container.length && !$container.find('.aimentor-missing-config').length) {
                $container.prepend(noticeHtml);
            } else if (!$container || !$container.length) {
                $('body').prepend(noticeHtml);
            }
            if ($button && $button.length) {
                $button.prop('disabled', true);
            }
            console.error('AiMentor AJAX configuration missing: expected window.aimentorAjax.');
            return false;
        }

        function getDefaultProvider() {
            if (typeof aimentorData.provider !== 'undefined' && aimentorData.provider) {
                return aimentorData.provider;
            }
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            return keys.length ? keys[0] : 'grok';
        }

        function getGeneratingMessage(meta) {
            if (strings.generatingWith) {
                return strings.generatingWith.replace('%s', meta.label || meta.key || '');
            }
            return 'Generating with ' + (meta.label || 'AiMentor') + '…';
        }

        function updateSummaryText($summary, providerKey, selection) {
            if ($summary && $summary.length) {
                $summary.text(buildSummary(providerKey, selection.task, selection.tier));
            }
        }

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

            if (!ensureAjaxConfig($container, $button)) {
                return;
            }

            var widgetState = api.state.widgets[config.widgetId] || config.defaults || api.state.defaults;
            widgetState = {
                task: sanitizeTask(widgetState.task, allowCanvas),
                tier: sanitizeTier(widgetState.tier)
            };

            if ($task.length) {
                $task.val(widgetState.task);
                if (!allowCanvas) {
                    $task.prop('disabled', true);
                }
            }
            if ($tier.length) {
                $tier.val(widgetState.tier);
            }

            var providerKey = $provider.val() || getDefaultProvider();
            var providerMeta = applyProviderMeta(providerKey, ui);
            updateSummaryText($summary, providerKey, widgetState);
            api.state.widgets[config.widgetId] = widgetState;

            function getSelection() {
                var currentTask = $task.length ? $task.val() : widgetState.task;
                var currentTier = $tier.length ? $tier.val() : widgetState.tier;
                var selection = {
                    task: sanitizeTask(currentTask, allowCanvas),
                    tier: sanitizeTier(currentTier)
                };
                if ($task.length && selection.task !== currentTask) {
                    $task.val(selection.task);
                }
                if ($tier.length && selection.tier !== currentTier) {
                    $tier.val(selection.tier);
                }
                return selection;
            }

            $provider.off('change.aimentor').on('change.aimentor', function() {
                var key = $(this).val();
                providerMeta = applyProviderMeta(key, ui);
                updateSummaryText($summary, key, api.state.widgets[config.widgetId] || widgetState);
            });

            if ($task.length) {
                $task.off('change.aimentor').on('change.aimentor', function() {
                    var selection = getSelection();
                    api.state.widgets[config.widgetId] = selection;
                    updateSummaryText($summary, $provider.val(), selection);
                });
            }

            if ($tier.length) {
                $tier.off('change.aimentor').on('change.aimentor', function() {
                    var selection = getSelection();
                    api.state.widgets[config.widgetId] = selection;
                    updateSummaryText($summary, $provider.val(), selection);
                });
            }

            $button.off('click.aimentor').on('click.aimentor', function() {
                var promptValue = ($prompt.val() || '').trim();
                var providerValue = $provider.val() || getDefaultProvider();
                providerMeta = getProviderMeta(providerValue);
                var selection = getSelection();
                api.state.widgets[config.widgetId] = selection;
                updateSummaryText($summary, providerValue, selection);

                if (!promptValue) {
                    var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                    if ($output.length) {
                        $output.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                    }
                    return;
                }

                var generatingMessage = getGeneratingMessage(providerMeta);
                if ($output.length) {
                    $output.html('<p>' + escapeHtml(generatingMessage) + '</p><p>' + escapeHtml(buildSummary(providerValue, selection.task, selection.tier)) + '</p>');
                }

                var originalText = $button.text();
                $button.prop('disabled', true).text(strings.generatingWith ? strings.generatingWith.replace('%s', providerMeta.label || providerMeta.key || '') : originalText);

                $.post(aimentorData.ajaxurl, {
                    action: 'aimentor_generate_page',
                    prompt: promptValue,
                    provider: providerValue,
                    task: selection.task,
                    tier: selection.tier,
                    nonce: aimentorData.nonce
                }, function(response) {
                    $button.prop('disabled', false);
                    applyProviderMeta(providerValue, ui);
                    if (response && response.data && response.data.provider) {
                        providerValue = response.data.provider;
                        applyProviderMeta(providerValue, ui);
                    }
                    updateSummaryText($summary, providerValue, selection);

                    if (response && response.success) {
                        if (response.data && response.data.canvas_json) {
                            if (window.elementorFrontend && elementorFrontend.elementsHandler) {
                                elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                            }
                            if ($output.length) {
                                $output.html('<p class="aimentor-provider-message jaggrok-provider-message">' + escapeHtml(buildSummary(providerValue, selection.task, selection.tier)) + '</p>');
                            }
                        } else if ($output.length) {
                            var html = response.data && response.data.html ? response.data.html : '';
                            $output.html('<p class="aimentor-provider-message jaggrok-provider-message">' + escapeHtml(buildSummary(providerValue, selection.task, selection.tier)) + '</p>' + html);
                        }
                    } else if ($output.length) {
                        var message = response && response.data ? response.data : 'Unknown error';
                        if (typeof message === 'object' && message !== null) {
                            message = message.message || 'Unknown error';
                        }
                        var errorPrefix = strings.errorPrefix || 'Error:';
                        $output.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(message) + '</p>');
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
    });
})(jQuery, window);
