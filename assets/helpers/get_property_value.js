export default function(property, path) {
    let value = property;
    for (const name of path.split('.')) {
        value = value[name];
    }
    return value;
};
