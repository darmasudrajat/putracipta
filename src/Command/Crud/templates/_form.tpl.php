{{ form_start(form) }}
    {{ form_widget(form) }}
    <div class="d-grid">
        <button class="btn btn-secondary"
                data-controller="dom-element"
                data-action="dom-element#confirm"
                data-dom-element-confirm-message-param="Are you sure you want to save this item?">
            Save
        </button>
    </div>
{{ form_end(form) }}
