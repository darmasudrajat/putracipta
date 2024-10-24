import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click(event) {
        this.dispatch('click');
    }
};
