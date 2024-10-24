import formatter from './formatter';
import normalizer from './normalizer';

export default {
    number: function(value, precision = 0) {
        return formatter.number(normalizer.number(value, ',', '.'), precision, ',', '.');
    }
};
