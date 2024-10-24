{% import 'app/data_grid.html.twig' as grid %}

{{ grid.form(form) }}

{{ grid.info(form, count, <?= $vars['instanceNamePlural'] ?>) }}

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr class="table-primary" {{ grid.sort_link(form) }}>
<?php foreach ($vars['dataHeaders'] as $index => $dataHeader): ?>
                <th role="button" {{ grid.sort_link_item(form, '<?= $vars['dataFieldNames'][$index] ?>') }}>
                    <?= $dataHeader ?> {{ grid.sort_char(form.sort.<?= $vars['dataFieldNames'][$index] ?>.vars.value) }}
                </th>
<?php endforeach ?>
                <th></th>
            </tr>
            <tr class="table-light" {{ grid.filter_link(form) }}>
<?php foreach ($vars['dataFieldNames'] as $index => $dataFieldName): ?>
                <th>
                    <input type="text" {{ grid.filter_link_item(form, '<?= $dataFieldName ?>') }} value="{{ form.filter.<?= $dataFieldName ?>.1.vars.value }}" />
                </th>
<?php endforeach ?>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {% for <?= $vars['instanceNameSingular'] ?> in <?= $vars['instanceNamePlural'] ?> %}
                <tr>
<?php foreach ($vars['dataFieldValues'] as $index => $dataFieldValue): ?>
                    <td>{{ <?= $dataFieldValue ?> }}</td>
<?php endforeach ?>
                    <td>
                        <a class="btn btn-info btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_show', {'id': <?= $vars['instanceNameSingular'] ?>.id}) }}">Show</a>
                        <a class="btn btn-warning btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_edit', {'id': <?= $vars['instanceNameSingular'] ?>.id}) }}">Edit</a>
                    </td>
                </tr>
            {% else %}
                <tr>
                    <td colspan="<?= count($vars['dataHeaders']) + 1 ?>">No records found</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>

{{ grid.navigation(form, count, <?= $vars['instanceNamePlural'] ?>) }}
