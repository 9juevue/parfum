import { z } from "zod";

export const brandStoreSchema = z.object({
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
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
    short_description: z
        .string({
            required_error: "Короткое описание обязательно",
            invalid_type_error: "Короткое описание обязательно",
        })
        .nonempty("Короткое описание обязательно"),
    country_id: z.number({
        required_error: "Страна обязательна",
        invalid_type_error: "Страна обязательна",
    }),
    seo_title: z.any(),
    seo_description: z.any(),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
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
});

export const brandUpdateSchema = z.object({
    id: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
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
    short_description: z
        .string({
            required_error: "Короткое описание обязательно",
            invalid_type_error: "Короткое описание обязательно",
        })
        .nonempty("Короткое описание обязательно"),
    country_id: z.number({
        required_error: "Страна обязательна",
        invalid_type_error: "Страна обязательна",
    }),
    seo_title: z.any(),
    seo_description: z.any(),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
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
});
