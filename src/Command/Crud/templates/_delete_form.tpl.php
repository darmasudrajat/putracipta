<form method="post" action="{{ path('<?= $vars['routeNamePrefix'] ?>_delete', {'id': <?= $vars['instanceNameSingular'] ?>.id}) }}">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ <?= $vars['instanceNameSingular'] ?>.id) }}">
    <div class="d-grid">
        <button class="btn btn-danger btn-sm"
                data-controller="dom-element"
                data-action="dom-element#confirm"
                data-dom-element-confirm-message-param="Are you sure you want to delete this item?">
            Delete
        </button>
    </div>
</form>
