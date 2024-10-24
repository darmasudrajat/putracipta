import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        options: Object
    }

    connect() {
        flatpickr(this.element, this.optionsValue);
    }
};
