import { z } from "zod";

export const countryStoreSchema = z.object({
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const countryUpdateSchema = z.object({
    id: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});
