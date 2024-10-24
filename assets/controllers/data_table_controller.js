import { Controller } from '@hotwired/stimulus';
import { getValueContent, putValueContent } from '../helpers';

export default class extends Controller {
    static targets = ['selectable']
    static classes = ['selected', 'active']
    static values = {
        selectedIdentifiersTarget: {type: String, default: ''}
    }

    initialize() {
        this.selectedIdentifiersElement = this.selectedIdentifiersTargetValue === '' ? null : document.querySelector(this.selectedIdentifiersTargetValue);
        this.selectedIdentifiers = [];
        if (this.selectedIdentifiersElement !== null) {
            const value = getValueContent(this.selectedIdentifiersElement);
            if (value !== '') {
                this.selectedIdentifiers = JSON.parse(value);
            }
        }
    }

    selectableTargetConnected(element) {
        if (this.selectedIdentifiersElement !== null) {
            const selectedIdentifier = eval(element.dataset.selectedIdentifier);
            if (this.selectedIdentifiers.includes(selectedIdentifier)) {
                element.classList.add(this.activeClass);
            }
        }
    }

    select(event) {
        if (this.selectedIdentifiersElement !== null) {
            const selectableElement = event.currentTarget;
            const selectedClass = this.selectedClass;
            const activeClass = this.activeClass;
            selectableElement.classList.remove(activeClass);
            selectableElement.classList.add(selectedClass);
            setTimeout(function() {
                selectableElement.classList.remove(selectedClass);
                selectableElement.classList.add(activeClass);
            }, 500);
            const selectedIdentifier = eval(selectableElement.dataset.selectedIdentifier);
            if (!this.selectedIdentifiers.includes(selectedIdentifier)) {
                this.selectedIdentifiers.push(selectedIdentifier);
                putValueContent(this.selectedIdentifiersElement, JSON.stringify(this.selectedIdentifiers));
            }
        }
        this.dispatch('select', {detail: event.params.values});
    }
};
