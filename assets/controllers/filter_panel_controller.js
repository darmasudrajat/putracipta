import { Controller } from '@hotwired/stimulus';
import { putValueContent } from '../helpers';

export default class extends Controller {
    static targets = ['widget']
    static values = {
        formTarget: String,
        valueCountReference: Object
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

    widgetTargetConnected(element) {
        if (parseInt(element.dataset.widgetIndex) === 0) {
            this.normalizeElement(element, false);
        }
    }

    clear(event) {
        this.widgetTargets.forEach(widget => {
            putValueContent(widget, '');
        });
    }

    sync(event) {
        event.detail.fieldList.forEach(field => {
            for (let i = 0; i < event.detail.values.length; i++) {
                if (event.detail.values[i] !== null) {
                    this.fieldRef[field][i].value = event.detail.values[i];
                }
            }
            this.normalizeElement(this.fieldRef[field][0], false);
        });
        const form = document.querySelector(this.formTargetValue);
        form.dispatchEvent(new Event('submit', { cancelable: true }));
    }

    normalize(event) {
        this.normalizeElement(event.currentTarget, true);
    }

    normalizeElement(element, resetValue) {
        const count = this.valueCountReferenceValue[element.value] === undefined ? 0 : this.valueCountReferenceValue[element.value];
        this.fieldRef[element.dataset.widgetFieldName].forEach(widget => {
            const index = parseInt(widget.dataset.widgetIndex);
            if (index > count) {
                widget.value = '';
                widget.disabled = true;
                widget.style.display = 'none';
            } else if (index > 0) {
                if (resetValue) {
                    widget.value = '';
                }
                widget.disabled = false;
                widget.style.display = 'inline';
            }
        });
    }
};
