var plugins = new Array(
    {% for plugin in plugins %}
        {% if loop.index>1 %}
        ,
        {% endif %}
        new Array(
                "{{ plugin.name }}",
                new Array(
                        {% for depend in plugin.dependencies%}
                            {% if loop.index > 1 %}
                            ,
                            {% endif %}
                            "{{ depend }}"
                        {% endfor %}
                )
        )
    {% endfor %}
);