function transformErrors(errors) {
    let formattedErrors = {};

    Object.keys(errors).forEach((key) => {
        const value = errors[key];

        const path = key.split(".");

        if (path.length > 1) {
            let current = formattedErrors;

            path.forEach((segment, index) => {
                if (index === path.length - 1) {
                    if (!current[segment]) {
                        current[segment] = value;
                    }
                } else {
                    if (!current[segment]) {
                        current[segment] = isNaN(path[index + 1]) ? {} : [];
                    }
                    current = current[segment];
                }
            });
        } else {
            formattedErrors[key] = value;
        }
    });

    return formattedErrors;
}

export function flattenErrors(errors, parentKey = null) {
    const flattened = {};

    Object.entries(errors).forEach(([key, value]) => {
        if (key === "_errors") return;

        if (
            value &&
            typeof value === "object" &&
            !Array.isArray(value) &&
            Object.keys(value).length === 1 &&
            "_errors" in value
        ) {
            flattened[key] = value._errors?.[0];
            return;
        }

        if (parentKey === key && value?.[key]?._errors) {
            flattened[key] = value[key]._errors?.[0];
            return;
        }

        if (value && typeof value === "object" && !Array.isArray(value)) {
            flattened[key] = flattenErrors(value, key);
        } else {
            flattened[key] = Array.isArray(value) ? value[0] : value;
        }
    });

    return flattened;
}

export function applyLaravelErrors(response, errors) {
    let laravelErrors = transformErrors(response);

    for (const field in laravelErrors) {
        if (
            Array.isArray(laravelErrors[field]) &&
            laravelErrors[field].length > 0
        ) {
            errors[field] = laravelErrors[field][0];
        }
    }
}

export function applyZodErrors(parseResult, errors) {
    const format = parseResult.error.format();
    const flattenedErrors = flattenErrors(format);

    Object.keys(flattenedErrors).forEach((field) => {
        errors[field] = flattenedErrors[field];
    });
}
