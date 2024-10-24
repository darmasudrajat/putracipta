import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        panelTarget: String
    }

    post(event) {
        const panel = document.querySelector(this.panelTargetValue);
        this.dispatch('sync', {prefix: 'pagination-panel', target: panel, detail: {number: event.params.number}});
    }
};
