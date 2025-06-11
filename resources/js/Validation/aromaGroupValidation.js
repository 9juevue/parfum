import { z } from "zod";

export const aromaGroupStoreSchema = z.object({
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
    short_description: z
        .string({
            required_error: "Короткое описание обязательно",
            invalid_type_error: "Короткое описание обязательно",
        })
        .nonempty("Короткое описание обязательно"),
    seo_title: z.any(),
    seo_description: z.any(),
    color: z
        .string({
            required_error: "Цвет обязателен",
            invalid_type_error: "Цвет обязателен",
        })
        .nonempty("Цвет обязателен"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const aromaGroupUpdateSchema = z.object({
    id: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
    short_description: z
        .string({
            required_error: "Короткое описание обязательно",
            invalid_type_error: "Короткое описание обязательно",
        })
        .nonempty("Короткое описание обязательно"),
    seo_title: z.any(),
    seo_description: z.any(),
    color: z
        .string({
            required_error: "Цвет обязателен",
            invalid_type_error: "Цвет обязателен",
        })
        .nonempty("Цвет обязателен"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});
