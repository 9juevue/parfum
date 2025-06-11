import { z } from "zod";

export const aromaNoteStoreSchema = z.object({
    title_ru: z
        .string({
            required_error: "Название на русском обязательно",
            invalid_type_error: "Название на русском обязательно",
        })
        .nonempty("Название на русском обязательно")
        .max(25, "Название на русском слишком длинное"),
    title_en: z
        .string({
            required_error: "Название на английском обязательно",
            invalid_type_error: "Название на английском обязательно",
        })
        .nonempty("Название на английском обязательно")
        .max(25, "Название на английском слишком длинное"),
    short_description: z
        .string({
            required_error: "Короткое описание обязательно",
            invalid_type_error: "Короткое описание обязательно",
        })
        .nonempty("Короткое описание обязательно"),
    seo_description: z.any(),
    color: z
        .string({
            required_error: "Цвет обязателен",
            invalid_type_error: "Цвет обязателен",
        })
        .nonempty("Цвет обязателен"),
    note_grouping_id: z.number({
        required_error: "Группировка нот обязательна",
        invalid_type_error: "Группировка нот обязательна",
    }),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const aromaNoteUpdateSchema = z.object({
    id: z.any(),
    title_ru: z
        .string({
            required_error: "Название на русском обязательно",
            invalid_type_error: "Название на русском обязательно",
        })
        .nonempty("Название на русском обязательно")
        .max(25, "Название на русском слишком длинное"),
    title_en: z
        .string({
            required_error: "Название на английском обязательно",
            invalid_type_error: "Название на английском обязательно",
        })
        .nonempty("Название на английском обязательно")
        .max(25, "Название на английском слишком длинное"),
    short_description: z
        .string({
            required_error: "Короткое описание обязательно",
            invalid_type_error: "Короткое описание обязательно",
        })
        .nonempty("Короткое описание обязательно"),
    seo_description: z.any(),
    color: z
        .string({
            required_error: "Цвет обязателен",
            invalid_type_error: "Цвет обязателен",
        })
        .nonempty("Цвет обязателен"),
    note_grouping_id: z.number({
        required_error: "Группировка нот обязательна",
        invalid_type_error: "Группировка нот обязательна",
    }),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});
