import { Controller } from '@hotwired/stimulus';

/**
 * Two-level privacy consent: bottom-right quick actions and centered details modal.
 */
export default class extends Controller {
    static targets = ['quickPanel', 'detailsPanel'];

    connect() {
        this._onOpenPreferences = this.handleOpenPreferences.bind(this);
        document.addEventListener('privacy-settings:open-preferences', this._onOpenPreferences);
    }

    disconnect() {
        document.removeEventListener('privacy-settings:open-preferences', this._onOpenPreferences);
    }

    openDetails(event) {
        event.preventDefault();
        this.quickPanelTarget.hidden = true;
        this.detailsPanelTarget.hidden = false;
    }

    closeDetails(event) {
        event.preventDefault();
        this.detailsPanelTarget.hidden = true;
        this.quickPanelTarget.hidden = false;
    }

    handleOpenPreferences(event) {
        this.element.hidden = false;
        if (this.hasQuickPanelTarget) {
            this.quickPanelTarget.hidden = true;
        }
        if (this.hasDetailsPanelTarget) {
            this.detailsPanelTarget.hidden = false;
        }

        const focusCategory = event.detail?.focusCategory;
        if (typeof focusCategory === 'string' && focusCategory !== '') {
            const row = this.element.querySelector(`#privacy-group-${focusCategory}`);
            row?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }

    notifyConsentUpdated() {
        document.dispatchEvent(new CustomEvent('privacy-settings:consent-updated', { bubbles: true }));
    }
}
