export default function(element) {
    const tagName = element.tagName.toLowerCase();
    if (tagName === 'input' || tagName === 'select') {
        return element.value;
    } else {
        return element.textContent;
    }
};
