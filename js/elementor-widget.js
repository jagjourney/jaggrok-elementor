(function($, window) {
    'use strict';

    function ensureBadgeStyles() {
        if (document.getElementById('aimentor-provider-badge-style')) {
            return;
        }
        var style = document.createElement('style');
        style.id = 'aimentor-provider-badge-style';
        style.textContent = '.aimentor-provider-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;text-transform:uppercase;letter-spacing:0.05em;}';
        document.head.appendChild(style);
    }

    function ensureFrameLibraryStyles() {
        if (document.getElementById('aimentor-frame-library-style')) {
            return;
        }
        var style = document.createElement('style');
        style.id = 'aimentor-frame-library-style';
        style.textContent = '' +
            '.aimentor-modal__aside{display:flex;flex-direction:column;gap:12px;min-width:0;}' +
            '.aimentor-modal__aside-tabs{display:flex;gap:6px;}' +
            '.aimentor-modal__aside-tab{flex:1;padding:8px 12px;border-radius:6px;border:1px solid #dcdcde;background:#eef1f5;font-size:12px;font-weight:600;color:#4b5563;cursor:pointer;transition:background-color 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;}' +
            '.aimentor-modal__aside-tab:focus{outline:2px solid #5b9dd9;outline-offset:1px;}' +
            '.aimentor-modal__aside-tab.is-active{background:#ffffff;color:#111827;box-shadow:0 1px 2px rgba(15,23,42,0.12);border-color:#c7c9cc;}' +
            '.aimentor-modal__panel{display:none;border:1px solid #dcdcde;border-radius:8px;background:#ffffff;padding:16px;box-shadow:0 1px 2px rgba(15,23,42,0.08);max-height:360px;overflow:auto;}' +
            '.aimentor-modal__panel.is-active{display:block;}' +
            '.aimentor-modal__select--multiline{min-height:128px;padding:10px;border:1px solid #d1d5db;border-radius:6px;font-family:inherit;line-height:1.4;}' +
            '.aimentor-modal__select--multiline option{white-space:normal;}' +
            '.aimentor-layout-history{display:flex;flex-direction:column;gap:12px;}' +
            '.aimentor-layout-history__header{display:flex;align-items:center;gap:8px;}' +
            '.aimentor-layout-history__title{margin:0;font-size:14px;font-weight:600;color:#111827;}' +
            '.aimentor-layout-history__nav{margin-left:auto;display:flex;gap:6px;}' +
            '.aimentor-layout-history__nav-button{border:1px solid #dcdcde;background:#f3f4f6;color:#111827;border-radius:4px;padding:4px 8px;line-height:1;font-size:12px;cursor:pointer;}' +
            '.aimentor-layout-history__nav-button[disabled]{opacity:0.5;cursor:not-allowed;}' +
            '.aimentor-layout-history__viewport{display:flex;flex-direction:column;gap:8px;}' +
            '.aimentor-layout-history__item{display:flex;flex-direction:column;gap:8px;border:1px solid #e5e7eb;border-radius:6px;padding:12px;background:#f9fafb;}' +
            '.aimentor-layout-history__preview{font-size:13px;color:#1f2937;line-height:1.5;}' +
            '.aimentor-layout-history__meta{margin:0;font-size:12px;color:#6b7280;}' +
            '.aimentor-layout-history__empty{margin:0;font-size:12px;color:#6b7280;}' +
            '.aimentor-frame-library{display:flex;flex-direction:column;gap:12px;}' +
            '.aimentor-frame-library__header{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;}' +
            '.aimentor-frame-library__title{margin:0;font-size:14px;font-weight:600;color:#111827;}' +
            '.aimentor-frame-library__description{margin:0;font-size:12px;color:#4b5563;}' +
            '.aimentor-frame-library__loading,.aimentor-frame-library__error,.aimentor-frame-library__empty,.aimentor-frame-library__updated{font-size:12px;color:#6b7280;margin:0;}' +
            '.aimentor-frame-library__error{color:#b91c1c;}' +
            '.aimentor-frame-library__list{display:grid;gap:12px;}' +
            '.aimentor-frame-library__card{display:flex;gap:12px;align-items:flex-start;border:1px solid #e5e7eb;border-radius:6px;padding:12px;background:#f9fafb;}' +
            '.aimentor-frame-library__preview{width:120px;flex:0 0 120px;aspect-ratio:4/3;background:#f3f4f6;border-radius:4px;overflow:hidden;display:flex;align-items:center;justify-content:center;}' +
            '.aimentor-frame-library__preview img{width:100%;height:100%;object-fit:cover;display:block;}' +
            '.aimentor-frame-library__preview-placeholder{font-size:12px;color:#6b7280;text-align:center;padding:8px;}' +
            '.aimentor-frame-library__content{flex:1;display:flex;flex-direction:column;gap:8px;}' +
            '.aimentor-frame-library__summary{margin:0;font-size:13px;color:#1f2937;line-height:1.5;}' +
            '.aimentor-frame-library__sections{margin:0;padding-left:18px;font-size:12px;color:#374151;}' +
            '.aimentor-frame-library__sections li{margin:0 0 4px;}' +
            '.aimentor-frame-library__meta{margin:0;font-size:12px;color:#6b7280;}' +
            '.aimentor-frame-library__actions{display:flex;gap:8px;flex-wrap:wrap;}' +
            '.aimentor-frame-library__actions .button-link{padding-left:0;padding-right:0;}';
        document.head.appendChild(style);
    }

    function ensureVariationStyles() {
        if (document.getElementById('aimentor-variation-style')) {
            return;
        }
        var style = document.createElement('style');
        style.id = 'aimentor-variation-style';
        style.textContent = '' +
            '.aimentor-variations{display:flex;flex-direction:column;gap:12px;}' +
            '.aimentor-variations__title{margin:0;font-size:16px;font-weight:600;color:#111827;}' +
            '.aimentor-variations__description,.aimentor-variations__count{margin:0;font-size:12px;color:#4b5563;}' +
            '.aimentor-variation-feedback{margin:0;font-size:12px;color:#047857;}' +
            '.aimentor-variations__grid{display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));}' +
            '.aimentor-variation-card{border:1px solid #dcdcde;border-radius:8px;background:#fff;padding:12px;display:flex;flex-direction:column;gap:10px;min-height:180px;box-shadow:0 1px 2px rgba(15,23,42,0.08);}' +
            '.aimentor-variation-card.is-inserted{border-color:#10b981;box-shadow:0 0 0 1px rgba(16,185,129,0.4);}' +
            '.aimentor-variation-card__header{display:flex;justify-content:space-between;align-items:center;gap:8px;}' +
            '.aimentor-variation-card__title{margin:0;font-size:14px;font-weight:600;color:#111827;}' +
            '.aimentor-variation-card__summary{margin:0;font-size:12px;color:#374151;line-height:1.5;}' +
            '.aimentor-variation-card__meta{margin:0;font-size:11px;color:#6b7280;}' +
            '.aimentor-variation-card__preview{background:#f9fafb;border:1px dashed #d1d5db;border-radius:6px;padding:12px;font-size:12px;color:#6b7280;text-align:center;min-height:80px;display:flex;align-items:center;justify-content:center;}' +
            '.aimentor-variation-card__footer{margin-top:auto;display:flex;}' +
            '.aimentor-variation-card__button{width:100%;font-size:13px;font-weight:600;}' +
            '.aimentor-variation-card__button[disabled]{opacity:0.7;cursor:not-allowed;}' +
            '.aimentor-variation-card__meta-list{margin:0;padding:0;list-style:none;display:flex;flex-wrap:wrap;gap:8px;font-size:11px;color:#6b7280;}' +
            '.aimentor-variation-card__meta-item{display:flex;align-items:center;gap:4px;}' +
            '.aimentor-variation-card__meta-icon{font-size:12px;}' +
            '.aimentor-variation-card__meta-text{margin:0;}' +
            '.aimentor-variation-card__preview strong{font-weight:600;color:#111827;}' +
            '.aimentor-variation-card__label{font-size:12px;color:#1f2937;font-weight:600;}' ;
        document.head.appendChild(style);
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatString(template, value) {
        if (!template) {
            return value;
        }
        return template.replace('%s', value);
    }

    function formatCountString(value, singularKey, pluralKey, strings) {
        var number = parseInt(value, 10);
        if (!number || number < 0) {
            return '';
        }
        var key = number === 1 ? singularKey : pluralKey;
        var template = strings && strings[key] ? strings[key] : '';
        if (template && template.indexOf('%d') !== -1) {
            return template.replace('%d', number);
        }
        return number + '';
    }

    function cloneLayout(layout) {
        if (!layout) {
            return null;
        }
        if (typeof layout === 'string') {
            try {
                return JSON.parse(layout);
            } catch (error) {
                return null;
            }
        }
        try {
            return JSON.parse(JSON.stringify(layout));
        } catch (error) {
            return null;
        }
    }

    function formatCanvasVariationMeta(meta, strings) {
        if (!meta || typeof meta !== 'object') {
            return '';
        }

        var pieces = [];
        var separator = strings.canvasVariationMetaSeparator || strings.summarySeparator || ' • ';
        var sectionsText = formatCountString(meta.sections, 'canvasVariationMetaSectionsSingular', 'canvasVariationMetaSections', strings);
        var columnsText = formatCountString(meta.columns, 'canvasVariationMetaColumnsSingular', 'canvasVariationMetaColumns', strings);
        var widgetsText = formatCountString(meta.widgets, 'canvasVariationMetaWidgetsSingular', 'canvasVariationMetaWidgets', strings);

        [sectionsText, columnsText, widgetsText].forEach(function(item) {
            if (item) {
                pieces.push(item);
            }
        });

        return pieces.join(separator);
    }

    function resolveVariationLabel(variation, index, strings) {
        if (variation && typeof variation.label === 'string' && variation.label.trim()) {
            return variation.label.trim();
        }

        var template = strings.canvasVariationLabel || '';

        if (template && template.indexOf('%d') !== -1) {
            return template.replace('%d', index + 1);
        }

        return 'Variation ' + (index + 1);
    }

    function recordHistoryEntry(prompt, providerKey, meta) {
        if (!prompt || !providerKey) {
            return;
        }
        var aimentorData = window.aimentorAjax || {};
        if (!aimentorData.historyEndpoint || !aimentorData.restNonce) {
            return;
        }

        var payload = {
            prompt: String(prompt),
            provider: String(providerKey)
        };

        if (meta && typeof meta === 'object') {
            if (meta.task) {
                payload.task = String(meta.task);
            }
            if (meta.tier) {
                payload.tier = String(meta.tier);
            }
            if (meta.model) {
                payload.model = String(meta.model);
            }
            if (meta.origin) {
                payload.origin = String(meta.origin);
            }
            if (typeof meta.tokens !== 'undefined') {
                payload.tokens = parseInt(meta.tokens, 10);
            }
            if (meta.rate_limit) {
                payload.rate_limit = meta.rate_limit;
            }
        }

        if (window.fetch) {
            window.fetch(aimentorData.historyEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': aimentorData.restNonce
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            }).catch(function() {
                // Silently ignore logging failures.
            });
            return;
        }

        if (window.jQuery && jQuery.ajax) {
            jQuery.ajax({
                url: aimentorData.historyEndpoint,
                method: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                processData: false,
                headers: {
                    'X-WP-Nonce': aimentorData.restNonce
                }
            });
        }
    }

    $(document).on('elementor/init', function() {
        var aimentorData = window.aimentorAjax || {};
        var strings = aimentorData.strings || {};
        var providerLabels = aimentorData.providerLabels || {};
        var providerSummaries = aimentorData.providerSummaries || {};
        var providersMeta = aimentorData.providersMeta || {};
        var defaultsData = aimentorData.defaults || {};
        var modelLabels = aimentorData.modelLabels || {};
        var modelPresets = aimentorData.modelPresets || {};
        var isProActive = !!aimentorData.isProActive;
        var savedPromptsData = aimentorData.savedPrompts || {};
        var savedPromptsStore = normalizeSavedPrompts(savedPromptsData);
        var savedPromptSelectNodes = [];
        var knowledgeData = Array.isArray(aimentorData.knowledgePacks) ? aimentorData.knowledgePacks : [];
        var knowledgeStore = normalizeKnowledgePacks(knowledgeData);
        var knowledgeEndpoint = typeof aimentorData.knowledgeEndpoint === 'string' ? aimentorData.knowledgeEndpoint : '';
        var knowledgeSelectNodes = [];
        var promptPresetData = buildPromptPresetData(aimentorData.promptPresets || {});
        var promptPresetLookup = promptPresetData.presets;
        var promptCategoryLookup = promptPresetData.categories;
        var promptCategoryPresetMap = promptPresetData.categoryPresets;
        var savedPromptCollator = (typeof window.Intl !== 'undefined' && typeof window.Intl.Collator === 'function') ? new window.Intl.Collator(undefined, { sensitivity: 'base' }) : null;
        var knowledgeCollator = savedPromptCollator;
        var defaultLayoutSummary = strings.recentLayoutsPreviewMissing || 'Preview unavailable for this layout.';
        var canvasHistoryStore = createCanvasHistoryStore(aimentorData.canvasHistory || [], parseInt(aimentorData.canvasHistoryMax, 10) || 0);
        var frameLibraryEndpoint = typeof aimentorData.frameLibraryEndpoint === 'string' ? aimentorData.frameLibraryEndpoint : '';
        var frameLibraryStore = createFrameLibraryStore(aimentorData.frameLibrary || []);
        var tonePresets = Array.isArray(aimentorData.tonePresets) ? aimentorData.tonePresets.slice() : [];
        var tonePresetLookup = {};
        var defaultTonePresetId = '';

        tonePresets.forEach(function(preset) {
            if (!preset || typeof preset.id === 'undefined') {
                return;
            }

            var id = String(preset.id);
            var label = typeof preset.label === 'string' ? preset.label : id;
            var keywords = typeof preset.keywords === 'string' ? preset.keywords : '';
            var isBrand = !!preset.is_brand;

            tonePresetLookup[id] = {
                id: id,
                label: label,
                keywords: keywords,
                isBrand: isBrand
            };

            if (!defaultTonePresetId && isBrand) {
                defaultTonePresetId = id;
            }
        });

        if (!defaultTonePresetId && tonePresets.length) {
            defaultTonePresetId = String(tonePresets[0].id);
        }

        var defaultToneKeywords = (defaultTonePresetId && tonePresetLookup[defaultTonePresetId]) ? tonePresetLookup[defaultTonePresetId].keywords || '' : '';
        var lastFocusedControlElement = null;

        function recordControlSelection(element) {
            if (!element || typeof element.selectionStart !== 'number' || typeof element.selectionEnd !== 'number') {
                return;
            }

            jQuery(element).data('aimentorSelection', {
                start: element.selectionStart,
                end: element.selectionEnd
            });
        }

        jQuery(document).on('focusin.aimentorControl', '.elementor-control input, .elementor-control textarea', function() {
            lastFocusedControlElement = this;
            recordControlSelection(this);
        });

        jQuery(document).on('keyup.aimentorControl mouseup.aimentorControl select.aimentorControl', '.elementor-control input, .elementor-control textarea', function() {
            if (this === lastFocusedControlElement) {
                recordControlSelection(this);
            }
        });

        function computeCooldownSeconds(rateLimit) {
            if (!rateLimit || typeof rateLimit !== 'object') {
                return 0;
            }

            var keys = ['cooldown_seconds', 'retry_after_seconds', 'reset_requests_seconds', 'reset_tokens_seconds'];
            var maxSeconds = 0;

            keys.forEach(function(key) {
                if (!Object.prototype.hasOwnProperty.call(rateLimit, key)) {
                    return;
                }

                var value = rateLimit[key];

                if (typeof value === 'string') {
                    value = parseFloat(value);
                }

                if (typeof value === 'number' && isFinite(value) && value > 0) {
                    maxSeconds = Math.max(maxSeconds, value);
                }
            });

            return maxSeconds;
        }

        function formatCooldownMessage(rateLimit) {
            if (!rateLimit || typeof rateLimit !== 'object') {
                return '';
            }

            if (typeof rateLimit.message === 'string' && rateLimit.message.trim()) {
                return rateLimit.message.trim();
            }

            var seconds = computeCooldownSeconds(rateLimit);

            if (!seconds) {
                return '';
            }

            var humanReadable = '';

            if (typeof rateLimit.cooldown_human === 'string' && rateLimit.cooldown_human.trim()) {
                humanReadable = rateLimit.cooldown_human.trim();
            } else {
                var rounded = Math.max(1, Math.round(seconds));

                if (rounded === 1 && strings.rateLimitSecondsFallbackSingular) {
                    humanReadable = strings.rateLimitSecondsFallbackSingular.replace('%d', rounded);
                } else if (strings.rateLimitSecondsFallback) {
                    humanReadable = strings.rateLimitSecondsFallback.replace('%d', rounded);
                } else if (rounded === 1) {
                    humanReadable = '1 second';
                } else {
                    humanReadable = rounded + ' seconds';
                }
            }

            return formatString(strings.rateLimitCooldown || 'Please wait %s before trying again.', humanReadable);
        }

        function updateCooldownNotice($notice, rateLimit) {
            if (!$notice || !$notice.length) {
                return;
            }

            var message = formatCooldownMessage(rateLimit);

            if (message) {
                $notice.text(message).show();
            } else {
                $notice.text('').hide();
            }
        }

        function sanitizeCanvasLayout(layout) {
            if (!layout) {
                return '';
            }

            var jsonString = '';

            if (typeof layout === 'string') {
                jsonString = layout;
            } else {
                try {
                    jsonString = JSON.stringify(layout);
                } catch (err) {
                    jsonString = '';
                }
            }

            if (!jsonString) {
                return '';
            }

            try {
                var decoded = JSON.parse(jsonString);
                if (!decoded || typeof decoded !== 'object') {
                    return '';
                }
            } catch (err) {
                return '';
            }

            return jsonString;
        }

        function normalizeCanvasHistoryEntry(entry) {
            if (!entry || typeof entry !== 'object') {
                return null;
            }

            var layoutJson = sanitizeCanvasLayout(entry.layout);

            if (!layoutJson) {
                return null;
            }

            var summaryText = '';

            if (typeof entry.summary === 'string') {
                summaryText = entry.summary.trim();
            }

            var providerKey = sanitizeProvider(entry.provider || '');
            var normalized = {
                id: typeof entry.id === 'string' && entry.id ? entry.id : String(Date.now()),
                summary: summaryText || defaultLayoutSummary,
                provider: providerKey,
                model: typeof entry.model === 'string' ? entry.model.trim() : '',
                task: sanitizeTask(entry.task || 'canvas', true),
                tier: sanitizeTier(entry.tier || ''),
                timestamp: typeof entry.timestamp === 'number' ? entry.timestamp : parseInt(entry.timestamp, 10) || Math.round(Date.now() / 1000),
                layout: layoutJson
            };

            if (!normalized.provider) {
                normalized.provider = providerKey || 'grok';
            }

            return normalized;
        }

        function sanitizeCanvasHistoryList(list) {
            if (!Array.isArray(list)) {
                return [];
            }

            var sanitized = [];

            list.forEach(function(item) {
                var normalized = normalizeCanvasHistoryEntry(item);

                if (!normalized) {
                    return;
                }

                sanitized = sanitized.filter(function(existing) {
                    return existing.layout !== normalized.layout && existing.id !== normalized.id;
                });

                sanitized.push(normalized);
            });

            return sanitized;
        }

        function createCanvasHistoryStore(initialEntries, maxItems) {
            var max = parseInt(maxItems, 10);

            if (!isFinite(max) || max <= 0) {
                max = 6;
            }

            var entries = sanitizeCanvasHistoryList(initialEntries).slice(0, max);
            var subscribers = [];

            function syncGlobal() {
                window.aimentorAjax = window.aimentorAjax || {};
                window.aimentorAjax.canvasHistory = entries.slice();
            }

            function notify() {
                syncGlobal();

                subscribers.forEach(function(callback) {
                    if (typeof callback !== 'function') {
                        return;
                    }

                    try {
                        callback(entries.slice());
                    } catch (err) {
                        // Ignore subscriber errors so the store keeps working for others.
                    }
                });
            }

            function subscribe(callback) {
                if (typeof callback === 'function') {
                    subscribers.push(callback);
                    callback(entries.slice());
                }

                return function() {
                    subscribers = subscribers.filter(function(candidate) {
                        return candidate !== callback;
                    });
                };
            }

            function set(list) {
                entries = sanitizeCanvasHistoryList(list).slice(0, max);
                notify();
            }

            function add(entry) {
                var normalized = normalizeCanvasHistoryEntry(entry);

                if (!normalized) {
                    return;
                }

                entries = [normalized].concat(entries.filter(function(existing) {
                    return existing.layout !== normalized.layout && existing.id !== normalized.id;
                }));

                if (entries.length > max) {
                    entries = entries.slice(0, max);
                }

                notify();
            }

            notify();

            return {
                get: function() {
                    return entries.slice();
                },
                subscribe: subscribe,
                set: set,
                add: add,
                max: max
            };
        }

        function sanitizeFrameItem(item) {
            if (!item || typeof item !== 'object') {
                return null;
            }

            var id = item.id;

            if (typeof id !== 'string' && typeof id !== 'number') {
                return null;
            }

            var frame = {
                id: String(id),
                title: String(item.title || ''),
                summary: String(item.summary || ''),
                provider: String(item.provider || ''),
                model: String(item.model || ''),
                prompt: String(item.prompt || ''),
                task: String(item.task || ''),
                tier: String(item.tier || ''),
                sections: [],
                preview: '',
                layout: '',
                modified: String(item.modified || '')
            };

            if (Array.isArray(item.sections)) {
                frame.sections = item.sections.map(function(section) {
                    return String(section || '').trim();
                }).filter(function(section) {
                    return section.length > 0;
                });
            }

            if (item.preview && typeof item.preview === 'object' && item.preview.url) {
                frame.preview = String(item.preview.url);
            }

            if (item.layout && typeof item.layout === 'string') {
                try {
                    var parsed = JSON.parse(item.layout);
                    if (parsed && typeof parsed === 'object') {
                        frame.layout = JSON.stringify(parsed);
                    }
                } catch (error) {
                    frame.layout = '';
                }
            } else if (item.layout && typeof item.layout === 'object') {
                try {
                    frame.layout = JSON.stringify(item.layout);
                } catch (error) {
                    frame.layout = '';
                }
            }

            return frame;
        }

        function sanitizeFrameList(list) {
            if (!Array.isArray(list)) {
                return [];
            }

            return list.map(sanitizeFrameItem).filter(Boolean);
        }

        function createFrameLibraryStore(initialItems) {
            var items = sanitizeFrameList(initialItems);
            var listeners = [];

            function syncGlobalFrames() {
                window.aimentorAjax = window.aimentorAjax || {};
                window.aimentorAjax.frameLibrary = items.slice();
            }

            function notify() {
                syncGlobalFrames();
                var snapshot = items.slice();
                listeners.slice().forEach(function(listener) {
                    if (typeof listener === 'function') {
                        try {
                            listener(snapshot);
                        } catch (error) {
                            // Swallow listener errors to avoid breaking others.
                        }
                    }
                });
            }

            return {
                get: function() {
                    return items.slice();
                },
                set: function(list) {
                    items = sanitizeFrameList(list);
                    notify();
                },
                subscribe: function(listener) {
                    if (typeof listener !== 'function') {
                        return function() {};
                    }

                    listeners.push(listener);
                    listener(items.slice());

                    return function() {
                        listeners = listeners.filter(function(existing) {
                            return existing !== listener;
                        });
                    };
                }
            };
        }

        function persistCanvasHistory(responseData, summaryText) {
            if (!responseData || !responseData.canvas_json) {
                return;
            }

            if (typeof $.post !== 'function') {
                return;
            }

            var layoutJson = sanitizeCanvasLayout(responseData.canvas_json);

            if (!layoutJson) {
                return;
            }

            if (!aimentorData.canvasHistoryNonce || !aimentorData.ajaxurl) {
                return;
            }

            var summary = '';

            if (typeof summaryText === 'string') {
                summary = summaryText.replace(/\s+/g, ' ').trim();
            }

            var requestPayload = {
                action: 'aimentor_store_canvas_history',
                nonce: aimentorData.canvasHistoryNonce,
                layout: layoutJson,
                summary: summary,
                provider: responseData.provider || '',
                model: responseData.model || '',
                task: responseData.task || 'canvas',
                tier: responseData.tier || '',
                origin: responseData.origin || 'ajax',
                tokens: typeof responseData.tokens !== 'undefined' ? responseData.tokens : 0
            };

            if (responseData.rate_limit) {
                try {
                    requestPayload.rate_limit = JSON.stringify(responseData.rate_limit);
                } catch (error) {
                    // Ignore serialization issues.
                }
            }

            $.post(aimentorData.ajaxurl, requestPayload).done(function(storeResponse) {
                if (!storeResponse || !storeResponse.success || !storeResponse.data) {
                    return;
                }

                if (storeResponse.data.history) {
                    canvasHistoryStore.set(storeResponse.data.history);
                    return;
                }

                if (storeResponse.data.entry) {
                    canvasHistoryStore.add(storeResponse.data.entry);
                }
            });
        }

        function renderCanvasVariations($target, variations, options) {
            ensureVariationStyles();

            if (!$target || !$target.length) {
                return;
            }

            options = options || {};
            var localStrings = strings || {};

            $target.empty();

            if (!Array.isArray(variations) || !variations.length) {
                var emptyMessage = localStrings.canvasVariationsEmpty || 'No layouts available yet. Try generating again.';
                $target.html('<p>' + escapeHtml(emptyMessage) + '</p>');
                return;
            }

            var wrapper = $('<div class="aimentor-variations"></div>');
            var headingText = localStrings.canvasVariationsHeading || 'Choose a layout style';
            wrapper.append('<h4 class="aimentor-variations__title">' + escapeHtml(headingText) + '</h4>');

            if (localStrings.canvasVariationsDescription) {
                wrapper.append('<p class="aimentor-variations__description">' + escapeHtml(localStrings.canvasVariationsDescription) + '</p>');
            }

            if (localStrings.canvasVariationsCount && localStrings.canvasVariationsCount.indexOf('%d') !== -1) {
                wrapper.append('<p class="aimentor-variations__count">' + escapeHtml(localStrings.canvasVariationsCount.replace('%d', variations.length)) + '</p>');
            }

            var feedback = $('<p class="aimentor-variation-feedback" aria-live="polite"></p>');
            if (options.summaryText) {
                var successPrefix = localStrings.successPrefix ? localStrings.successPrefix + ' ' : '';
                feedback.text(successPrefix + options.summaryText).show();
            } else {
                feedback.hide();
            }
            wrapper.append(feedback);

            var grid = $('<div class="aimentor-variations__grid"></div>');

            variations.forEach(function(variation, index) {
                var label = resolveVariationLabel(variation, index, localStrings);
                var summary = (variation && typeof variation.summary === 'string') ? variation.summary.trim() : '';
                var metaText = formatCanvasVariationMeta(variation ? variation.meta : null, localStrings);
                var previewText = summary || localStrings.canvasVariationPreviewPlaceholder || 'Preview not available for this layout yet.';
                var card = $('<article class="aimentor-variation-card" role="group" aria-label="' + escapeHtml(label) + '"></article>');
                var header = $('<div class="aimentor-variation-card__header"></div>');
                header.append('<span class="aimentor-variation-card__label">' + escapeHtml(label) + '</span>');
                card.append(header);
                card.append('<div class="aimentor-variation-card__preview">' + escapeHtml(previewText) + '</div>');
                if (summary) {
                    card.append('<p class="aimentor-variation-card__summary">' + escapeHtml(summary) + '</p>');
                }
                if (metaText) {
                    card.append('<p class="aimentor-variation-card__meta">' + escapeHtml(metaText) + '</p>');
                }
                var buttonLabel = localStrings.canvasVariationActionLabel || localStrings.recentLayoutsUse || 'Insert layout';
                var button = $('<button type="button" class="button button-primary aimentor-variation-card__button">' + escapeHtml(buttonLabel) + '</button>');
                var footer = $('<div class="aimentor-variation-card__footer"></div>').append(button);
                card.append(footer);

                button.on('click', function() {
                    handleVariationInsert(variation, {
                        label: label,
                        button: button,
                        card: card,
                        feedback: feedback,
                        options: options
                    });
                });

                grid.append(card);
            });

            wrapper.append(grid);
            $target.append(wrapper);
        }

        function handleVariationInsert(variation, context) {
            if (!variation || !variation.layout) {
                return;
            }

            context = context || {};
            var button = context.button;

            if (button && button.length && button.prop('disabled')) {
                return;
            }

            if (button && button.length) {
                button.prop('disabled', true);
            }

            var layoutForInsert = cloneLayout(variation.layout);
            var layoutForHistory = cloneLayout(variation.layout);

            if (!layoutForInsert || !layoutForHistory) {
                if (button && button.length) {
                    button.prop('disabled', false);
                }
                return;
            }

            if (window.elementorFrontend && elementorFrontend.elementsHandler && typeof elementorFrontend.elementsHandler.addElements === 'function') {
                try {
                    elementorFrontend.elementsHandler.addElements(layoutForInsert);
                } catch (error) {
                    if (button && button.length) {
                        button.prop('disabled', false);
                    }
                    return;
                }
            }

            var options = context.options || {};
            var responseData = options.responseData || {};
            var historySummary = '';

            if (variation.summary && context.label) {
                historySummary = context.label + ' — ' + variation.summary;
            } else if (variation.summary) {
                historySummary = variation.summary;
            } else if (context.label && options.summaryText) {
                historySummary = context.label + ' — ' + options.summaryText;
            } else {
                historySummary = options.summaryText || context.label || '';
            }

            persistCanvasHistory({
                canvas_json: layoutForHistory,
                provider: responseData.provider || options.provider || '',
                model: responseData.model || options.model || '',
                task: responseData.task || 'canvas',
                tier: responseData.tier || options.tier || ''
            }, historySummary);

            if (context.card && context.card.length) {
                context.card.addClass('is-inserted');
            }

            if (context.feedback && context.feedback.length) {
                var message = '';
                if (strings.canvasVariationInsertedNamed && strings.canvasVariationInsertedNamed.indexOf('%s') !== -1) {
                    message = strings.canvasVariationInsertedNamed.replace('%s', context.label || '');
                } else {
                    message = strings.canvasVariationInserted || 'Layout inserted!';
                }
                context.feedback.text(message).show();
            }

            setTimeout(function() {
                if (button && button.length) {
                    button.prop('disabled', false);
                }
            }, 800);
        }

        function attachHistoryCarousel($scope) {
            if (!$scope || !$scope.length) {
                return;
            }

            var $container = $scope.find('.aimentor-layout-history');

            if (!$container.length || $container.data('aimentorCarouselInit')) {
                return;
            }

            $container.data('aimentorCarouselInit', true);

            var $viewport = $container.find('.aimentor-layout-history__viewport');
            var $empty = $container.find('.aimentor-layout-history__empty');
            var $navButtons = $container.find('.aimentor-layout-history__nav-button');
            var $title = $container.find('.aimentor-layout-history__title');
            var state = { index: 0 };
            var unsubscribe = null;

            if (strings.recentLayoutsHeading && $title.length) {
                $title.text(strings.recentLayoutsHeading);
            }

            if (strings.recentLayoutsEmpty) {
                if ($empty.length) {
                    $empty.text(strings.recentLayoutsEmpty);
                }
                $container.attr('data-empty-text', strings.recentLayoutsEmpty);
            }

            function render(entries) {
                entries = Array.isArray(entries) ? entries : [];

                if (!entries.length) {
                    $viewport.empty();
                    $empty.show();
                    $navButtons.prop('disabled', true).attr('aria-disabled', 'true');
                    return;
                }

                $empty.hide();

                if (state.index >= entries.length) {
                    state.index = 0;
                }

                if (state.index < 0) {
                    state.index = 0;
                }

                var entry = entries[state.index];
                var providerLabel = providerLabels[entry.provider] || entry.provider || '';
                var metaParts = [];

                if (entry.tier === 'quality' && (strings.qualityLabel || '').length) {
                    metaParts.push(strings.qualityLabel);
                } else if (entry.tier === 'fast' && (strings.fastLabel || '').length) {
                    metaParts.push(strings.fastLabel);
                }

                if (providerLabel) {
                    metaParts.push(providerLabel);
                }

                if (entry.model) {
                    metaParts.push(entry.model);
                }

                var separator = strings.recentLayoutsMetaSeparator || strings.summarySeparator || ' • ';
                var metaText = metaParts.join(separator);
                var timestampText = '';

                if (entry.timestamp) {
                    var timestampDate = new Date(entry.timestamp * 1000);

                    if (!isNaN(timestampDate.getTime())) {
                        var formattedDate = timestampDate.toLocaleString();

                        if (strings.recentLayoutsTimestamp) {
                            timestampText = strings.recentLayoutsTimestamp.replace('%s', formattedDate);
                        } else {
                            timestampText = formattedDate;
                        }
                    }
                }

                var subtitleParts = [];

                if (timestampText) {
                    subtitleParts.push(timestampText);
                }

                if (metaText) {
                    subtitleParts.push(metaText);
                }

                var subtitle = subtitleParts.join(separator);
                var summary = entry.summary || defaultLayoutSummary;
                var html = '' +
                    '<article class="aimentor-layout-history__item" role="option" aria-selected="true" data-entry-id="' + escapeHtml(entry.id) + '">' +
                    '  <div class="aimentor-layout-history__preview">' + escapeHtml(summary) + '</div>' +
                    (subtitle ? '  <p class="aimentor-layout-history__meta">' + escapeHtml(subtitle) + '</p>' : '') +
                    '  <button type="button" class="button button-secondary aimentor-layout-history__apply">' + escapeHtml(strings.recentLayoutsUse || 'Insert layout') + '</button>' +
                    '</article>';

                $viewport.html(html);

                var disableNav = entries.length <= 1;
                $navButtons.prop('disabled', disableNav).attr('aria-disabled', disableNav ? 'true' : 'false');
            }

            $navButtons.off('click.aimentor').on('click.aimentor', function() {
                var entries = canvasHistoryStore.get();

                if (!entries.length) {
                    return;
                }

                var isNext = $(this).hasClass('aimentor-layout-history__nav-button--next');
                state.index = (state.index + (isNext ? 1 : -1) + entries.length) % entries.length;
                render(entries);
            });

            $container.off('click.aimentorHistory').on('click.aimentorHistory', '.aimentor-layout-history__apply', function(event) {
                event.preventDefault();

                var $item = $(this).closest('.aimentor-layout-history__item');
                var entryId = $item.data('entry-id');

                if (!entryId) {
                    return;
                }

                var entries = canvasHistoryStore.get();
                var entry = null;

                for (var i = 0; i < entries.length; i++) {
                    if (entries[i] && entries[i].id === entryId) {
                        entry = entries[i];
                        break;
                    }
                }

                if (!entry) {
                    return;
                }

                var parsedLayout = null;

                try {
                    parsedLayout = JSON.parse(entry.layout);
                } catch (err) {
                    parsedLayout = null;
                }

                if (!parsedLayout || !window.elementorFrontend || !elementorFrontend.elementsHandler) {
                    return;
                }

                elementorFrontend.elementsHandler.addElements(parsedLayout);
            });

            unsubscribe = canvasHistoryStore.subscribe(function(entries) {
                entries = Array.isArray(entries) ? entries : [];

                if (!entries.length) {
                    state.index = 0;
                } else if (state.index >= entries.length) {
                    state.index = 0;
                }

                render(entries);
            });

            $container.data('aimentorCarouselUnsub', unsubscribe);

            $container.on('remove.aimentor', function() {
                if (typeof unsubscribe === 'function') {
                    unsubscribe();
                    unsubscribe = null;
                }
            });
        }

        function attachPlaceholderTriggers($scope) {
            if (!$scope || !$scope.length) {
                return;
            }

            var $aside = $scope.find('.aimentor-modal__aside');

            if (!$aside.length || $aside.data('aimentorAsideInit')) {
                return;
            }

            $aside.data('aimentorAsideInit', true);

            var $tabs = $aside.find('.aimentor-modal__aside-tab');
            var $panels = $aside.find('.aimentor-modal__panel');

            function activate(panelName) {
                if (!panelName) {
                    panelName = 'history';
                }

                $tabs.each(function() {
                    var $tab = $(this);
                    var isTarget = $tab.data('panel') === panelName;
                    $tab.toggleClass('is-active', isTarget).attr('aria-selected', isTarget ? 'true' : 'false');
                });

                $panels.each(function() {
                    var $panel = $(this);
                    var isTarget = $panel.data('panel') === panelName;
                    $panel.toggleClass('is-active', isTarget).attr('aria-hidden', isTarget ? 'false' : 'true');
                });
            }

            $tabs.off('click.aimentorTabs').on('click.aimentorTabs', function(event) {
                event.preventDefault();
                activate($(this).data('panel'));
            });

            var initialPanel = $tabs.filter('.is-active').first().data('panel') || 'history';
            activate(initialPanel);
        }

        function attachFrameLibrary($scope) {
            if (!$scope || !$scope.length) {
                return;
            }

            var $container = $scope.find('.aimentor-frame-library');

            if (!$container.length || $container.data('aimentorFrameInit')) {
                return;
            }

            $container.data('aimentorFrameInit', true);

            ensureFrameLibraryStyles();

            var $list = $container.find('.aimentor-frame-library__list');
            var $empty = $container.find('.aimentor-frame-library__empty');
            var $error = $container.find('.aimentor-frame-library__error');
            var $loading = $container.find('.aimentor-frame-library__loading');
            var $updated = $container.find('.aimentor-frame-library__updated');
            var emptyText = $container.data('emptyText') || ($empty.text() || strings.frameLibraryEmpty || '');

            function findFrameById(frameId) {
                var frames = frameLibraryStore.get();

                for (var i = 0; i < frames.length; i++) {
                    if (frames[i] && frames[i].id === frameId) {
                        return frames[i];
                    }
                }

                return null;
            }

            function parseFrameLayout(frame) {
                if (!frame || !frame.layout) {
                    return null;
                }

                try {
                    var parsed = JSON.parse(frame.layout);
                    if (parsed && typeof parsed === 'object') {
                        return parsed;
                    }
                } catch (error) {
                    return null;
                }

                return null;
            }

            function formatUpdatedLabel(value) {
                if (!value) {
                    return '';
                }

                var date = new Date(value);

                if (isNaN(date.getTime())) {
                    var timestamp = parseInt(value, 10);
                    if (isFinite(timestamp)) {
                        date = new Date(timestamp * 1000);
                    }
                }

                if (isNaN(date.getTime())) {
                    return '';
                }

                var formatted = date.toLocaleString();

                if (strings.frameLibraryUpdated) {
                    return strings.frameLibraryUpdated.replace('%s', formatted);
                }

                return 'Updated ' + formatted;
            }

            function buildFrameCard(frame) {
                var title = frame.title || strings.frameLibraryHeading || 'Frame';
                var summary = frame.summary || '';
                var sections = Array.isArray(frame.sections) ? frame.sections : [];
                var metaParts = [];
                var providerName = providerLabels[frame.provider] || frame.provider || '';

                if (providerName) {
                    metaParts.push(providerName);
                }

                if (frame.model) {
                    metaParts.push(frame.model);
                }

                if (frame.tier) {
                    metaParts.push(frame.tier);
                }

                var metaText = metaParts.join(strings.recentLayoutsMetaSeparator || strings.summarySeparator || ' • ');
                var previewHtml = '';

                if (frame.preview) {
                    previewHtml = '<div class="aimentor-frame-library__preview"><img src="' + escapeHtml(frame.preview) + '" alt="" /></div>';
                } else {
                    var placeholder = strings.frameLibraryPreviewPending || strings.recentLayoutsPreviewMissing || 'Preview unavailable';
                    previewHtml = '<div class="aimentor-frame-library__preview"><span class="aimentor-frame-library__preview-placeholder">' + escapeHtml(placeholder) + '</span></div>';
                }

                var sectionsHtml = '';

                if (sections.length) {
                    var sectionsLabel = strings.frameLibrarySectionsLabel || 'Suggested sections';
                    sectionsHtml = '<div class="aimentor-frame-library__sections-wrapper"><strong>' + escapeHtml(sectionsLabel) + '</strong><ul class="aimentor-frame-library__sections">' + sections.map(function(section) {
                        return '<li>' + escapeHtml(section) + '</li>';
                    }).join('') + '</ul></div>';
                }

                var actionsHtml = '<div class="aimentor-frame-library__actions">' +
                    '<button type="button" class="button button-secondary aimentor-frame-library__insert">' + escapeHtml(strings.frameLibraryInsert || 'Insert frame') + '</button>';

                if (frame.prompt) {
                    actionsHtml += '<button type="button" class="button button-link aimentor-frame-library__seed">' + escapeHtml(strings.frameLibrarySeed || 'Seed prompt') + '</button>';
                }

                actionsHtml += '</div>';

                return '<article class="aimentor-frame-library__card" data-frame-id="' + escapeHtml(frame.id) + '">' +
                    previewHtml +
                    '<div class="aimentor-frame-library__content">' +
                    '<h4 class="aimentor-frame-library__title">' + escapeHtml(title) + '</h4>' +
                    (summary ? '<p class="aimentor-frame-library__summary">' + escapeHtml(summary) + '</p>' : '') +
                    sectionsHtml +
                    (metaText ? '<p class="aimentor-frame-library__meta">' + escapeHtml(metaText) + '</p>' : '') +
                    actionsHtml +
                    '</div></article>';
            }

            function render(items) {
                items = Array.isArray(items) ? items : [];

                if (!items.length) {
                    $list.empty();
                    if (emptyText) {
                        $empty.text(emptyText).show();
                    } else {
                        $empty.show();
                    }
                    $error.hide();
                    if ($updated.length) {
                        $updated.hide();
                    }
                    return;
                }

                $empty.hide();

                var html = items.map(buildFrameCard).join('');
                $list.html(html);
                $error.hide();

                if ($updated.length) {
                    var updatedLabel = formatUpdatedLabel(items[0] && items[0].modified ? items[0].modified : '');

                    if (updatedLabel) {
                        $updated.text(updatedLabel).show();
                    } else {
                        $updated.hide();
                    }
                }
            }

            var unsubscribe = frameLibraryStore.subscribe(render);

            $container.on('remove.aimentorFrame', function() {
                if (typeof unsubscribe === 'function') {
                    unsubscribe();
                }
            });

            if (frameLibraryEndpoint && window.fetch) {
                if ($loading.length) {
                    $loading.show();
                }

                var requestOptions = {
                    credentials: 'same-origin'
                };

                if (aimentorData.restNonce) {
                    requestOptions.headers = { 'X-WP-Nonce': aimentorData.restNonce };
                }

                window.fetch(frameLibraryEndpoint, requestOptions).then(function(response) {
                    if (!response.ok) {
                        throw new Error('Failed to load frames');
                    }
                    return response.json();
                }).then(function(payload) {
                    var items = payload && Array.isArray(payload.items) ? payload.items : [];
                    frameLibraryStore.set(items);

                    if (items.length && $updated.length) {
                        var updatedLabel = formatUpdatedLabel(items[0].modified || '');

                        if (updatedLabel) {
                            $updated.text(updatedLabel).show();
                        } else {
                            $updated.hide();
                        }
                    }

                    $error.hide();
                }).catch(function() {
                    if (!$list.children().length && $error.length) {
                        $error.show();
                    }
                }).finally(function() {
                    if ($loading.length) {
                        $loading.hide();
                    }
                });
            } else if ($loading.length) {
                $loading.hide();
            }

            $container.on('click', '.aimentor-frame-library__insert', function(event) {
                event.preventDefault();

                var $card = $(this).closest('.aimentor-frame-library__card');
                var frameId = $card.data('frame-id');
                var frame = findFrameById(frameId);

                if (!frame) {
                    return;
                }

                var layoutObject = parseFrameLayout(frame);

                if (!layoutObject || !window.elementorFrontend || !elementorFrontend.elementsHandler || typeof elementorFrontend.elementsHandler.addElements !== 'function') {
                    return;
                }

                elementorFrontend.elementsHandler.addElements(layoutObject);
            });

            $container.on('click', '.aimentor-frame-library__seed', function(event) {
                event.preventDefault();

                var $card = $(this).closest('.aimentor-frame-library__card');
                var frameId = $card.data('frame-id');
                var frame = findFrameById(frameId);

                if (!frame || !frame.prompt) {
                    return;
                }

                var $promptField = $scope.find('textarea[data-setting="aimentor_prompt_text"]');
                mergePromptText($promptField, frame.prompt);
            });
        }

        ensureBadgeStyles();
        ensureFrameLibraryStyles();

        function normalizeSavedPromptEntry(entry, scope) {
            if (!entry || typeof entry !== 'object') {
                return null;
            }

            var promptValue = typeof entry.prompt === 'string' ? entry.prompt.trim() : '';
            var idValue = typeof entry.id === 'string' ? entry.id : '';
            var labelValue = typeof entry.label === 'string' ? entry.label.trim() : '';
            var entryScope = scope === 'global' ? 'global' : 'user';

            if (!promptValue || !idValue) {
                return null;
            }

            if (!labelValue) {
                labelValue = promptValue.length > 60 ? promptValue.slice(0, 57) + '…' : promptValue;
            }

            return {
                id: idValue,
                label: labelValue,
                prompt: promptValue,
                scope: entryScope
            };
        }

        function sortSavedPromptEntries(list) {
            if (!Array.isArray(list)) {
                return [];
            }

            return list.slice().sort(function(a, b) {
                var labelA = a && typeof a.label === 'string' ? a.label : '';
                var labelB = b && typeof b.label === 'string' ? b.label : '';

                if (savedPromptCollator) {
                    return savedPromptCollator.compare(labelA, labelB);
                }

                return labelA.localeCompare(labelB);
            });
        }

        function normalizeSavedPrompts(data) {
            var normalized = { global: [], user: [] };

            if (!data || typeof data !== 'object') {
                return normalized;
            }

            if (Array.isArray(data.user)) {
                normalized.user = sortSavedPromptEntries(data.user.map(function(entry) {
                    return normalizeSavedPromptEntry(entry, 'user');
                }).filter(Boolean));
            }

            if (Array.isArray(data.global)) {
                normalized.global = sortSavedPromptEntries(data.global.map(function(entry) {
                    return normalizeSavedPromptEntry(entry, 'global');
                }).filter(Boolean));
            }

            return normalized;
        }

        function cloneSavedPromptEntry(entry) {
            return {
                id: entry.id,
                label: entry.label,
                prompt: entry.prompt,
                scope: entry.scope
            };
        }

        function cloneSavedPrompts() {
            return {
                global: savedPromptsStore.global.map(cloneSavedPromptEntry),
                user: savedPromptsStore.user.map(cloneSavedPromptEntry)
            };
        }

        function getAllSavedPrompts() {
            return savedPromptsStore.user.concat(savedPromptsStore.global);
        }

        function applySavedPromptToField(entry, $field) {
            if (!entry || !$field || !$field.length) {
                return;
            }

            $field.val(entry.prompt).trigger('input');
        }

        function bindSavedPromptSelect($select, $field) {
            if (!$select || !$select.length) {
                return;
            }

            registerSavedPromptSelect($select);

            $select.off('change.aimentor').on('change.aimentor', function() {
                var selectedPrompt = findSavedPromptById($(this).val());
                applySavedPromptToField(selectedPrompt, $field);
            });
        }

        function findSavedPromptById(id) {
            if (!id) {
                return null;
            }

            var prompts = getAllSavedPrompts();
            for (var i = 0; i < prompts.length; i++) {
                if (prompts[i] && prompts[i].id === id) {
                    return prompts[i];
                }
            }

            return null;
        }

        function getActiveDocumentConfig() {
            var doc = null;
            if (window.elementor && elementor.documents && typeof elementor.documents.getCurrent === 'function') {
                try {
                    var current = elementor.documents.getCurrent();
                    if (current) {
                        if (current.config) {
                            doc = current.config;
                        } else if (current.document && current.document.config) {
                            doc = current.document.config;
                        } else {
                            doc = current;
                        }
                    }
                } catch (err) {
                    doc = null;
                }
            }

            if (!doc && window.elementor && elementor.config && elementor.config.document) {
                doc = elementor.config.document;
            }

            if (!doc && window.elementorCommon && elementorCommon.config && elementorCommon.config.document) {
                doc = elementorCommon.config.document;
            }

            if (!doc) {
                return {};
            }

            var base;
            try {
                base = JSON.parse(JSON.stringify(doc));
            } catch (err) {
                base = Object.assign({}, doc);
            }

            if (!base.page_settings) {
                if (doc.page_settings && typeof doc.page_settings === 'object') {
                    base.page_settings = doc.page_settings;
                } else if (doc.settings && typeof doc.settings === 'object') {
                    base.page_settings = doc.settings;
                } else if (doc.config && doc.config.page_settings && typeof doc.config.page_settings === 'object') {
                    base.page_settings = doc.config.page_settings;
                }
            }

            return base;
        }

        function extractTemplateFromConfig(docConfig) {
            if (!docConfig || typeof docConfig !== 'object') {
                return '';
            }

            var sources = [];
            if (docConfig.page_settings && typeof docConfig.page_settings === 'object') {
                sources.push(docConfig.page_settings);
            }
            if (docConfig.settings && typeof docConfig.settings === 'object') {
                sources.push(docConfig.settings);
            }
            if (docConfig.config && docConfig.config.page_settings && typeof docConfig.config.page_settings === 'object') {
                sources.push(docConfig.config.page_settings);
            }

            for (var i = 0; i < sources.length; i++) {
                var candidate = sources[i];
                if (!candidate) {
                    continue;
                }
                var templateValue = '';
                if (typeof candidate.template === 'string') {
                    templateValue = candidate.template;
                } else if (typeof candidate.post_template === 'string') {
                    templateValue = candidate.post_template;
                }
                if (templateValue && templateValue !== 'default') {
                    return templateValue;
                }
            }

            return '';
        }

        function extractPostTypesFromConfig(docConfig) {
            if (!docConfig || typeof docConfig !== 'object') {
                return [];
            }

            var values = [];
            var seen = {};
            var candidates = [docConfig];

            if (docConfig.config && typeof docConfig.config === 'object') {
                candidates.push(docConfig.config);
            }
            if (docConfig.panel && typeof docConfig.panel === 'object') {
                candidates.push(docConfig.panel);
            }
            if (docConfig.page_settings && typeof docConfig.page_settings === 'object') {
                candidates.push(docConfig.page_settings);
            }

            var fields = ['post_type', 'type', 'postType', 'post_type_slug', 'postTypeSlug', 'main_post_type', 'source', 'sub_type', 'subType'];

            candidates.forEach(function(candidate) {
                if (!candidate || typeof candidate !== 'object') {
                    return;
                }
                fields.forEach(function(field) {
                    var value = candidate[field];
                    if (typeof value === 'string' && value) {
                        var normalized = value.toLowerCase();
                        if (!seen[normalized]) {
                            seen[normalized] = true;
                            values.push(normalized);
                        }
                    }
                });
            });

            return values;
        }

        function buildDocumentContextCandidates() {
            var docConfig = getActiveDocumentConfig();
            return {
                template: extractTemplateFromConfig(docConfig),
                postTypes: extractPostTypesFromConfig(docConfig)
            };
        }

        function resolveDocumentDefaults(defaultsConfig) {
            var contexts = defaultsConfig && typeof defaultsConfig.contexts === 'object' ? defaultsConfig.contexts : {};
            var pageTypes = contexts.page_types && typeof contexts.page_types === 'object' ? contexts.page_types : {};
            var globalDefault = contexts.default && typeof contexts.default === 'object' ? contexts.default : {};
            var fallbackProvider = typeof globalDefault.provider === 'string' ? globalDefault.provider : '';
            var fallbackModel = typeof globalDefault.model === 'string' ? globalDefault.model : '';
            var candidates = buildDocumentContextCandidates();
            var template = candidates.template;
            var postTypes = Array.isArray(candidates.postTypes) ? candidates.postTypes : [];

            if (template) {
                var prioritized = postTypes.slice();
                prioritized.push('__global__');
                var inspected = {};

                for (var i = 0; i < prioritized.length; i++) {
                    var slug = prioritized[i];
                    if (!slug || inspected[slug]) {
                        continue;
                    }
                    inspected[slug] = true;

                    var typeEntry = pageTypes[slug];
                    if (!typeEntry || typeof typeEntry !== 'object') {
                        continue;
                    }

                    var templateMap = typeEntry.templates && typeof typeEntry.templates === 'object' ? typeEntry.templates : {};
                    var templateEntry = templateMap[template];
                    if (!templateEntry || typeof templateEntry !== 'object') {
                        continue;
                    }

                    var baseProvider = typeof typeEntry.provider === 'string' ? typeEntry.provider : fallbackProvider;
                    var baseModel = typeof typeEntry.model === 'string' ? typeEntry.model : fallbackModel;
                    var provider = typeof templateEntry.provider === 'string' && templateEntry.provider ? templateEntry.provider : baseProvider;
                    var model = typeof templateEntry.model === 'string' && templateEntry.model ? templateEntry.model : baseModel;

                    return {
                        key: 'template:' + template,
                        provider: provider || '',
                        model: model || ''
                    };
                }

                var pageTypeKeys = Object.keys(pageTypes);
                for (var j = 0; j < pageTypeKeys.length; j++) {
                    var fallbackEntry = pageTypes[pageTypeKeys[j]];
                    if (!fallbackEntry || typeof fallbackEntry !== 'object') {
                        continue;
                    }
                    var fallbackTemplates = fallbackEntry.templates && typeof fallbackEntry.templates === 'object' ? fallbackEntry.templates : {};
                    var fallbackTemplateEntry = fallbackTemplates[template];
                    if (!fallbackTemplateEntry || typeof fallbackTemplateEntry !== 'object') {
                        continue;
                    }
                    var fallbackBaseProvider = typeof fallbackEntry.provider === 'string' ? fallbackEntry.provider : fallbackProvider;
                    var fallbackBaseModel = typeof fallbackEntry.model === 'string' ? fallbackEntry.model : fallbackModel;
                    var fallbackProviderValue = typeof fallbackTemplateEntry.provider === 'string' && fallbackTemplateEntry.provider ? fallbackTemplateEntry.provider : fallbackBaseProvider;
                    var fallbackModelValue = typeof fallbackTemplateEntry.model === 'string' && fallbackTemplateEntry.model ? fallbackTemplateEntry.model : fallbackBaseModel;
                    return {
                        key: 'template:' + template,
                        provider: fallbackProviderValue || '',
                        model: fallbackModelValue || ''
                    };
                }
            }

            if (postTypes.length) {
                for (var k = 0; k < postTypes.length; k++) {
                    var postTypeSlug = postTypes[k];
                    if (!postTypeSlug) {
                        continue;
                    }
                    var postTypeEntry = pageTypes[postTypeSlug];
                    if (!postTypeEntry || typeof postTypeEntry !== 'object') {
                        continue;
                    }
                    var providerValue = typeof postTypeEntry.provider === 'string' && postTypeEntry.provider ? postTypeEntry.provider : fallbackProvider;
                    var modelValue = typeof postTypeEntry.model === 'string' && postTypeEntry.model ? postTypeEntry.model : fallbackModel;
                    if (providerValue || modelValue) {
                        return {
                            key: 'post_type:' + postTypeSlug,
                            provider: providerValue || '',
                            model: modelValue || ''
                        };
                    }
                }
            }

            if (contexts.default && typeof contexts.default === 'object') {
                return {
                    key: 'default',
                    provider: fallbackProvider || '',
                    model: fallbackModel || ''
                };
            }

            return { key: 'default', provider: '', model: '' };
        }

        function deriveModelPreset(providerKey, modelKey) {
            if (!providerKey || !modelKey) {
                return null;
            }

            var providerEntry = modelPresets[providerKey];
            if (!providerEntry || typeof providerEntry !== 'object') {
                return null;
            }

            var preferredTasks = ['content', 'canvas'];
            var preferredTiers = ['fast', 'quality'];

            for (var i = 0; i < preferredTasks.length; i++) {
                var task = preferredTasks[i];
                var taskEntry = providerEntry[task];
                if (!taskEntry || typeof taskEntry !== 'object') {
                    continue;
                }
                for (var j = 0; j < preferredTiers.length; j++) {
                    var tier = preferredTiers[j];
                    if (taskEntry[tier] === modelKey) {
                        return { task: task, tier: tier };
                    }
                }

                var tierKeys = Object.keys(taskEntry);
                for (var k = 0; k < tierKeys.length; k++) {
                    var tierKey = tierKeys[k];
                    if (taskEntry[tierKey] === modelKey) {
                        return { task: task, tier: tierKey };
                    }
                }
            }

            var fallbackTasks = Object.keys(providerEntry);
            for (var index = 0; index < fallbackTasks.length; index++) {
                var fallbackTask = fallbackTasks[index];
                var fallbackEntry = providerEntry[fallbackTask];
                if (!fallbackEntry || typeof fallbackEntry !== 'object') {
                    continue;
                }
                var fallbackTiers = Object.keys(fallbackEntry);
                for (var t = 0; t < fallbackTiers.length; t++) {
                    var fallbackTier = fallbackTiers[t];
                    if (fallbackEntry[fallbackTier] === modelKey) {
                        return { task: fallbackTask, tier: fallbackTier };
                    }
                }
            }

            return null;
        }

        function populateSavedPromptSelect($select) {
            if (!$select || !$select.length) {
                return;
            }

            var placeholder = strings.savedPromptPlaceholder || 'Select a saved prompt…';
            var emptyLabel = strings.savedPromptEmpty || 'No saved prompts found.';
            var userLabel = strings.savedPromptGroupUser || 'My Prompts';
            var globalLabel = strings.savedPromptGroupGlobal || 'Shared Prompts';
            var currentValue = $select.val();
            var allPrompts = getAllSavedPrompts();
            var html = '<option value="">' + escapeHtml(placeholder) + '</option>';

            if (!allPrompts.length) {
                html += '<option value="" disabled>' + escapeHtml(emptyLabel) + '</option>';
                $select.html(html).val('');
                return;
            }

            var grouped = { user: [], global: [] };
            allPrompts.forEach(function(entry) {
                if (!entry) {
                    return;
                }
                var target = entry.scope === 'global' ? 'global' : 'user';
                grouped[target].push(entry);
            });

            if (grouped.user.length) {
                html += '<optgroup label="' + escapeHtml(userLabel) + '">';
                grouped.user.forEach(function(entry) {
                    html += '<option value="' + escapeHtml(entry.id) + '">' + escapeHtml(entry.label) + '</option>';
                });
                html += '</optgroup>';
            }

            if (grouped.global.length) {
                html += '<optgroup label="' + escapeHtml(globalLabel) + '">';
                grouped.global.forEach(function(entry) {
                    html += '<option value="' + escapeHtml(entry.id) + '">' + escapeHtml(entry.label) + '</option>';
                });
                html += '</optgroup>';
            }

            $select.html(html);

            if (currentValue && findSavedPromptById(currentValue)) {
                $select.val(currentValue);
            } else {
                $select.val('');
            }
        }

        function refreshRegisteredSavedPromptSelects() {
            savedPromptSelectNodes = savedPromptSelectNodes.filter(function(node) {
                return node && node.parentNode;
            });

            savedPromptSelectNodes.forEach(function(node) {
                populateSavedPromptSelect($(node));
            });
        }

        function registerSavedPromptSelect($select) {
            if (!$select || !$select.length) {
                return;
            }

            $select.each(function() {
                if (savedPromptSelectNodes.indexOf(this) === -1) {
                    savedPromptSelectNodes.push(this);
                }
            });

            populateSavedPromptSelect($select);
        }

        function setSavedPrompts(data) {
            savedPromptsStore = normalizeSavedPrompts(data);
            refreshRegisteredSavedPromptSelects();
            $(document).trigger('aimentor:saved-prompts-refreshed', { prompts: cloneSavedPrompts() });
        }

        function normalizeKnowledgePack(entry) {
            if (!entry || typeof entry !== 'object') {
                return null;
            }

            var id = typeof entry.id === 'string' ? entry.id.trim() : '';
            var title = typeof entry.title === 'string' ? entry.title.trim() : '';

            if (!id || !title) {
                return null;
            }

            return {
                id: id,
                title: title,
                summary: typeof entry.summary === 'string' ? entry.summary.trim() : '',
                guidance: typeof entry.guidance === 'string' ? entry.guidance.trim() : ''
            };
        }

        function sortKnowledgePacks(list) {
            if (!Array.isArray(list)) {
                return [];
            }

            return list.slice().sort(function(a, b) {
                var titleA = a && typeof a.title === 'string' ? a.title : '';
                var titleB = b && typeof b.title === 'string' ? b.title : '';

                if (knowledgeCollator) {
                    return knowledgeCollator.compare(titleA, titleB);
                }

                return titleA.localeCompare(titleB);
            });
        }

        function normalizeKnowledgePacks(data) {
            if (!Array.isArray(data)) {
                return [];
            }

            return sortKnowledgePacks(data.map(normalizeKnowledgePack).filter(Boolean));
        }

        function cloneKnowledgePack(entry) {
            return {
                id: entry.id,
                title: entry.title,
                summary: entry.summary,
                guidance: entry.guidance
            };
        }

        function cloneKnowledgeStore() {
            return knowledgeStore.map(cloneKnowledgePack);
        }

        function setKnowledgeStore(data, options) {
            var settings = $.extend({ silent: false }, options);
            knowledgeStore = normalizeKnowledgePacks(data);
            refreshRegisteredKnowledgeSelects();
            window.aimentorAjax = window.aimentorAjax || {};
            window.aimentorAjax.knowledgePacks = cloneKnowledgeStore();
            if (!settings.silent) {
                $(document).trigger('aimentor:knowledge-packs-refreshed', { packs: cloneKnowledgeStore() });
            }
        }

        function findKnowledgePackById(id) {
            var target = typeof id === 'string' ? id : '';
            if (!target) {
                return null;
            }

            for (var index = 0; index < knowledgeStore.length; index++) {
                var pack = knowledgeStore[index];
                if (pack && pack.id === target) {
                    return pack;
                }
            }

            return null;
        }

        function populateKnowledgeSelect($select) {
            if (!$select || !$select.length) {
                return;
            }

            var currentValues = $select.val() || [];
            var options = '';

            if (!knowledgeStore.length) {
                options = '<option value="" disabled>' + escapeHtml(strings.knowledgePackSelectionEmpty || 'No knowledge packs available.') + '</option>';
                $select.prop('disabled', true);
            } else {
                knowledgeStore.forEach(function(pack) {
                    options += '<option value="' + escapeHtml(pack.id) + '">' + escapeHtml(pack.title) + '</option>';
                });
                $select.prop('disabled', false);
            }

            $select.html(options);
            if (knowledgeStore.length && currentValues && currentValues.length) {
                $select.val(currentValues);
            } else {
                $select.val([]);
            }
        }

        function refreshRegisteredKnowledgeSelects() {
            knowledgeSelectNodes = knowledgeSelectNodes.filter(function(node) {
                return node && node.parentNode;
            });

            knowledgeSelectNodes.forEach(function(node) {
                populateKnowledgeSelect($(node));
            });
        }

        function registerKnowledgeSelect($select) {
            if (!$select || !$select.length) {
                return;
            }

            $select.each(function() {
                if (knowledgeSelectNodes.indexOf(this) === -1) {
                    knowledgeSelectNodes.push(this);
                }
            });

            populateKnowledgeSelect($select);
        }

        function sanitizeKnowledgeSelection(ids) {
            var selection = Array.isArray(ids) ? ids : [];
            var unique = {};
            var result = [];

            selection.forEach(function(id) {
                if (!id) {
                    return;
                }
                var key = String(id);
                if (!unique[key]) {
                    unique[key] = true;
                    result.push(key);
                }
            });

            return result;
        }

        function updateKnowledgeSelectionSummary($target, ids) {
            if (!$target || !$target.length) {
                return;
            }

            var selected = sanitizeKnowledgeSelection(ids);

            if (!selected.length) {
                $target.text('');
                return;
            }

            var template = strings.knowledgePackSelectedCount || '%d knowledge packs selected';
            var message = template.indexOf('%d') !== -1 ? template.replace('%d', selected.length) : selected.length + ' knowledge packs selected';
            $target.text(message);
        }

        function refreshKnowledgeStore() {
            if (!knowledgeEndpoint || !aimentorData.restNonce || typeof window.fetch !== 'function') {
                return Promise.resolve(cloneKnowledgeStore());
            }

            return window.fetch(knowledgeEndpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-WP-Nonce': aimentorData.restNonce
                },
                credentials: 'same-origin'
            }).then(function(response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            }).then(function(body) {
                if (body && body.packs) {
                    setKnowledgeStore(body.packs);
                }
                return cloneKnowledgeStore();
            }).catch(function() {
                return cloneKnowledgeStore();
            });
        }

        function refreshSavedPrompts() {
            var endpoint = aimentorData.promptsEndpoint;

            if (!endpoint || !aimentorData.restNonce || typeof window.fetch !== 'function') {
                return Promise.resolve(cloneSavedPrompts());
            }

            return window.fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-WP-Nonce': aimentorData.restNonce
                },
                credentials: 'same-origin'
            }).then(function(response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            }).then(function(body) {
                if (body && body.prompts) {
                    setSavedPrompts(body.prompts);
                }
                return cloneSavedPrompts();
            }).catch(function() {
                return cloneSavedPrompts();
            });
        }

        var providerDefaults = buildProviderDefaults();
        var providerMap = buildProviderMap(providerDefaults);
        var existingProviders = (typeof window.AiMentorProviders === 'object' && window.AiMentorProviders) ? window.AiMentorProviders : {};

        if (!Object.keys(existingProviders).length && typeof window.JagGrokProviders === 'object' && window.JagGrokProviders) {
            existingProviders = Object.assign({}, window.JagGrokProviders);
        }

        window.AiMentorProviders = Object.assign({}, providerDefaults, providerMap, existingProviders);

        if (typeof window.JagGrokProviders !== 'undefined') {
            window.JagGrokProviders = window.AiMentorProviders;
        }

        var existingState = (window.AiMentorElementorUI && window.AiMentorElementorUI.state) ? window.AiMentorElementorUI.state : {};
        var documentDefaults = resolveDocumentDefaults(defaultsData);
        var contextPreset = deriveModelPreset(documentDefaults.provider, documentDefaults.model);
        var defaultProviderCandidate = (existingState.defaults && existingState.defaults.provider) || documentDefaults.provider || defaultsData.provider || aimentorData.provider;
        var defaultProvider = sanitizeProvider(defaultProviderCandidate);
        var defaultTaskCandidate = (existingState.defaults && existingState.defaults.task) || (contextPreset && contextPreset.task) || (defaultsData && defaultsData.task) || 'content';
        var defaultTierCandidate = (existingState.defaults && existingState.defaults.tier) || (contextPreset && contextPreset.tier) || (defaultsData && defaultsData.tier) || 'fast';
        var defaultTask = sanitizeTask(defaultTaskCandidate, isProActive);
        var defaultTier = sanitizeTier(defaultTierCandidate);

        var api = window.AiMentorElementorUI || {};
        api.state = {
            defaults: {
                task: defaultTask,
                tier: defaultTier,
                provider: defaultProvider,
                model: documentDefaults.model || '',
                contextKey: documentDefaults.key || 'default'
            },
            contextMap: (defaultsData && defaultsData.contexts && typeof defaultsData.contexts === 'object') ? defaultsData.contexts : {},
            documentDefaults: documentDefaults,
            widgets: existingState.widgets || {},
            modal: existingState.modal ? {
                task: sanitizeTask(existingState.modal.task, isProActive),
                tier: sanitizeTier(existingState.modal.tier),
                provider: sanitizeProvider(existingState.modal.provider || defaultProvider),
                tonePreset: sanitizeTonePreset(existingState.modal.tonePreset) || (defaultTonePresetId || ''),
                toneCustom: typeof existingState.modal.toneCustom === 'string' ? existingState.modal.toneCustom : '',
                knowledge: Array.isArray(existingState.modal.knowledge) ? existingState.modal.knowledge.map(String) : []
            } : {
                task: defaultTask,
                tier: defaultTier,
                provider: defaultProvider,
                tonePreset: sanitizeTonePreset(defaultTonePresetId) || '',
                toneCustom: '',
                knowledge: []
            }
        };

        api.buildSummary = buildSummary;
        api.getProviderMeta = getProviderMeta;
        api.getSavedPrompts = function() {
            return cloneSavedPrompts();
        };
        api.refreshSavedPrompts = refreshSavedPrompts;
        api.refreshKnowledgePacks = refreshKnowledgeStore;

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
            var $savedPromptSelect = config.savedPromptSelector ? $(config.savedPromptSelector) : $();
            var $presetSelect = config.presetSelector ? $(config.presetSelector) : $();
            var $presetDescription = config.presetDescriptionSelector ? $(config.presetDescriptionSelector) : $();
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

            if ($savedPromptSelect.length) {
                bindSavedPromptSelect($savedPromptSelect, $prompt);
            }

            if ($presetSelect.length) {
                var initialPresetValue = $presetSelect.val() || '';
                $presetSelect.data('aimentorLastPreset', initialPresetValue);
                applyPresetChoice($prompt, initialPresetValue, false, $presetDescription);
                $presetSelect.off('change.aimentor').on('change.aimentor', function() {
                    var selectedValue = $(this).val() || '';
                    var previousValue = $presetSelect.data('aimentorLastPreset') || '';
                    $presetSelect.data('aimentorLastPreset', selectedValue);
                    applyPresetChoice($prompt, selectedValue, !!selectedValue && selectedValue !== previousValue, $presetDescription);
                });
            } else if ($presetDescription.length) {
                $presetDescription.hide();
            }

            var widgetState = api.state.widgets[config.widgetId] || {};
            widgetState.provider = sanitizeProvider(widgetState.provider || $provider.val() || getDefaultProvider());
            widgetState.task = sanitizeTask(widgetState.task || ($task.length ? $task.val() : api.state.defaults.task), allowCanvas);
            widgetState.tier = sanitizeTier(widgetState.tier || ($tier.length ? $tier.val() : api.state.defaults.tier));
            var defaultModalKnowledge = (api.state.modal && Array.isArray(api.state.modal.knowledge)) ? api.state.modal.knowledge : [];
            widgetState.knowledge = sanitizeKnowledgeSelection(Array.isArray(widgetState.knowledge) ? widgetState.knowledge : defaultModalKnowledge);
            api.state.widgets[config.widgetId] = widgetState;

            $provider.val(widgetState.provider);
            if ($task.length) {
                $task.val(widgetState.task);
                if (!allowCanvas) {
                    $task.prop('disabled', true);
                }
            }
            if ($tier.length) {
                $tier.val(widgetState.tier);
            }

            applyProviderMeta(widgetState.provider, ui);
            updateSummaryText($summary, widgetState.provider, widgetState);

            $provider.off('change.aimentor').on('change.aimentor', function() {
                var providerKey = sanitizeProvider($(this).val());
                widgetState.provider = providerKey;
                applyProviderMeta(providerKey, ui);
                updateSummaryText($summary, providerKey, widgetState);
            });

            if ($task.length) {
                $task.off('change.aimentor').on('change.aimentor', function() {
                    widgetState.task = sanitizeTask($(this).val(), allowCanvas);
                    $(this).val(widgetState.task);
                    updateSummaryText($summary, widgetState.provider, widgetState);
                });
            }

            if ($tier.length) {
                $tier.off('change.aimentor').on('change.aimentor', function() {
                    widgetState.tier = sanitizeTier($(this).val());
                    $(this).val(widgetState.tier);
                    updateSummaryText($summary, widgetState.provider, widgetState);
                });
            }

            $button.off('click.aimentor').on('click.aimentor', function() {
                var promptValue = ($prompt.val() || '').trim();
                if (!promptValue) {
                    var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                    if ($output.length) {
                        $output.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                    }
                    return;
                }

                var providerKey = sanitizeProvider($provider.val() || widgetState.provider);
                widgetState.provider = providerKey;
                var providerMeta = applyProviderMeta(providerKey, ui);
                updateSummaryText($summary, providerKey, widgetState);

                var generatingMessage = getGeneratingMessage(providerMeta);
                if ($output.length) {
                    $output.html('<p>' + escapeHtml(generatingMessage) + '</p>');
                }

                $button.prop('disabled', true).text(generatingMessage);

                var requestPayload = {
                    action: 'aimentor_generate_page',
                    prompt: promptValue,
                    provider: providerKey,
                    nonce: aimentorData.nonce,
                    task: widgetState.task,
                    tier: widgetState.tier
                };

                if (widgetState.knowledge && widgetState.knowledge.length) {
                    requestPayload.knowledge_ids = widgetState.knowledge.slice();
                }

                if (widgetState.task === 'canvas') {
                    var variationCount = parseInt(aimentorData.canvasVariationCount, 10);
                    if (isFinite(variationCount) && variationCount > 0) {
                        requestPayload.variations = variationCount;
                    }
                }

                $.post(aimentorData.ajaxurl, requestPayload)
                    .done(function(response) {
                        $button.prop('disabled', false);
                        applyProviderMeta(providerKey, ui);

                        var responseProvider = response && response.data && response.data.provider ? sanitizeProvider(response.data.provider) : providerKey;
                        if (responseProvider !== widgetState.provider) {
                            widgetState.provider = responseProvider;
                            $provider.val(responseProvider);
                            applyProviderMeta(responseProvider, ui);
                        }

                        updateSummaryText($summary, widgetState.provider, widgetState);

                        if (response && response.success) {
                            var summaryText = extractResponseSummary(response, widgetState.provider, widgetState);
                            var canvasVariations = [];
                            if (response.data && Array.isArray(response.data.canvas_variations)) {
                                canvasVariations = response.data.canvas_variations;
                            }
                            var hasCanvasPayload = response.data && response.data.canvas_json && !canvasVariations.length;

                            var historyMeta = {
                                task: widgetState.task,
                                tier: widgetState.tier,
                                model: response && response.data && response.data.model ? response.data.model : '',
                                origin: 'ajax',
                                rate_limit: response && response.data ? response.data.rate_limit || null : null,
                                tokens: response && response.data && typeof response.data.tokens !== 'undefined' ? response.data.tokens : 0
                            };

                            if (canvasVariations.length && $output.length) {
                                renderCanvasVariations($output, canvasVariations, {
                                    provider: responseProvider,
                                    tier: widgetState.tier,
                                    summaryText: summaryText,
                                    responseData: response.data
                                });
                            }

                            if (hasCanvasPayload && window.elementorFrontend && elementorFrontend.elementsHandler) {
                                elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                            }

                            if (hasCanvasPayload) {
                                persistCanvasHistory(response.data, summaryText);
                            }

                            if (!canvasVariations.length && $output.length) {
                                $output.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + '</p>');
                            }

                            if (response.data && Array.isArray(response.data.warnings) && response.data.warnings.length && $output.length) {
                                var warningItems = response.data.warnings.map(function(message) {
                                    return '<li>' + escapeHtml(String(message)) + '</li>';
                                }).join('');
                                var warningTitle = escapeHtml(strings.analyticsWarningTitle || 'Guardrail warnings');
                                $output.append('<div class="aimentor-guardrail-warnings" role="status"><strong>' + warningTitle + '</strong><ul>' + warningItems + '</ul></div>');
                            }

                            if (!response.data || !response.data.history_recorded) {
                                recordHistoryEntry(promptValue, widgetState.provider, historyMeta);
                            }
                        } else {
                            var message = response && response.data ? response.data : 'Unknown error';
                            if (typeof message === 'object' && message !== null) {
                                message = message.message || message.error || 'Unknown error';
                            }
                            var errorPrefix = strings.errorPrefix || 'Error:';
                            if ($output.length) {
                                $output.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(String(message)) + '</p>');
                            }
                        }
                    })
                    .fail(function() {
                        $button.prop('disabled', false);
                        applyProviderMeta(widgetState.provider, ui);
                        var errorPrefix = strings.errorPrefix || 'Error:';
                        var errorMessage = strings.unknownError || 'Request failed.';
                        if ($output.length) {
                            $output.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(errorMessage) + '</p>');
                        }
                    });
            });
        };

        api.openModal = function() {
            var allowCanvas = isProActive;
            if (!$('#aimentor-modal').length) {
                var defaultProvider = sanitizeProvider(getDefaultProvider());
                var providerOptions = buildProviderOptions(defaultProvider);
                var generationLabel = escapeHtml(strings.generationType || 'Generation Type');
                var performanceLabel = escapeHtml(strings.performanceLabel || 'Performance');
                var pageCopyLabel = escapeHtml(strings.pageCopy || 'Page Copy');
                var pageLayoutLabel = escapeHtml(strings.pageLayout || 'Page Layout');
                var fastLabel = escapeHtml(strings.fastLabel || 'Fast');
                var qualityLabel = escapeHtml(strings.qualityLabel || 'Quality');
                var promptLabel = escapeHtml(strings.promptLabel || 'Prompt');
                var promptPlaceholder = escapeHtml(strings.promptPlaceholder || 'Describe your page (e.g., hero with CTA)');
                var savedPromptLabel = escapeHtml(strings.savedPromptLabel || 'Saved Prompts');
                var savedPromptPlaceholder = escapeHtml(strings.savedPromptPlaceholder || 'Select a saved prompt…');
                var knowledgeLabel = escapeHtml(strings.knowledgePackLabel || 'Knowledge Packs');
                var knowledgeDescription = escapeHtml(strings.knowledgePackDescription || 'Optional: ground the response with knowledge packs saved in AiMentor settings.');
                var knowledgeEmpty = escapeHtml(strings.knowledgePackSelectionEmpty || 'No knowledge packs available yet.');
                var toneLabel = escapeHtml(strings.tonePresetLabel || 'Tone');
                var tonePlaceholder = escapeHtml(strings.tonePresetPlaceholder || 'Select a tone preset…');
                var toneCustomOption = escapeHtml(strings.tonePresetCustomOption || 'Custom tone…');
                var toneCustomLabel = escapeHtml(strings.tonePresetCustomLabel || 'Custom tone keywords');
                var toneCustomPlaceholder = escapeHtml(strings.tonePresetCustomPlaceholder || 'e.g., bold, welcoming, energetic');
                var headingMeta = getProviderMeta(defaultProvider);
                var headingText = escapeHtml(strings.writeWith ? strings.writeWith.replace('%s', headingMeta.label || defaultProvider) : 'Write with ' + (headingMeta.label || defaultProvider));
                var closeLabel = escapeHtml(strings.closeModal || 'Close modal');
                var askLabel = escapeHtml(strings.askAiMentor || 'Ask AiMentor');
                var rewriteLabelRaw = strings.rewriteButtonLabel || 'Rewrite with Tone';
                var rewriteLabel = escapeHtml(rewriteLabelRaw);
                var historyHeading = escapeHtml(strings.recentLayoutsHeading || 'Recent layouts');
                var historyNavLabel = escapeHtml(strings.recentLayoutsBrowse || 'Browse recent layouts');
                var historyPrevLabel = escapeHtml(strings.recentLayoutsPrev || 'Show previous layout');
                var historyNextLabel = escapeHtml(strings.recentLayoutsNext || 'Show next layout');
                var historyEmpty = escapeHtml(strings.recentLayoutsEmpty || 'Generate a layout to see it here after your next run.');
                var frameHeading = escapeHtml(strings.frameLibraryHeading || 'Frame Library');
                var frameDescription = escapeHtml(strings.frameLibraryDescription || 'Insert curated layouts or pull their prompts into the generator to start faster.');
                var frameEmpty = escapeHtml(strings.frameLibraryEmpty || 'No frames have been curated yet. Promote frames from AiMentor settings.');
                var frameLoading = escapeHtml(strings.frameLibraryLoading || 'Loading curated frames…');
                var frameError = escapeHtml(strings.frameLibraryError || 'Unable to load frames. Refresh the panel or try again later.');
                var asideLabel = escapeHtml(strings.modalAsideLabel || 'Explore saved layouts and frames');
                var historyPanelId = 'aimentor-panel-history';
                var libraryPanelId = 'aimentor-panel-library';

                ensureFrameLibraryStyles();

                var toneOptions = '<option value="">' + tonePlaceholder + '</option>';
                tonePresets.forEach(function(preset) {
                    if (!preset || typeof preset.id === 'undefined') {
                        return;
                    }
                    var id = String(preset.id);
                    var meta = tonePresetLookup[id] || {};
                    toneOptions += '<option value="' + escapeHtml(id) + '">' + escapeHtml(meta.label || id) + '</option>';
                });
                toneOptions += '<option value="custom">' + toneCustomOption + '</option>';

                var modalHtml = '' +
                    '<div id="aimentor-modal" class="aimentor-modal" role="dialog" aria-modal="true" aria-labelledby="aimentor-modal-heading-text" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;">' +
                    '  <div class="aimentor-modal__content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:90%;max-width:900px;background:white;border-radius:8px;box-shadow:0 5px 18px rgba(0,0,0,0.25);overflow:hidden;">' +
                    '    <div class="aimentor-modal__header" style="padding:20px;border-bottom:1px solid #e2e4e7;display:flex;align-items:center;justify-content:space-between;gap:12px;">' +
                    '      <h3 id="aimentor-modal-heading-text" class="aimentor-modal__title" style="margin:0;display:flex;align-items:center;gap:8px;"><span class="dashicons dashicons-art" aria-hidden="true"></span><span>' + headingText + '</span></h3>' +
                    '      <button type="button" id="aimentor-modal-close" class="aimentor-modal__close" aria-label="' + closeLabel + '" style="background:none;border:none;font-size:24px;line-height:1;color:#6b7280;cursor:pointer;">&times;</button>' +
                    '    </div>' +
                    '    <div class="aimentor-modal__body" style="padding:20px;display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start;max-height:80vh;overflow-y:auto;">' +
                    '      <div class="aimentor-modal__form" style="flex:1;min-width:320px;display:flex;flex-direction:column;gap:16px;">' +
                    '        <div class="aimentor-modal__providers" role="radiogroup" aria-label="' + escapeHtml(strings.chooseProvider || 'Choose provider') + '">' + providerOptions + '</div>' +
                    '        <div class="aimentor-provider-active" style="display:flex;align-items:center;gap:8px;">' +
                    '          <span id="aimentor-provider-active-icon" aria-hidden="true"></span>' +
                    '          <strong id="aimentor-provider-active-label"></strong>' +
                    '          <span id="aimentor-provider-active-badge" class="aimentor-provider-badge"></span>' +
                    '        </div>' +
                    '        <p id="aimentor-provider-summary" class="aimentor-provider-description" style="margin:0;font-size:13px;color:#4b5563;"></p>' +
                    '        <label for="aimentor-modal-task" class="aimentor-modal__label">' + generationLabel + '</label>' +
                    '        <select id="aimentor-modal-task" class="aimentor-modal__select"' + (allowCanvas ? '' : ' disabled') + '>' +
                    '          <option value="content">' + pageCopyLabel + '</option>' +
                    '          <option value="canvas"' + (allowCanvas ? '' : ' disabled') + '>' + pageLayoutLabel + '</option>' +
                    '        </select>' +
                    '        <label for="aimentor-modal-tier" class="aimentor-modal__label">' + performanceLabel + '</label>' +
                    '        <select id="aimentor-modal-tier" class="aimentor-modal__select">' +
                    '          <option value="fast">' + fastLabel + '</option>' +
                    '          <option value="quality">' + qualityLabel + '</option>' +
                    '        </select>' +
                    '        <p id="aimentor-modal-summary" class="aimentor-context-summary" aria-live="polite" style="margin:0;font-weight:600;color:#111827;"></p>' +
                    '        <label for="aimentor-saved-prompts" class="aimentor-modal__label">' + savedPromptLabel + '</label>' +
                    '        <select id="aimentor-saved-prompts" class="aimentor-modal__select">' +
                    '          <option value="">' + savedPromptPlaceholder + '</option>' +
                    '        </select>' +
                    '        <label for="aimentor-knowledge-packs" class="aimentor-modal__label">' + knowledgeLabel + '</label>' +
                    '        <select id="aimentor-knowledge-packs" class="aimentor-modal__select aimentor-modal__select--multiline" multiple size="4"></select>' +
                    '        <p class="aimentor-modal__hint" id="aimentor-knowledge-description" style="margin:6px 0 0;font-size:12px;color:#4b5563;">' + knowledgeDescription + '</p>' +
                    '        <p class="aimentor-modal__hint" id="aimentor-knowledge-summary" style="margin:4px 0 12px;font-size:12px;color:#2563eb;"></p>' +
                    '        <label for="aimentor-prompt" class="aimentor-modal__label">' + promptLabel + '</label>' +
                    '        <textarea id="aimentor-prompt" rows="4" placeholder="' + promptPlaceholder + '" style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:6px;font-family:inherit;"></textarea>' +
                    '        <label for="aimentor-tone-preset" class="aimentor-modal__label">' + toneLabel + '</label>' +
                    '        <select id="aimentor-tone-preset" class="aimentor-modal__select">' + toneOptions + '</select>' +
                    '        <label for="aimentor-tone-custom" id="aimentor-tone-custom-label" class="aimentor-modal__label" style="display:none;">' + toneCustomLabel + '</label>' +
                    '        <input type="text" id="aimentor-tone-custom" class="aimentor-modal__input" style="display:none;padding:10px;border:1px solid #d1d5db;border-radius:6px;font-family:inherit;" placeholder="' + toneCustomPlaceholder + '">' +
                    '        <div class="aimentor-modal__actions" style="display:flex;gap:8px;flex-wrap:wrap;">' +
                    '          <button type="button" id="aimentor-rewrite" class="button button-secondary" style="flex:1 1 160px;font-weight:600;">' + rewriteLabel + '</button>' +
                    '          <button type="button" id="aimentor-generate" class="button button-primary" style="flex:1 1 160px;padding:12px;font-size:16px;font-weight:600;">' + askLabel + '</button>' +
                    '        </div>' +
                    '        <p id="aimentor-cooldown-notice" class="aimentor-cooldown-notice" aria-live="polite" style="display:none;margin:8px 0 0;font-size:12px;color:#b45309;"></p>' +
                    '        <div id="aimentor-result" style="min-height:38px;padding:12px;background:#f3f4f6;border-radius:6px;color:#111827;"></div>' +
                    '      </div>' +
                    '      <aside class="aimentor-modal__aside" style="flex:0 0 280px;display:flex;flex-direction:column;gap:12px;">' +
                    '        <div class="aimentor-modal__aside-tabs" role="tablist" aria-label="' + asideLabel + '">' +
                    '          <button type="button" class="aimentor-modal__aside-tab is-active" id="aimentor-tab-history" role="tab" aria-selected="true" aria-controls="' + historyPanelId + '" data-panel="history">' + historyHeading + '</button>' +
                    '          <button type="button" class="aimentor-modal__aside-tab" id="aimentor-tab-library" role="tab" aria-selected="false" aria-controls="' + libraryPanelId + '" data-panel="library">' + frameHeading + '</button>' +
                    '        </div>' +
                    '        <div class="aimentor-modal__aside-panels" style="display:flex;flex-direction:column;gap:12px;">' +
                    '          <section class="aimentor-modal__panel aimentor-layout-history is-active" id="' + historyPanelId + '" role="tabpanel" aria-labelledby="aimentor-tab-history" data-panel="history" aria-live="polite" data-empty-text="' + historyEmpty + '">' +
                    '            <div class="aimentor-layout-history__header">' +
                    '              <strong class="aimentor-layout-history__title">' + historyHeading + '</strong>' +
                    '              <div class="aimentor-layout-history__nav" role="group" aria-label="' + historyNavLabel + '">' +
                    '                <button type="button" class="aimentor-layout-history__nav-button aimentor-layout-history__nav-button--prev" aria-label="' + historyPrevLabel + '" disabled>&lsaquo;</button>' +
                    '                <button type="button" class="aimentor-layout-history__nav-button aimentor-layout-history__nav-button--next" aria-label="' + historyNextLabel + '" disabled>&rsaquo;</button>' +
                    '              </div>' +
                    '            </div>' +
                    '            <div class="aimentor-layout-history__viewport" role="listbox" aria-label="' + historyHeading + '"></div>' +
                    '            <p class="aimentor-layout-history__empty">' + historyEmpty + '</p>' +
                    '          </section>' +
                    '          <section class="aimentor-modal__panel aimentor-frame-library" id="' + libraryPanelId + '" role="tabpanel" aria-labelledby="aimentor-tab-library" data-panel="library" aria-live="polite" aria-hidden="true" data-empty-text="' + frameEmpty + '">' +
                    '            <div class="aimentor-frame-library__header">' +
                    '              <strong class="aimentor-frame-library__title">' + frameHeading + '</strong>' +
                    '              <p class="aimentor-frame-library__updated" style="display:none;"></p>' +
                    '            </div>' +
                    '            <p class="aimentor-frame-library__description">' + frameDescription + '</p>' +
                    '            <div class="aimentor-frame-library__loading" style="display:none;">' + frameLoading + '</div>' +
                    '            <div class="aimentor-frame-library__error" style="display:none;">' + frameError + '</div>' +
                    '            <div class="aimentor-frame-library__list" role="list"></div>' +
                    '            <p class="aimentor-frame-library__empty">' + frameEmpty + '</p>' +
                    '          </section>' +
                    '        </div>' +
                    '      </aside>' +
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
            var $cooldownNotice = $('#aimentor-cooldown-notice');
            var $savedPromptSelect = $('#aimentor-saved-prompts');
            var $knowledgeSelect = $('#aimentor-knowledge-packs');
            var $knowledgeSummary = $('#aimentor-knowledge-summary');
            var $tonePreset = $('#aimentor-tone-preset');
            var $toneCustom = $('#aimentor-tone-custom');
            var $toneCustomLabel = $('#aimentor-tone-custom-label');
            var $rewrite = $('#aimentor-rewrite');
            var ui = {
                icon: $('#aimentor-provider-active-icon'),
                label: $('#aimentor-provider-active-label'),
                summary: $('#aimentor-provider-summary'),
                badge: $('#aimentor-provider-active-badge'),
                button: $generate,
                heading: $('#aimentor-modal-heading-text span').last()
            };

            attachHistoryCarousel($modal);
            attachFrameLibrary($modal);
            attachPlaceholderTriggers($modal);

            $modal.show();
            $close.off('click.aimentor').on('click.aimentor', function() {
                $modal.hide();
            });

            var modalState = api.state.modal;
            modalState.provider = sanitizeProvider(modalState.provider || getDefaultProvider());
            modalState.task = sanitizeTask(modalState.task, allowCanvas);
            modalState.tier = sanitizeTier(modalState.tier);
            modalState.knowledge = sanitizeKnowledgeSelection(modalState.knowledge || []);

            function updateToneCustomVisibility(selectedPreset) {
                var isCustom = selectedPreset === 'custom';
                if ($toneCustomLabel.length) {
                    $toneCustomLabel.toggle(isCustom);
                }
                if ($toneCustom.length) {
                    $toneCustom.toggle(isCustom);
                }
            }

            function getToneKeywordsForPreset(presetId) {
                if (!presetId) {
                    return '';
                }
                if (presetId === 'custom') {
                    return ($toneCustom.val() || '').trim();
                }
                var presetMeta = tonePresetLookup[presetId];
                return presetMeta && typeof presetMeta.keywords === 'string' ? presetMeta.keywords : '';
            }

            function resolveToneSelection() {
                var selected = sanitizeTonePreset($tonePreset.val());
                if (!selected && $tonePreset.val() === 'custom') {
                    selected = 'custom';
                }
                if (!selected && modalState.tonePreset) {
                    selected = sanitizeTonePreset(modalState.tonePreset);
                }
                if (!selected && defaultTonePresetId) {
                    selected = sanitizeTonePreset(defaultTonePresetId);
                }

                var keywords = '';
                if (selected === 'custom') {
                    keywords = ($toneCustom.val() || '').trim();
                } else if (selected) {
                    keywords = getToneKeywordsForPreset(selected);
                }

                if (!keywords && selected === 'custom' && modalState.toneCustom) {
                    keywords = modalState.toneCustom.trim();
                }

                if (!keywords && defaultToneKeywords) {
                    keywords = defaultToneKeywords;
                }

                return {
                    id: selected,
                    keywords: keywords
                };
            }

            function captureRewriteTarget() {
                var target = null;

                if (lastFocusedControlElement && document.body.contains(lastFocusedControlElement)) {
                    var element = lastFocusedControlElement;
                    var value = typeof element.value === 'string' ? element.value : '';
                    var storedSelection = jQuery(element).data('aimentorSelection') || {};
                    var start = typeof storedSelection.start === 'number' ? storedSelection.start : (typeof element.selectionStart === 'number' ? element.selectionStart : 0);
                    var end = typeof storedSelection.end === 'number' ? storedSelection.end : (typeof element.selectionEnd === 'number' ? element.selectionEnd : value.length);

                    if (!value) {
                        start = 0;
                        end = 0;
                    }

                    if (typeof start !== 'number' || isNaN(start) || start < 0) {
                        start = 0;
                    }

                    if (typeof end !== 'number' || isNaN(end) || end > value.length) {
                        end = value.length;
                    }

                    if (end < start) {
                        end = start;
                    }

                    var selectionText = value.slice(start, end);
                    var normalized = selectionText && selectionText.trim() ? selectionText.trim() : value.trim();

                    if (normalized) {
                        target = {
                            type: 'control',
                            element: element,
                            start: selectionText && selectionText.trim() ? start : 0,
                            end: selectionText && selectionText.trim() ? end : value.length,
                            originalValue: value,
                            text: normalized
                        };
                    }
                }

                if (!target) {
                    var promptValue = ($prompt.val() || '').trim();
                    if (promptValue) {
                        target = {
                            type: 'prompt',
                            text: promptValue
                        };
                    }
                }

                if (target && target.text && target.type === 'control' && (!$prompt.val() || !$prompt.val().trim())) {
                    $prompt.val(target.text);
                }

                modalState.rewriteTarget = target;
            }

            function resolveRewriteSource() {
                var target = modalState.rewriteTarget || null;
                if (target && target.text) {
                    return { text: target.text, target: target };
                }

                var promptValue = ($prompt.val() || '').trim();
                if (promptValue) {
                    return { text: promptValue, target: { type: 'prompt' } };
                }

                return { text: '', target: target || { type: 'prompt' } };
            }

            function applyRewriteResult(target, rewrittenText) {
                var applied = 'prompt';
                if (target && target.type === 'control' && target.element && document.body.contains(target.element)) {
                    var element = target.element;
                    var value = typeof element.value === 'string' ? element.value : '';
                    var start = typeof target.start === 'number' ? target.start : 0;
                    var end = typeof target.end === 'number' ? target.end : value.length;

                    if (start < 0 || start > value.length) {
                        start = 0;
                    }

                    if (end < start || end > value.length) {
                        end = value.length;
                    }

                    var updated = value.slice(0, start) + rewrittenText + value.slice(end);
                    element.value = updated;

                    try {
                        if (typeof element.setSelectionRange === 'function') {
                            element.setSelectionRange(start, start + rewrittenText.length);
                        }
                    } catch (error) {
                        // Ignore selection errors for non-text inputs.
                    }

                    jQuery(element).trigger('input').trigger('change');
                    modalState.rewriteTarget = {
                        type: 'control',
                        element: element,
                        start: start,
                        end: start + rewrittenText.length,
                        originalValue: updated,
                        text: rewrittenText
                    };
                    lastFocusedControlElement = element;
                    recordControlSelection(element);
                    applied = 'control';
                } else {
                    $prompt.val(rewrittenText);
                    modalState.rewriteTarget = {
                        type: 'prompt',
                        text: rewrittenText
                    };
                }

                return applied;
            }

            modalState.tonePreset = sanitizeTonePreset(modalState.tonePreset) || (defaultTonePresetId ? sanitizeTonePreset(defaultTonePresetId) : '');
            modalState.toneCustom = typeof modalState.toneCustom === 'string' ? modalState.toneCustom : '';

            if ($tonePreset.length) {
                $tonePreset.val(modalState.tonePreset || '');
            }

            if ($toneCustom.length) {
                $toneCustom.val(modalState.toneCustom);
            }

            updateToneCustomVisibility(modalState.tonePreset);

            $providerRadios.prop('checked', false);
            $providerRadios.filter('[value="' + modalState.provider + '"]').prop('checked', true);
            if (!$providerRadios.filter(':checked').length && $providerRadios.length) {
                modalState.provider = sanitizeProvider($providerRadios.first().val());
                $providerRadios.first().prop('checked', true);
            }

            if ($task.length) {
                $task.val(modalState.task);
                if (!allowCanvas) {
                    $task.prop('disabled', true);
                }
            }
            if ($tier.length) {
                $tier.val(modalState.tier);
            }

            applyProviderMeta(modalState.provider, ui);
            updateSummaryText($summary, modalState.provider, modalState);

            if (!ensureAjaxConfig($modal, $generate)) {
                return;
            }

            bindSavedPromptSelect($savedPromptSelect, $prompt);
            $savedPromptSelect.val('');
            refreshSavedPrompts().then(function() {
                populateSavedPromptSelect($savedPromptSelect);
            });

            registerKnowledgeSelect($knowledgeSelect);

            function syncKnowledgeSelectionDisplay(selection) {
                if ($knowledgeSelect.length) {
                    $knowledgeSelect.val(selection);
                    if (!knowledgeStore.length) {
                        $knowledgeSelect.prop('disabled', true);
                    }
                }

                if ($knowledgeSummary.length) {
                    if (!knowledgeStore.length) {
                        $knowledgeSummary.text(knowledgeEmpty);
                    } else {
                        updateKnowledgeSelectionSummary($knowledgeSummary, selection);
                    }
                }
            }

            syncKnowledgeSelectionDisplay(modalState.knowledge);

            refreshKnowledgeStore().then(function() {
                registerKnowledgeSelect($knowledgeSelect);
                syncKnowledgeSelectionDisplay(modalState.knowledge);
            });

            $knowledgeSelect.off('change.aimentorKnowledge').on('change.aimentorKnowledge', function() {
                var selected = sanitizeKnowledgeSelection($(this).val());
                modalState.knowledge = selected;
                updateKnowledgeSelectionSummary($knowledgeSummary, selected);
                updateSummaryText($summary, modalState.provider, modalState);
                if (api.state && api.state.widgets) {
                    Object.keys(api.state.widgets).forEach(function(key) {
                        var widgetEntry = api.state.widgets[key];
                        if (widgetEntry && typeof widgetEntry === 'object') {
                            widgetEntry.knowledge = selected.slice();
                        }
                    });
                }
            });

            $providerRadios.off('change.aimentor').on('change.aimentor', function() {
                modalState.provider = sanitizeProvider($(this).val());
                applyProviderMeta(modalState.provider, ui);
                updateSummaryText($summary, modalState.provider, modalState);
            });

            $task.off('change.aimentor').on('change.aimentor', function() {
                modalState.task = sanitizeTask($(this).val(), allowCanvas);
                $(this).val(modalState.task);
                updateSummaryText($summary, modalState.provider, modalState);
            });

            $tier.off('change.aimentor').on('change.aimentor', function() {
                modalState.tier = sanitizeTier($(this).val());
                $(this).val(modalState.tier);
                updateSummaryText($summary, modalState.provider, modalState);
            });

            if ($tonePreset.length) {
                $tonePreset.off('change.aimentorTone').on('change.aimentorTone', function() {
                    var value = sanitizeTonePreset($(this).val());
                    if (!value && $(this).val() === 'custom') {
                        value = 'custom';
                    }
                    if (!value && defaultTonePresetId) {
                        value = sanitizeTonePreset(defaultTonePresetId);
                    }
                    modalState.tonePreset = value;
                    if (value !== 'custom') {
                        modalState.toneCustom = '';
                        if ($toneCustom.length) {
                            $toneCustom.val('');
                        }
                    }
                    updateToneCustomVisibility(value);
                });
            }

            if ($toneCustom.length) {
                $toneCustom.off('input.aimentorTone').on('input.aimentorTone', function() {
                    modalState.toneCustom = $(this).val();
                });
            }

            captureRewriteTarget();
            if (!modalState.rewriteTarget || modalState.rewriteTarget.type !== 'prompt') {
                $prompt.val('');
            }
            $prompt.focus();
            $result.empty();
            updateCooldownNotice($cooldownNotice, null);

            if ($rewrite.length) {
                $rewrite.off('click.aimentorRewrite').on('click.aimentorRewrite', function() {
                    var rewriteSource = resolveRewriteSource();

                    if (!rewriteSource.text) {
                        var missingMessage = strings.rewriteMissingSource || 'Highlight text in Elementor or enter a prompt to rewrite.';
                        $result.html('<p style="color:#b91c1c;">' + escapeHtml(missingMessage) + '</p>');
                        return;
                    }

                    var toneSelection = resolveToneSelection();
                    modalState.tonePreset = toneSelection.id;
                    if (toneSelection.id === 'custom') {
                        modalState.toneCustom = ($toneCustom.val() || '').trim();
                    } else {
                        modalState.toneCustom = '';
                    }

                    var toneKeywords = toneSelection.keywords || '';
                    if (!toneKeywords && defaultToneKeywords) {
                        toneKeywords = defaultToneKeywords;
                    }

                    var payload = {
                        action: 'aimentor_rewrite_content',
                        nonce: aimentorData.rewriteNonce || aimentorData.nonce,
                        provider: modalState.provider,
                        tier: modalState.tier,
                        content: rewriteSource.text,
                        tone_keywords: toneKeywords
                    };

                    if (modalState.knowledge && modalState.knowledge.length) {
                        payload.knowledge_ids = modalState.knowledge.slice();
                    }

                    var workingMessage = strings.rewriteWorking || 'Rewriting…';
                    $rewrite.prop('disabled', true).text(workingMessage);
                    $result.html('<p>' + escapeHtml(workingMessage) + '</p>');

                    $.post(aimentorData.ajaxurl, payload).done(function(response) {
                        $rewrite.prop('disabled', false).text(rewriteLabelRaw);

                        if (response && response.success && response.data && response.data.rewritten) {
                            var appliedTo = applyRewriteResult(rewriteSource.target, response.data.rewritten);
                            var successMessage = strings.rewriteSuccess || 'Copy rewritten to match the selected tone.';

                            if (appliedTo === 'control' && strings.rewriteAppliedToControl) {
                                successMessage = strings.rewriteAppliedToControl;
                            } else if (appliedTo === 'prompt' && strings.rewriteAppliedToPrompt) {
                                successMessage = strings.rewriteAppliedToPrompt;
                            }

                            $result.html('<p style="color:green">' + escapeHtml(successMessage) + '</p>');
                        } else {
                            var message = strings.rewriteError || 'Unable to rewrite the selection. Try again.';

                            if (response && response.data) {
                                var detail = response.data.message || response.data.error || '';
                                if (detail) {
                                    message = message + ' ' + detail;
                                }
                            }

                            $result.html('<p style="color:#b91c1c;">' + escapeHtml(String(message)) + '</p>');
                        }
                    }).fail(function() {
                        $rewrite.prop('disabled', false).text(rewriteLabelRaw);
                        var message = strings.rewriteError || 'Unable to rewrite the selection. Try again.';
                        $result.html('<p style="color:#b91c1c;">' + escapeHtml(message) + '</p>');
                    });
                });
            }

            $generate.off('click.aimentor').on('click.aimentor', function() {
                var promptValue = ($prompt.val() || '').trim();
                if (!promptValue) {
                    var promptMessage = strings.promptRequired || 'Please enter a prompt!';
                    $result.html('<p style="color:red">' + escapeHtml(promptMessage) + '</p>');
                    return;
                }

                var selectionKnowledge = sanitizeKnowledgeSelection(modalState.knowledge);
                modalState.knowledge = selectionKnowledge;
                var selection = {
                    task: sanitizeTask($task.val(), allowCanvas),
                    tier: sanitizeTier($tier.val()),
                    knowledge: selectionKnowledge.slice()
                };
                modalState.task = selection.task;
                modalState.tier = selection.tier;

                var providerValue = modalState.provider;
                var providerMeta = applyProviderMeta(providerValue, ui);
                selection.knowledge = modalState.knowledge.slice();
                updateSummaryText($summary, providerValue, selection);

                var generatingMessage = getGeneratingMessage(providerMeta);
                $generate.prop('disabled', true).text(generatingMessage);
                $result.html('<p>' + escapeHtml(generatingMessage) + '</p><p>' + escapeHtml(buildSummary(providerValue, selection.task, selection.tier)) + '</p>');
                updateCooldownNotice($cooldownNotice, null);

                var modalRequestPayload = {
                    action: 'aimentor_generate_page',
                    prompt: promptValue,
                    provider: providerValue,
                    task: selection.task,
                    tier: selection.tier,
                    nonce: aimentorData.nonce
                };

                if (modalState.knowledge && modalState.knowledge.length) {
                    modalRequestPayload.knowledge_ids = modalState.knowledge.slice();
                }

                if (selection.task === 'canvas') {
                    var modalVariationCount = parseInt(aimentorData.canvasVariationCount, 10);
                    if (isFinite(modalVariationCount) && modalVariationCount > 0) {
                        modalRequestPayload.variations = modalVariationCount;
                    }
                }

                $.post(aimentorData.ajaxurl, modalRequestPayload).done(function(response) {
                    $generate.prop('disabled', false);
                    applyProviderMeta(providerValue, ui);

                    var rateLimitPayload = null;
                    if (response && response.data && typeof response.data === 'object' && response.data !== null) {
                        rateLimitPayload = response.data.rate_limit || null;
                    }

                    updateCooldownNotice($cooldownNotice, rateLimitPayload);

                    if (response && response.data && response.data.provider) {
                        providerValue = sanitizeProvider(response.data.provider);
                        modalState.provider = providerValue;
                        $providerRadios.prop('checked', false);
                        $providerRadios.filter('[value="' + providerValue + '"]').prop('checked', true);
                        applyProviderMeta(providerValue, ui);
                    }

                    selection.knowledge = modalState.knowledge.slice();
                    updateSummaryText($summary, providerValue, selection);

                    if (response && response.success) {
                        var summaryText = extractResponseSummary(response, providerValue, selection);
                        var canvasVariations = [];
                        if (response.data && Array.isArray(response.data.canvas_variations)) {
                            canvasVariations = response.data.canvas_variations;
                        }
                        var hasCanvasPayload = response.data && response.data.canvas_json && !canvasVariations.length;

                        var historyMeta = {
                            task: selection.task,
                            tier: selection.tier,
                            model: response && response.data && response.data.model ? response.data.model : '',
                            origin: 'ajax',
                            rate_limit: response && response.data ? response.data.rate_limit || null : null,
                            tokens: response && response.data && typeof response.data.tokens !== 'undefined' ? response.data.tokens : 0
                        };

                        if (canvasVariations.length) {
                            renderCanvasVariations($result, canvasVariations, {
                                provider: providerValue,
                                tier: selection.tier,
                                summaryText: summaryText,
                                responseData: response.data
                            });
                        }

                        if (hasCanvasPayload && window.elementorFrontend && elementorFrontend.elementsHandler) {
                            elementorFrontend.elementsHandler.addElements(response.data.canvas_json);
                        }

                        if (hasCanvasPayload) {
                            persistCanvasHistory(response.data, summaryText);
                        }

                        if (!canvasVariations.length) {
                            if (response.data && response.data.html) {
                                var snippet = response.data.html.substring(0, 160);
                                var snippetHtml = snippet ? '<br><small>' + escapeHtml(snippet + (response.data.html.length > 160 ? '…' : '')) + '</small>' : '';
                                $result.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + snippetHtml + '</p>');
                            } else {
                                $result.html('<p style="color:green">' + escapeHtml(strings.successPrefix || '✅') + ' ' + escapeHtml(summaryText) + '</p>');
                            }
                        }

                        if (response.data && Array.isArray(response.data.warnings) && response.data.warnings.length) {
                            var warningItems = response.data.warnings.map(function(message) {
                                return '<li>' + escapeHtml(String(message)) + '</li>';
                            }).join('');
                            var warningTitle = escapeHtml(strings.analyticsWarningTitle || 'Guardrail warnings');
                            $result.append('<div class="aimentor-guardrail-warnings" role="status"><strong>' + warningTitle + '</strong><ul>' + warningItems + '</ul></div>');
                        }

                        if (!response.data || !response.data.history_recorded) {
                            recordHistoryEntry(promptValue, providerValue, historyMeta);
                        }
                    } else {
                        var message = response && response.data ? response.data : 'Unknown error';
                        if (typeof message === 'object' && message !== null) {
                            message = message.message || message.error || 'Unknown error';
                        }
                        var errorPrefix = strings.errorPrefix || 'Error:';
                        $result.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(String(message)) + '</p>');
                    }
                }).fail(function() {
                    $generate.prop('disabled', false);
                    applyProviderMeta(modalState.provider, ui);
                    var errorPrefix = strings.errorPrefix || 'Error:';
                    var errorMessage = strings.unknownError || 'Request failed.';
                    $result.html('<p style="color:red">' + escapeHtml(errorPrefix) + ' ' + escapeHtml(errorMessage) + '</p>');
                    updateCooldownNotice($cooldownNotice, null);
                });
            });
        };

        $(document).on('aimentor:saved-prompts-updated', function(event, payload) {
            if (payload && payload.prompts) {
                setSavedPrompts(payload.prompts);
                return;
            }

            refreshSavedPrompts();
        });

        window.AiMentorElementorUI = api;

        if (window.elementor && window.elementor.hooks && typeof window.elementor.hooks.addAction === 'function') {
            var widgetSlugs = ['aimentor-ai-generator', 'jaggrok-ai-generator'];

            [
                'panel/widgets/aimentor-ai-generator/controls/write_with_aimentor/event',
                'panel/widgets/aimentor-ai-generator/controls/write_with_jaggrok/event',
                'panel/widgets/jaggrok-ai-generator/controls/write_with_aimentor/event',
                'panel/widgets/jaggrok-ai-generator/controls/write_with_jaggrok/event'
            ].forEach(function(hookName) {
                window.elementor.hooks.addAction(hookName, api.openModal);
            });

            widgetSlugs.forEach(function(slug) {
                window.elementor.hooks.addAction('panel/open_editor/widget/' + slug, function(panel, model, view) {
                    if (!view || !view.$el) {
                        return;
                    }
                    setTimeout(function() {
                        setupPresetControlBindings(view);
                    }, 0);
                });
            });
        }

        $(document).off('click.aimentorOpenModal').on('click.aimentorOpenModal', '.aimentor-modal-trigger', function(event) {
            event.preventDefault();
            if (typeof api.openModal === 'function') {
                api.openModal();
            }
        });

        function buildPromptPresetData(catalog) {
            var data = {
                presets: {},
                categories: {},
                categoryPresets: {}
            };
            if (!catalog || typeof catalog !== 'object') {
                return data;
            }
            Object.keys(catalog).forEach(function(providerKey) {
                var providerEntry = catalog[providerKey];
                if (!providerEntry || typeof providerEntry !== 'object') {
                    return;
                }
                Object.keys(providerEntry).forEach(function(categoryKey) {
                    var categoryMeta = providerEntry[categoryKey];
                    if (!categoryMeta || typeof categoryMeta !== 'object') {
                        return;
                    }
                    var categoryId = providerKey + '::' + categoryKey;
                    var categoryLabel = typeof categoryMeta.label === 'string' ? categoryMeta.label : categoryKey;
                    var categoryDescription = typeof categoryMeta.description === 'string' ? categoryMeta.description : '';
                    data.categories[categoryId] = {
                        id: categoryId,
                        key: categoryKey,
                        provider: providerKey,
                        label: categoryLabel,
                        description: categoryDescription
                    };
                    data.categoryPresets[categoryId] = [];

                    var presets = categoryMeta.presets;
                    if (!presets || typeof presets !== 'object') {
                        return;
                    }

                    Object.keys(presets).forEach(function(presetKey) {
                        var presetMeta = presets[presetKey];
                        if (!presetMeta || typeof presetMeta !== 'object') {
                            return;
                        }
                        var presetId = providerKey + '::' + categoryKey + '::' + presetKey;
                        data.presets[presetId] = {
                            id: presetId,
                            key: presetKey,
                            provider: providerKey,
                            categoryId: categoryId,
                            categoryKey: categoryKey,
                            label: typeof presetMeta.label === 'string' ? presetMeta.label : presetKey,
                            description: typeof presetMeta.description === 'string' ? presetMeta.description : '',
                            prompt: typeof presetMeta.prompt === 'string' ? presetMeta.prompt : '',
                            task: typeof presetMeta.task === 'string' ? presetMeta.task : ''
                        };
                        data.categoryPresets[categoryId].push(presetId);
                    });
                });
            });
            return data;
        }

        function getPromptPreset(value) {
            if (!value) {
                return null;
            }
            return promptPresetLookup[value] || null;
        }

        function getPromptCategory(value) {
            if (!value) {
                return null;
            }
            return promptCategoryLookup[value] || null;
        }

        function mergePromptText($field, presetPrompt) {
            if (!$field || !$field.length) {
                return;
            }
            var presetText = (presetPrompt || '').trim();
            if (!presetText) {
                return;
            }
            var currentValue = String($field.val() || '');
            var normalizedCurrent = currentValue.trim();
            if (!normalizedCurrent) {
                $field.val(presetText);
                $field.trigger('input');
                return;
            }
            if (normalizedCurrent.indexOf(presetText) !== -1) {
                return;
            }
            var separator = /\n\s*$/.test(currentValue) ? '' : '\n\n';
            $field.val(currentValue + separator + presetText);
            $field.trigger('input');
        }

        function renderPresetDescription($target, preset) {
            if (!$target || !$target.length) {
                return;
            }
            if (!preset) {
                $target.text('').hide();
                return;
            }
            var providerName = providerLabels[preset.provider] || preset.provider || '';
            var category = preset.categoryId ? getPromptCategory(preset.categoryId) : null;
            var pieces = [];
            if (providerName) {
                pieces.push(providerName);
            }
            if (category && category.label) {
                pieces.push(category.label);
            }
            if (preset.label) {
                pieces.push(preset.label);
            }
            var headline = pieces.join(' • ');
            var descriptionText = preset.description || (category && category.description ? category.description : '');
            var message = headline;
            if (descriptionText) {
                message = message ? headline + ' — ' + descriptionText : descriptionText;
            }
            if (!message) {
                message = preset.key || '';
            }
            if (message) {
                $target.text(message).show();
            } else {
                $target.text('').hide();
            }
        }

        function renderCategoryDescription($target, category) {
            if (!$target || !$target.length) {
                return;
            }
            if (!category) {
                $target.text('').hide();
                return;
            }
            var providerName = providerLabels[category.provider] || category.provider || '';
            var pieces = [];
            if (providerName) {
                pieces.push(providerName);
            }
            if (category.label) {
                pieces.push(category.label);
            }
            var headline = pieces.join(' • ');
            var descriptionText = category.description || '';
            var message = headline;
            if (descriptionText) {
                message = message ? headline + ' — ' + descriptionText : descriptionText;
            }
            if (!message) {
                message = category.key || '';
            }
            if (message) {
                $target.text(message).show();
            } else {
                $target.text('').hide();
            }
        }

        function applyPresetChoice($field, presetValue, shouldMerge, $descriptionEl) {
            var preset = getPromptPreset(presetValue);
            if ($descriptionEl && $descriptionEl.length) {
                renderPresetDescription($descriptionEl, preset);
            }
            if (!shouldMerge || !$field || !$field.length) {
                return;
            }
            if (!preset || !preset.prompt) {
                return;
            }
            mergePromptText($field, preset.prompt);
        }

        function setupPresetControlBindings(view) {
            var $panel = view && view.$el ? view.$el : null;
            if (!$panel || !$panel.length) {
                return;
            }
            var $categorySelect = $panel.find('select[data-setting="aimentor_prompt_category"]');
            var $presetSelect = $panel.find('select[data-setting="aimentor_prompt_preset"]');
            var $promptField = $panel.find('textarea[data-setting="aimentor_prompt_text"]');
            if (!$presetSelect.length || !$promptField.length) {
                return;
            }

            var $control = $presetSelect.closest('.elementor-control');
            var $description = $control.find('.aimentor-preset-description');
            if (!$description.length) {
                $description = $('<p class="aimentor-preset-description" style="margin-top:8px;font-size:12px;color:#4b5563;display:none;"></p>');
                $control.find('.elementor-control-input-wrapper').append($description);
            }

            var presetOptionCache = [];
            $presetSelect.find('option').each(function() {
                var optionValue = typeof this.value === 'string' ? this.value : '';
                presetOptionCache.push({
                    value: optionValue,
                    label: $(this).text()
                });
            });

            function buildAllowedPresetLookup(categoryId) {
                if (!categoryId) {
                    return null;
                }
                var presetIds = promptCategoryPresetMap[categoryId];
                if (!Array.isArray(presetIds) || !presetIds.length) {
                    return {};
                }
                var lookup = {};
                presetIds.forEach(function(id) {
                    lookup[id] = true;
                });
                return lookup;
            }

            function restorePresetOptions(categoryId, preserveSelection) {
                var allowedLookup = buildAllowedPresetLookup(categoryId);
                var previousValue = preserveSelection ? ($presetSelect.val() || '') : '';
                var preservedMatch = false;

                $presetSelect.empty();

                presetOptionCache.forEach(function(option) {
                    if (!option || typeof option.value === 'undefined') {
                        return;
                    }
                    if (option.value && allowedLookup && !allowedLookup[option.value]) {
                        return;
                    }
                    var $option = $('<option></option>').val(option.value).text(option.label);
                    $presetSelect.append($option);
                    if (option.value === previousValue) {
                        preservedMatch = true;
                    }
                });

                var finalValue = preservedMatch ? previousValue : '';
                $presetSelect.val(finalValue);
                $presetSelect.data('aimentorLastPreset', finalValue);
                applyPresetChoice($promptField, finalValue, false, $description);
            }

            var $categoryDescription = $();
            if ($categorySelect.length) {
                var $categoryControl = $categorySelect.closest('.elementor-control');
                $categoryDescription = $categoryControl.find('.aimentor-preset-category-description');
                if (!$categoryDescription.length) {
                    $categoryDescription = $('<p class="aimentor-preset-category-description" style="margin-top:8px;font-size:12px;color:#4b5563;display:none;"></p>');
                    $categoryControl.find('.elementor-control-input-wrapper').append($categoryDescription);
                }
                var initialCategoryValue = $categorySelect.val() || '';
                renderCategoryDescription($categoryDescription, getPromptCategory(initialCategoryValue));
                restorePresetOptions(initialCategoryValue, true);

                $categorySelect.off('change.aimentorPresetCategory').on('change.aimentorPresetCategory', function() {
                    var value = $(this).val() || '';
                    renderCategoryDescription($categoryDescription, getPromptCategory(value));
                    restorePresetOptions(value, false);
                });
            } else {
                restorePresetOptions('', true);
            }

            $presetSelect.off('change.aimentorPreset').on('change.aimentorPreset', function() {
                var value = $(this).val() || '';
                var previous = $presetSelect.data('aimentorLastPreset') || '';
                $presetSelect.data('aimentorLastPreset', value);
                applyPresetChoice($promptField, value, !!value && value !== previous, $description);

                if ($categorySelect.length) {
                    var preset = getPromptPreset(value);
                    var presetCategoryId = preset && preset.categoryId ? preset.categoryId : '';
                    if (presetCategoryId && presetCategoryId !== ($categorySelect.val() || '')) {
                        $categorySelect.val(presetCategoryId);
                        renderCategoryDescription($categoryDescription, getPromptCategory(presetCategoryId));
                        restorePresetOptions(presetCategoryId, true);
                    }
                }
            });
        }

        function buildProviderDefaults() {
            var defaults = {};
            Object.keys(providersMeta || {}).forEach(function(key) {
                var meta = providersMeta[key] || {};
                var label = meta.label || providerLabels[key] || key;
                defaults[key] = Object.assign({
                    label: label,
                    icon: meta.icon || '🤖',
                    summary: meta.summary || providerSummaries[key] || formatString(strings.contentGenerated, label || key),
                    badgeText: meta.badgeText || label,
                    badgeColor: meta.badgeColor || '#444444'
                }, meta);
            });
            if (!Object.keys(defaults).length) {
                defaults = {
                    grok: {
                        label: providerLabels.grok || 'xAI Grok',
                        icon: '🚀',
                        summary: providerSummaries.grok || formatString(strings.contentGenerated, providerLabels.grok || 'xAI Grok'),
                        badgeText: 'xAI',
                        badgeColor: '#1E1E1E'
                    },
                    anthropic: {
                        label: providerLabels.anthropic || 'Anthropic Claude',
                        icon: '✨',
                        summary: providerSummaries.anthropic || formatString(strings.contentGenerated, providerLabels.anthropic || 'Anthropic Claude'),
                        badgeText: 'Claude',
                        badgeColor: '#FF5C35'
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
            return defaults;
        }

        function buildProviderMap(defaults) {
            var map = Object.assign({}, defaults);
            Object.keys(providerLabels || {}).forEach(function(key) {
                var label = providerLabels[key];
                var meta = map[key] || {};
                meta.label = label;
                meta.summary = providerSummaries[key] || meta.summary || formatString(strings.contentGenerated, label);
                map[key] = meta;
            });
            return map;
        }

        function sanitizeProvider(value) {
            var providers = window.AiMentorProviders || {};
            if (value && providers[value]) {
                return value;
            }
            var keys = Object.keys(providers);
            return keys.length ? keys[0] : (value || 'grok');
        }

        function getProviderMeta(key) {
            var providers = window.AiMentorProviders || {};
            var meta = providers[key];
            if (!meta) {
                var fallbackLabel = key || 'AiMentor';
                return {
                    key: key,
                    label: fallbackLabel,
                    icon: '🤖',
                    summary: formatString(strings.contentGenerated, fallbackLabel),
                    badgeText: fallbackLabel,
                    badgeColor: '#444444'
                };
            }
            var result = Object.assign({}, meta);
            result.key = key;
            if (!result.summary) {
                result.summary = formatString(strings.contentGenerated, result.label || key);
            }
            if (!result.badgeText) {
                result.badgeText = result.label || key;
            }
            if (!result.badgeColor) {
                result.badgeColor = '#444444';
            }
            if (!result.icon) {
                result.icon = '🤖';
            }
            return result;
        }

        function getTaskLabel(task) {
            if (task === 'canvas') {
                return strings.layoutLabel || strings.pageLayout || 'Page Layout';
            }
            return strings.copyLabel || strings.pageCopy || 'Page Copy';
        }

        function getTierLabel(tier) {
            if (tier === 'quality') {
                return strings.qualityLabel || 'Quality';
            }
            return strings.fastLabel || 'Fast';
        }

        function getModelLabel(providerKey, task, tier) {
            var providerEntry = (modelLabels || {})[providerKey];
            if (!providerEntry) {
                return '';
            }
            if (typeof providerEntry === 'string') {
                return providerEntry;
            }
            if (providerEntry && typeof providerEntry === 'object') {
                var taskEntry = providerEntry[task];
                if (typeof taskEntry === 'string') {
                    return taskEntry;
                }
                if (taskEntry && typeof taskEntry === 'object' && taskEntry[tier] && typeof taskEntry[tier] === 'string') {
                    return taskEntry[tier];
                }
                if (providerEntry[tier] && typeof providerEntry[tier] === 'string') {
                    return providerEntry[tier];
                }
            }
            return '';
        }

        function buildSummary(providerKey, task, tier) {
            var taskLabel = getTaskLabel(task || 'content');
            var tierLabel = getTierLabel(tier || 'fast');
            var separator = typeof strings.summarySeparator === 'string' ? strings.summarySeparator : ' • ';
            var summary = taskLabel + separator + tierLabel;
            var modelLabel = getModelLabel(providerKey, task, tier);
            if (modelLabel) {
                var powered = strings.summaryPoweredBy ? strings.summaryPoweredBy.replace('%s', modelLabel) : '– powered by ' + modelLabel;
                summary += ' ' + powered;
            }
            return summary;
        }

        function sanitizeTask(value, allowCanvas) {
            var valid = ['content'];
            if (allowCanvas) {
                valid.push('canvas');
            }
            return valid.indexOf(value) !== -1 ? value : valid[0];
        }

        function sanitizeTier(value) {
            var valid = ['fast', 'quality'];
            return valid.indexOf(value) !== -1 ? value : valid[0];
        }

        function sanitizeTonePreset(value) {
            var key = typeof value === 'string' ? value.trim() : '';
            if (!key) {
                return '';
            }
            if (key === 'custom') {
                return 'custom';
            }
            return Object.prototype.hasOwnProperty.call(tonePresetLookup, key) ? key : '';
        }

        function buildProviderOptions(defaultProvider) {
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            if (!keys.length) {
                providers = providerDefaults;
                keys = Object.keys(providers);
            }
            var optionsHtml = '';
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

        function getGeneratingMessage(meta) {
            if (strings.generatingWith) {
                return strings.generatingWith.replace('%s', meta.label || meta.key || '');
            }
            return 'Generating with ' + (meta.label || 'AiMentor') + '…';
        }

        function ensureAjaxConfig($container, $button) {
            if (aimentorData.ajaxurl && aimentorData.nonce) {
                return true;
            }
            var message = strings.missingConfig || 'AiMentor AJAX configuration is missing. Please ensure the plugin assets are enqueued properly.';
            var $notice = $('<div class="notice notice-error aimentor-missing-config"><p></p></div>');
            $notice.find('p').text(message);
            var $target = $container && $container.length ? $container : $('body');
            $target.prepend($notice);
            if ($button && $button.length) {
                $button.prop('disabled', true);
            }
            console.error('AiMentor AJAX configuration missing: expected window.aimentorAjax.');
            return false;
        }

        function extractResponseSummary(response, providerKey, selection) {
            if (response && response.data) {
                if (typeof response.data.summary === 'string' && response.data.summary.trim()) {
                    return response.data.summary.trim();
                }
                if (typeof response.data.message === 'string' && response.data.message.trim()) {
                    return response.data.message.trim();
                }
            }
            if (selection && selection.task && selection.tier) {
                return buildSummary(providerKey, selection.task, selection.tier);
            }
            var meta = getProviderMeta(providerKey);
            return formatString(strings.contentGenerated, meta.label || providerKey);
        }

        function updateSummaryText($summary, providerKey, selection) {
            if (!$summary || !$summary.length) {
                return;
            }
            var summary = buildSummary(providerKey, selection.task, selection.tier);
            if (selection && Array.isArray(selection.knowledge) && selection.knowledge.length) {
                var template = strings.knowledgePackSelectedCount || '%d knowledge packs selected';
                var message = template.indexOf('%d') !== -1 ? template.replace('%d', selection.knowledge.length) : selection.knowledge.length + ' knowledge packs selected';
                var separator = typeof strings.summarySeparator === 'string' ? strings.summarySeparator : ' • ';
                summary += separator + message;
            }
            $summary.text(summary);
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
                ui.badge.css('background-color', meta.badgeColor || '#444444');
            }
            if (ui.button && ui.button.length) {
                var buttonText = strings.askAiMentorWith ? strings.askAiMentorWith.replace('%s', meta.label || providerKey) : (strings.generateWith ? strings.generateWith.replace('%s', meta.label || providerKey) : 'Generate with ' + (meta.label || providerKey));
                ui.button.text(buttonText);
            }
            if (ui.heading && ui.heading.length) {
                var headingText = strings.writeWith ? strings.writeWith.replace('%s', meta.label || providerKey) : 'Write with ' + (meta.label || providerKey);
                ui.heading.text(headingText);
            }
            return meta;
        }

        function getDefaultProvider() {
            if (api && api.state && api.state.defaults && api.state.defaults.provider) {
                return sanitizeProvider(api.state.defaults.provider);
            }
            if (defaultsData && typeof defaultsData.provider === 'string' && defaultsData.provider) {
                return sanitizeProvider(defaultsData.provider);
            }
            if (typeof aimentorData.provider === 'string' && aimentorData.provider) {
                return sanitizeProvider(aimentorData.provider);
            }
            var providers = window.AiMentorProviders || {};
            var keys = Object.keys(providers);
            return keys.length ? keys[0] : 'grok';
        }
    });
})(jQuery, window);
