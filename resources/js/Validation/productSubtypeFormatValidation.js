import { z } from "zod";

export const productSubtypeFormatStoreSchema = z.object({
    product_subtype_format: z.number({
        required_error: "Название обязательно",
        invalid_type_error: "Название обязательно",
    }),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    hint: z
        .string({
            required_error: "Текст о типе обязателен",
            invalid_type_error: "Текст о типе обязателен",
        })
        .nonempty("Текст о типе обязателен"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});

export const productSubtypeFormatUpdateSchema = z.object({
    id: z.any(),
    product_subtype_format: z.number({
        required_error: "Название обязательно",
        invalid_type_error: "Название обязательно",
    }),
    description: z
        .string({
            required_error: "Описание обязательно",
            invalid_type_error: "Описание обязательно",
        })
        .nonempty("Описание обязательно"),
    hint: z
        .string({
            required_error: "Текст о типе обязателен",
            invalid_type_error: "Текст о типе обязателен",
        })
        .nonempty("Текст о типе обязателен"),
    files: z
        .array(
            z.object({
                url: z.string(),
                sort: z.number(),
            }),
        )
        .min(1, "Изображение обязательно"),
});
