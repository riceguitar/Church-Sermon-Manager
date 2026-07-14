const choices = new Choices('.et_pb_include_taxonomies-select', {
    removeItemButton: true,
});

choices.passedElement.addEventListener('change', function (event) { // @todo - this works, it just needs to be triggered on each "select" input.
    let element = jQuery('[name="et_pb_include_taxonomies"]');
    let existingValues = element.val();

    existingValues = existingValues ? JSON.parse(existingValues) : [];

    if (existingValues.indexOf(event.detail.value) === -1) {
        existingValues.push(event.detail.value);
    }

    element.val(JSON.stringify(existingValues));
    element.attr('value', JSON.stringify(existingValues));
}, false);
