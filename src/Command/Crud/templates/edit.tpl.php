{% extends 'layout_page.html.twig' %}

{% block title 'Edit <?= $vars['entityTitle'] ?>' %}

{% block toolbar %}
    <a class="btn btn-primary btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_index') }}">Manage</a>
    <a class="btn btn-info btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_show', {'id': <?= $vars['instanceNameSingular'] ?>.id}) }}">Show</a>
{% endblock %}

{% block content %}
    {{ include('<?= $vars['templatePathPrefix'] ?>/_form.html.twig') }}
{% endblock %}
