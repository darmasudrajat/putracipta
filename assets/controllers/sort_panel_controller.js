import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['widget']
    static values = {
        formTarget: String
    }

    initialize() {
        this.fieldRef = {};
        this.widgetTargets.forEach(widget => {
            if (this.fieldRef[widget.dataset.widgetFieldName] === undefined) {
                this.fieldRef[widget.dataset.widgetFieldName] = [];
            }
            const index = parseInt(widget.dataset.widgetIndex);
            this.fieldRef[widget.dataset.widgetFieldName][index] = widget;
        });
    }

    clear(event) {
        this.widgetTargets.forEach(widget => {
            widget.value = '';
        });
    }

    sync(event) {
        event.detail.fieldList.forEach(field => {
            for (let i = 0; i < event.detail.values.length; i++) {
                this.fieldRef[field][i].value = event.detail.values[i];
            }
        });
        const form = document.querySelector(this.formTargetValue);
        form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
};
