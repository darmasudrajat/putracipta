import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    forward(event) {
        const attributeList = event.params.attributesList[event.type];
        const attributes = Array.isArray(attributeList) ? attributeList : [attributeList];
        for (const attribute of attributes) {
            const prefix = event.target.getAttribute(attribute);
            this.dispatch(event.type, {detail: event.detail, target: event.target, prefix, bubbles: event.bubbles, cancelable: event.cancelable});
        }
    }

    trigger(event) {
        const elements = document.querySelectorAll(event.params.triggerElementsTarget);
        for (const $element of elements) {
            if (event.params.triggerCondition === undefined || eval(event.params.triggerCondition)) {
                $element.dispatchEvent(new Event(event.params.triggerEventType));
            }
        }
    }
};
