import { Controller } from '@hotwired/stimulus';

/**
 * Two-level privacy consent modal: quick accept/reject and detailed category form.
 */
export default class extends Controller {
    static targets = ['quickPanel', 'detailsPanel'];

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
}
