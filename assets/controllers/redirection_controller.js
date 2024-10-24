import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String,
        method: {type: String, default: 'POST'},
        formTarget: {type: String, default: 'form'}
    }

    redirect(event) {
        const url = event.params.url !== undefined ? event.params.url : this.urlValue;
        const method = event.params.method !== undefined ? event.params.method : this.methodValue;
        const formTarget = event.params.formTarget !== undefined ? event.params.formTarget : this.formTargetValue;
        this.redirectTo(url, method, formTarget, event.params.extraValues);
    }

    redirectTo(url, method, formTarget, extraValues) {
        const formElements = document.querySelectorAll(formTarget);
        if (formElements.length > 0) {
            const formData = new FormData();
            for (const formElement of formElements) {
                const newFormData = new FormData(formElement);
                for (const pair of newFormData.entries()) {
                    formData.append(pair[0], pair[1]);
                }
            }
            const form = document.createElement('form');
            form.action = url;
            form.method = method;
            for (const pair of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = pair[0];
                input.value = pair[1];
                form.appendChild(input);
            }
            for (const [key, value] of Object.entries(extraValues)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        } else {
            window.location.href = url;
        }
    }
};
