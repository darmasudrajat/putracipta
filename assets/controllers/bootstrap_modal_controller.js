import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    open() {
        const modal = bootstrap.Modal.getOrCreateInstance(this.element);
        modal.show();
    }

    close() {
        const modal = bootstrap.Modal.getOrCreateInstance(this.element);
        modal.hide();
    }
};
