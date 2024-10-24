{% extends 'layout_page.html.twig' %}

{% block title 'Show <?= $vars['entityTitle'] ?>' %}

{% block toolbar %}
    <a class="btn btn-primary btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_index') }}">Manage</a>
    <a class="btn btn-warning btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_edit', {'id': <?= $vars['instanceNameSingular'] ?>.id}) }}">Edit</a>
{% endblock %}

{% block content %}
    <table class="table table-bordered table-striped">
        <tbody>
<?php foreach ($vars['dataHeaders'] as $index => $dataHeader): ?>
            <tr>
                <th><?= $dataHeader ?></th>
                <td>{{ <?= $vars['dataFieldValues'][$index] ?> }}</td>
            </tr>
<?php endforeach ?>
        </tbody>
    </table>

    {{ include('<?= $vars['templatePathPrefix'] ?>/_delete_form.html.twig') }}
{% endblock %}
