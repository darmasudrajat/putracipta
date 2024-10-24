export default function(element, content) {
    const tagName = element.tagName.toLowerCase();
    if (content) {
        if (tagName === 'input' || tagName === 'select') {
            element.value = content;
        } else {
            element.textContent = content;
        }
    }
};
