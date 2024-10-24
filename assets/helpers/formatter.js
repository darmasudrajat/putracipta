export default {
    number: function(value, precision = 0, decimal = '.', separator = ',') {
        let num = !isNaN(parseFloat(value)) && isFinite(value) ? value : 0;
        num = Math.round(num + 'e' + precision) + 'e-' + precision;
        if (isNaN(num)) {
            num = 0;
        }
        const parts = Number(num).toFixed(precision).toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, separator);
        return parts.join(decimal);
    },

    datetime: function(value, format = 'YYYY-MM-DD hh:mm:ss') {
        const dateObj = new Date(value);
        const date = dateObj.getDate();
        const month = dateObj.getMonth();
        const year = dateObj.getFullYear();
        const hour = dateObj.getHours();
        const minute = dateObj.getMinutes();
        const second = dateObj.getSeconds();
        const pad = val => ('00' + val).slice(-2);
        let str = format;
        if (str.indexOf('Y') !== -1) {
            str = str.replace('YYYY', year);
            str = str.replace('YY', (year + '').substr(-2, 2));
        }
        if (str.indexOf('D') !== -1) {
            str = str.replace('DD', pad(date));
            str = str.replace('D', date);
        }
        if (str.indexOf('h') !== -1) {
            str = str.replace('hh', pad(hour));
            str = str.replace('h', hour);
        }
        if (str.indexOf('m') !== -1) {
            str = str.replace('mm', pad(minute));
            str = str.replace('m', minute);
        }
        if (str.indexOf('s') !== -1) {
            str = str.replace('ss', pad(second));
            str = str.replace('s', second);
        }
        if (str.indexOf('M') !== -1) {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            str = str.replace('MMMM', monthNames[month]);
            str = str.replace('MMM', monthNames[month].substr(0, 3));
            str = str.replace('MM', pad(month + 1));
            str = str.replace('M', month + 1);
        }
        return str;
    }
};
