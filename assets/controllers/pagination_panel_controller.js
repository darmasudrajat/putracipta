import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['size', 'number']
    static values = {
        formTarget: String
    }

    sync(event) {
        if (event.detail.size !== undefined) {
            this.sizeTarget.value = event.detail.size;
        }
        if (event.detail.number !== undefined) {
            this.numberTarget.value = event.detail.number;
        }
        const form = document.querySelector(this.formTargetValue);
        form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
};
