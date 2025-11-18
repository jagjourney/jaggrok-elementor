# Canvas vs Copy Generation Smoke Checklist

These smoke tests confirm the preset resolver picks the correct provider defaults for canvas and content tasks after the structured context refactor.

## Preconditions
- The AiMentor Elementor plugin is activated.
- API keys for xAI Grok and OpenAI are configured in **AiMentor → Settings**.
- Elementor editor is available on a test page or template.

## Steps

### 1. Canvas generation (xAI Grok)
1. Open Elementor on a blank canvas and launch the AiMentor generator.
2. Select **Canvas** as the task type and **Quality** as the performance tier.
3. Trigger a generation using a prompt such as “Create a pricing page with tiered plans and a contact form.”
4. Observe the developer console (Network tab) for the `aimentor_generate_page` AJAX call (or the legacy `jaggrok_generate_page` alias) and confirm the JSON response includes:
   - `task: "canvas"`
   - `tier: "quality"`
   - `provider: "grok"`
   - `model` value containing the `grok-4-code` preset (or the configured override).
   - `canvas_variations` array containing decoded layout options with `summary` metadata.
5. Verify the returned content renders as structured Elementor JSON (widgets, containers, responsive columns).
6. Insert a variation from the UI cards and confirm the chosen layout is added to the canvas history carousel with the combined label + summary.

### 2. Copy generation (OpenAI)
1. Switch the provider to **OpenAI** in the generator UI.
2. Choose **Content** for the task type and **Fast** for the performance tier.
3. Run a prompt such as “Write a hero section for a summer sale landing page.”
4. Validate the AJAX response metadata shows:
   - `task: "content"`
   - `tier: "fast"`
   - `provider: "openai"`
   - `model` returning `gpt-4o-mini` (or a configured alternative).
5. Confirm the HTML output is clean, copy-focused markup ready for Elementor insertion.

### 3. Error logging sanity
1. Temporarily clear the configured API key for the active provider.
2. Trigger another generation request.
3. Check `wp-content/uploads/aimentor/aimentor-errors.log` (or the custom log location) for an entry containing the provider, model, task, and tier metadata to aid troubleshooting.

### Expected outcome
- Canvas requests are routed to the structured Codex-style models with JSON responses.
- Copy requests use the latest writing-focused models and return HTML.
- Error logs always include the task and tier context when failures occur.
