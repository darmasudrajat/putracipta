import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item']
    static classes = ['active']

    itemTargetConnected(target) {
        if (this.activeClasses.every(activeClass => target.classList.contains(activeClass))) {
            target.scrollIntoView({block: 'nearest'});
        }
    }
};
