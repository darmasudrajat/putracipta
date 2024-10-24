export default {
    number: function(value, decimal = '.', separator = ',') {
        return value.replaceAll(separator, '').replaceAll(decimal, '.');
    }
};
