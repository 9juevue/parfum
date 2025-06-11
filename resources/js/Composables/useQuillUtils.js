export function getCleanHTML(html) {
    if (html == null) {
        return "";
    }

    const str = String(html);

    return str.replace(/&nbsp;/g, " ").replace(/&amp;nbsp;/g, "&nbsp;");
}
