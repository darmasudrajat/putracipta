import { Controller } from '@hotwired/stimulus';
import * as helper from '../helpers';

export default class extends Controller {
    doNothing(event) {
    }

    confirm(event) {
        if (!window.confirm(event.params.confirmMessage)) {
            event.preventDefault();
        }
    }

    bind(event) {
        const $helper = helper;
        const $event = event;
        const $element = this.element;
        if (event.params.bindAction !== undefined) {
            eval(event.params.bindAction);
        } else {
            for (const spec of event.params.bindSpecifications) {
                if (spec.eventTypes === undefined || spec.eventTypes.includes(event.type)) {
                    let $sources = null;
                    let $destinations = null;
                    if (spec.sources !== undefined) {
                        $sources = eval(spec.sources);
                    } else if (spec.sourcesTarget !== undefined) {
                        $sources = [...document.querySelectorAll(spec.sourcesTarget)];
                    } else {
                        $sources = [$element];
                    }
                    if (spec.destinations !== undefined) {
                        $destinations = eval(spec.destinations);
                    } else if (spec.destinationsTarget !== undefined) {
                        $destinations = [...document.querySelectorAll(spec.destinationsTarget)];
                    } else {
                        $destinations = [$element];
                    }
                    switch (spec.action) {
                        case 'putHtmlContent':
                            const $content = eval(spec.descriptor.content);
                            for (const $destination of $destinations) {
                                if (spec.condition === undefined || eval(spec.condition)) {
                                    if (spec.descriptor.mode === undefined || spec.descriptor.mode === 'replace') {
                                        $destination.innerHTML = $content;
                                    } else if (spec.descriptor.mode === 'prepend') {
                                        $destination.insertAdjacentHTML('afterbegin', $content);
                                    } else if (spec.descriptor.mode === 'append') {
                                        $destination.insertAdjacentHTML('beforeend', $content);
                                    }
                                }
                            }
                            break;
                        case 'setPropertyValue':
                            const $value = eval(spec.descriptor.value);
                            for (const $destination of $destinations) {
                                if (spec.condition === undefined || eval(spec.condition)) {
                                    $destination[spec.descriptor.property] = $value;
                                }
                            }
                            break;
                        case 'setAttribute':
                            for (const $destination of $destinations) {
                                if (spec.condition === undefined || eval(spec.condition)) {
                                    $destination.setAttribute(spec.descriptor.name, spec.descriptor.value === undefined ? spec.descriptor.name : spec.descriptor.value);
                                }
                            }
                            break;
                        case 'removeAttribute':
                            for (const $destination of $destinations) {
                                if (spec.condition === undefined || eval(spec.condition)) {
                                    $destination.removeAttribute(spec.descriptor.name);
                                }
                            }
                            break;
                        case 'addClass':
                            for (const $destination of $destinations) {
                                if (spec.condition === undefined || eval(spec.condition)) {
                                    $destination.classList.add(spec.descriptor.name);
                                }
                            }
                            break;
                        case 'removeClass':
                            for (const $destination of $destinations) {
                                if (spec.condition === undefined || eval(spec.condition)) {
                                    $destination.classList.remove(spec.descriptor.name);
                                }
                            }
                            break;
                    }
                }
            }
        }
    }

    appendHtml(event) {
        const doc = new DOMParser().parseFromString(event.params.appendHtmlTemplate, "text/html");
        const html = this.getNormalizedTemplate(doc.documentElement.textContent, event.detail);
        if (!this.element.innerHTML.includes(html)) {
            this.element.insertAdjacentHTML('beforeend', html);
        }
    }

    putContent(event) {
        const content = this.getNormalizedTemplate(event.params.putContentTemplate, event.detail);
        helper.putValueContent(this.element, content);
    }

    getNormalizedTemplate(template, values) {
        return template.replace(/(?<=^|[^$])\$\{([^}]+)\}/g, function(match, p1) {
            return helper.getPropertyValue(values, p1);
        });
    }
};
