import { Controller } from '@hotwired/stimulus';

/**
 * Opt-in activation for inert privacy-gated script tags.
 */
export default class extends Controller {
    static values = {
        choices: Object,
    };

    connect() {
        this._onConsentUpdated = this.rescan.bind(this);
        document.addEventListener('privacy-settings:consent-updated', this._onConsentUpdated);
        this.rescan();
    }

    disconnect() {
        document.removeEventListener('privacy-settings:consent-updated', this._onConsentUpdated);
    }

    rescan() {
        const choices = this.readChoices();
        if (!choices) {
            return;
        }

        document.querySelectorAll('script[data-privacy-category][type="text/plain"]').forEach((node) => {
            const category = node.getAttribute('data-privacy-category');
            if (!category || !(category in choices)) {
                return;
            }

            if (choices[category]) {
                this.activateScript(node);
            }
        });
    }

    readChoices() {
        if (Object.keys(this.choicesValue).length > 0) {
            return this.choicesValue;
        }

        const bootstrap = document.getElementById('privacy-settings-effective-choices');
        if (!bootstrap) {
            return null;
        }

        try {
            return JSON.parse(bootstrap.textContent || '{}');
        } catch {
            return null;
        }
    }

    activateScript(node) {
        if (node.dataset.privacyActivated === 'true') {
            return;
        }

        const script = document.createElement('script');
        const originalType = node.getAttribute('data-original-type') || 'text/javascript';
        script.type = originalType;

        for (const attr of node.attributes) {
            if (attr.name === 'type' || attr.name === 'data-privacy-category' || attr.name === 'data-original-type') {
                continue;
            }
            script.setAttribute(attr.name, attr.value);
        }

        if (node.textContent) {
            script.textContent = node.textContent;
        }

        node.dataset.privacyActivated = 'true';
        node.parentNode?.insertBefore(script, node.nextSibling);
    }
}
