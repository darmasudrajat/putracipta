import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        panelTarget: String
    }

    reset(event) {
        const panel = document.querySelector(this.panelTargetValue);
        this.dispatch('clear', {prefix: 'filter-panel', target: panel});
    }

    post(event) {
        const currentValue = event.currentTarget.value;
        const panel = document.querySelector(this.panelTargetValue);
        const fieldList = event.params.fieldList;
        const valuesMap = event.params.valuesMap;
        const specialValuesMap = event.params.specialValuesMap;
        let values = [];
        if (specialValuesMap !== undefined && specialValuesMap[currentValue] !== undefined) {
            values = specialValuesMap[currentValue];
        } else {
            values = valuesMap;
        }
        values = values.map(value => value === true ? currentValue : (value === false ? null : value));
        this.dispatch('sync', {prefix: 'filter-panel', target: panel, detail: {fieldList, values}});
    }
};
