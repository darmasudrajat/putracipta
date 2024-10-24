import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String,
        method: {type: String, default: 'POST'},
        formTarget: {type: String, default: 'form'},
        autoLoad: Boolean
    }

    connect() {
        if (this.autoLoadValue) {
            this.loadContent(this.urlValue, this.methodValue, this.formTargetValue);
        }
    }

    load(event) {
        const url = event.params.url !== undefined ? event.params.url : this.urlValue;
        const method = event.params.method !== undefined ? event.params.method : this.methodValue;
        const formTarget = event.params.formTarget !== undefined ? event.params.formTarget : this.formTargetValue;
        this.loadContent(url, method, formTarget);
    }

    loadContent(url, method, formTarget) {
        const formElements = document.querySelectorAll(formTarget);
        if (formElements.length > 0) {
            const formData = new FormData();
            for (const formElement of formElements) {
                const newFormData = new FormData(formElement);
                for (const pair of newFormData.entries()) {
                    formData.append(pair[0], pair[1]);
                }
            }
            if (method === 'GET' || method === 'get') {
                this.fetchContent(url + '?' + new URLSearchParams(formData).toString(), {method});
            } else if (method === 'POST' || method === 'post') {
                this.fetchContent(url, {method, body: formData});
            }
        } else {
            this.fetchContent(url, {method});
        }
    }

    fetchContent(url, options) {
        fetch(url, options)
                .then(response => response.text())
                .then(html => this.element.innerHTML = html);
    }
};
