import { z } from "zod";

const currentYear = new Date().getFullYear();

export const productStoreSchema = z.object({
    category_type: z.any(),
    subtype_category_type: z.number({
        required_error: "Категория типов подвидов обязательна",
        invalid_type_error: "Категория типов подвидов обязательна",
    }),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(78, "Название слишком длинное"),
    slug: z.preprocess(
        (arg) => (arg === "" ? null : arg),
        z
            .string()
            .regex(
                /^[a-z0-9-]+$/,
                'Допустимы только латинские буквы, цифры и "-"',
            )
            .optional()
            .nullish(),
    ),
    synonyms: z.array(
        z.object({
            id: z.any(),
            title: z
                .string({
                    required_error: "Поле должно быть заполнено",
                    invalid_type_error: "Поле должно быть заполнено",
                })
                .nonempty("Поле должно быть заполнено")
                .max(255, "Синоним слишком длинный"),
        }),
    ),
    sku: z
        .string({
            required_error: "Артикул обязателен",
            invalid_type_error: "Артикул обязателен",
        })
        .nonempty("Артикул обязателен")
        .max(25, "Артикул слишком длинный"),
    tag: z.any(),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    brand_id: z.number({
        required_error: "Бренд обязателен",
        invalid_type_error: "Бренд обязателен",
    }),
    perfumers: z.array(z.number()).min(1, "Парфюмеры обязательны"),
    year: z
        .number({
            required_error: "Год создания обязателен",
            invalid_type_error: "Год создания обязателен",
        })
        .min(1900, "Введите число от 1900 до текущего")
        .max(currentYear, "Введите число от 1900 до текущего"),
    gender_type: z.number({
        required_error: "Гендер обязателен",
        invalid_type_error: "Гендер обязателен",
    }),
    video: z.any(),
    aroma_groups: z.array(z.number()).min(1, "Группы аромата обязательны"),
    aroma_chords: z.array(z.number()).min(1, "Аккорды аромата обязательны"),
    base_notes: z.array(z.number()).min(1, "Базовые ноты аромата обязательны"),
    middle_notes: z
        .array(z.number())
        .min(1, "Средние ноты аромата обязательны"),
    top_notes: z.array(z.number()).min(1, "Верхние ноты аромата обязательны"),
    longevity: z
        .number({
            required_error: "Стойкость обязательна",
            invalid_type_error: "Стойкость обязательна",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    sillage: z
        .number({
            required_error: "Шлейф обязателен",
            invalid_type_error: "Шлейф обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    season: z
        .number({
            required_error: "Сезон обязателен",
            invalid_type_error: "Сезон обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    time_of_day: z
        .number({
            required_error: "Время дня обязателен",
            invalid_type_error: "Время дня обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    gender: z
        .number({
            required_error: "Гендер обязателен",
            invalid_type_error: "Гендер обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
                is_favorite: z.boolean().default(false),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const giftSetStoreSchema = z.object({
    category_type: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(78, "Название слишком длинное"),
    slug: z.preprocess(
        (arg) => (arg === "" ? null : arg),
        z
            .string()
            .regex(
                /^[a-z0-9-]+$/,
                'Допустимы только латинские буквы, цифры и "-"',
            )
            .optional()
            .nullish(),
    ),
    synonyms: z.array(
        z.object({
            id: z.any(),
            title: z
                .string({
                    required_error: "Поле должно быть заполнено",
                    invalid_type_error: "Поле должно быть заполнено",
                })
                .nonempty("Поле должно быть заполнено")
                .max(255, "Синоним слишком длинный"),
        }),
    ),
    sku: z
        .string({
            required_error: "Артикул обязателен",
            invalid_type_error: "Артикул обязателен",
        })
        .nonempty("Артикул обязателен")
        .max(25, "Артикул слишком длинный"),
    tag: z.any(),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    brand_id: z.number({
        required_error: "Бренд обязателен",
        invalid_type_error: "Бренд обязателен",
    }),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
                is_favorite: z.boolean().default(false),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const parfumBoxStoreSchema = z.object({
    category_type: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(78, "Название слишком длинное"),
    slug: z.preprocess(
        (arg) => (arg === "" ? null : arg),
        z
            .string()
            .regex(
                /^[a-z0-9-]+$/,
                'Допустимы только латинские буквы, цифры и "-"',
            )
            .optional()
            .nullish(),
    ),
    synonyms: z.array(
        z.object({
            id: z.any(),
            title: z
                .string({
                    required_error: "Поле должно быть заполнено",
                    invalid_type_error: "Поле должно быть заполнено",
                })
                .nonempty("Поле должно быть заполнено")
                .max(255, "Синоним слишком длинный"),
        }),
    ),
    sku: z
        .string({
            required_error: "Артикул обязателен",
            invalid_type_error: "Артикул обязателен",
        })
        .nonempty("Артикул обязателен")
        .max(25, "Артикул слишком длинный"),
    tag: z.any(),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    parfum_box_subtypes: z
        .array(
            z.object({
                id: z.any(),
            }),
        )
        .min(1, "Связанные подвиды отливантов обязательны"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
                is_favorite: z.boolean().default(false),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const productUpdateSchema = z.object({
    id: z.any(),
    category_type: z.any(),
    subtype_category_type: z.number({
        required_error: "Категория типов подвидов обязательна",
        invalid_type_error: "Категория типов подвидов обязательна",
    }),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(78, "Название слишком длинное"),
    slug: z.preprocess(
        (arg) => (arg === "" ? null : arg),
        z
            .string()
            .regex(
                /^[a-z0-9-]+$/,
                'Допустимы только латинские буквы, цифры и "-"',
            )
            .optional()
            .nullish(),
    ),
    synonyms: z.array(
        z.object({
            id: z.any(),
            title: z
                .string({
                    required_error: "Поле должно быть заполнено",
                    invalid_type_error: "Поле должно быть заполнено",
                })
                .nonempty("Поле должно быть заполнено")
                .max(255, "Синоним слишком длинный"),
        }),
    ),
    sku: z
        .string({
            required_error: "Артикул обязателен",
            invalid_type_error: "Артикул обязателен",
        })
        .nonempty("Артикул обязателен")
        .max(25, "Артикул слишком длинный"),
    tag: z.any(),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    brand_id: z.number({
        required_error: "Бренд обязателен",
        invalid_type_error: "Бренд обязателен",
    }),
    perfumers: z.array(z.number()).min(1, "Парфюмеры обязательны"),
    year: z
        .number({
            required_error: "Год создания обязателен",
            invalid_type_error: "Год создания обязателен",
        })
        .min(1900, "Введите число от 1900 до текущего")
        .max(currentYear, "Введите число от 1900 до текущего"),
    gender_type: z.number({
        required_error: "Гендер обязателен",
        invalid_type_error: "Гендер обязателен",
    }),
    video: z.any(),
    aroma_groups: z.array(z.number()).min(1, "Группы аромата обязательны"),
    aroma_chords: z.array(z.number()).min(1, "Аккорды аромата обязательны"),
    base_notes: z.array(z.number()).min(1, "Базовые ноты аромата обязательны"),
    middle_notes: z
        .array(z.number())
        .min(1, "Средние ноты аромата обязательны"),
    top_notes: z.array(z.number()).min(1, "Верхние ноты аромата обязательны"),
    longevity: z
        .number({
            required_error: "Стойкость обязательна",
            invalid_type_error: "Стойкость обязательна",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    sillage: z
        .number({
            required_error: "Шлейф обязателен",
            invalid_type_error: "Шлейф обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    season: z
        .number({
            required_error: "Сезон обязателен",
            invalid_type_error: "Сезон обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    time_of_day: z
        .number({
            required_error: "Время дня обязателен",
            invalid_type_error: "Время дня обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    gender: z
        .number({
            required_error: "Гендер обязателен",
            invalid_type_error: "Гендер обязателен",
        })
        .min(1, "Выберите число от 1 до 10")
        .max(10, "Выберите число от 1 до 10"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
                is_favorite: z.boolean().default(false),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const giftSetUpdateSchema = z.object({
    id: z.any(),
    category_type: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(78, "Название слишком длинное"),
    slug: z.preprocess(
        (arg) => (arg === "" ? null : arg),
        z
            .string()
            .regex(
                /^[a-z0-9-]+$/,
                'Допустимы только латинские буквы, цифры и "-"',
            )
            .optional()
            .nullish(),
    ),
    synonyms: z.array(
        z.object({
            id: z.any(),
            title: z
                .string({
                    required_error: "Поле должно быть заполнено",
                    invalid_type_error: "Поле должно быть заполнено",
                })
                .nonempty("Поле должно быть заполнено")
                .max(255, "Синоним слишком длинный"),
        }),
    ),
    sku: z
        .string({
            required_error: "Артикул обязателен",
            invalid_type_error: "Артикул обязателен",
        })
        .nonempty("Артикул обязателен")
        .max(25, "Артикул слишком длинный"),
    tag: z.any(),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    brand_id: z.number({
        required_error: "Бренд обязателен",
        invalid_type_error: "Бренд обязателен",
    }),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
                is_favorite: z.boolean().default(false),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const parfumBoxUpdateSchema = z.object({
    id: z.any(),
    category_type: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(78, "Название слишком длинное"),
    slug: z.preprocess(
        (arg) => (arg === "" ? null : arg),
        z
            .string()
            .regex(
                /^[a-z0-9-]+$/,
                'Допустимы только латинские буквы, цифры и "-"',
            )
            .optional()
            .nullish(),
    ),
    synonyms: z.array(
        z.object({
            id: z.any(),
            title: z
                .string({
                    required_error: "Поле должно быть заполнено",
                    invalid_type_error: "Поле должно быть заполнено",
                })
                .nonempty("Поле должно быть заполнено")
                .max(255, "Синоним слишком длинный"),
        }),
    ),
    sku: z
        .string({
            required_error: "Артикул обязателен",
            invalid_type_error: "Артикул обязателен",
        })
        .nonempty("Артикул обязателен")
        .max(25, "Артикул слишком длинный"),
    tag: z.any(),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    parfum_box_subtypes: z
        .array(
            z.object({
                id: z.any(),
            }),
        )
        .min(1, "Связанные подвиды отливантов обязательны"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
                is_favorite: z.boolean().default(false),
            }),
        )
        .min(1, "Изображение обязательно"),
});
