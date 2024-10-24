{% extends 'layout_page.html.twig' %}

{% block title 'Add <?= $vars['entityTitle'] ?>' %}

{% block toolbar %}
    <a class="btn btn-primary btn-sm" href="{{ path('<?= $vars['routeNamePrefix'] ?>_index') }}">Manage</a>
{% endblock %}

{% block content %}
    {{ include('<?= $vars['templatePathPrefix'] ?>/_form.html.twig') }}
{% endblock %}
