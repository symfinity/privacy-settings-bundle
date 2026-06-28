import { Controller } from '@hotwired/stimulus';

const unlockedEmbeds = new Set();

/**
 * Page-local media embed unlock — one facade per embedId per page view.
 */
export default class extends Controller {
    static targets = ['facade', 'frame'];

    static values = {
        embedId: String,
        src: String,
        title: String,
    };

    connect() {
        if (unlockedEmbeds.has(this.embedIdValue)) {
            this.showIframe();
        }
    }

    loadEmbed(event) {
        event.preventDefault();
        unlockedEmbeds.add(this.embedIdValue);
        this.showIframe();
    }

    openCookieSettings(event) {
        event.preventDefault();
        document.dispatchEvent(new CustomEvent('privacy-settings:open-preferences', {
            bubbles: true,
            detail: { focusCategory: 'media' },
        }));
    }

    showIframe() {
        if (!this.hasFrameTarget || !this.hasFacadeTarget) {
            return;
        }

        this.element.classList.add('privacy-media-embed--loaded');
        this.facadeTarget.hidden = true;
        this.facadeTarget.setAttribute('aria-hidden', 'true');
        this.frameTarget.hidden = false;
        this.frameTarget.removeAttribute('aria-hidden');

        if (this.frameTarget.querySelector('iframe')) {
            return;
        }

        const iframe = document.createElement('iframe');
        iframe.src = this.srcValue;
        iframe.title = this.titleValue;
        iframe.loading = 'eager';
        iframe.referrerPolicy = 'strict-origin-when-cross-origin';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.allowFullscreen = true;
        iframe.className = 'privacy-media-embed__iframe';

        this.frameTarget.replaceChildren(iframe);
    }
}
