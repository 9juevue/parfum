export function resetObject(obj) {
    Object.keys(obj).forEach((key) => {
        const value = obj[key];

        if (Array.isArray(value)) {
            obj[key] = [];
        } else if (value !== null && typeof value === "object") {
            resetObject(value);
        } else {
            obj[key] = null;
        }
    });
}
