import { z } from "zod";

export const noteGroupingStoreSchema = z.object({
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
});

export const noteGroupingUpdateSchema = z.object({
    id: z.any(),
    title: z
        .string({
            required_error: "Название обязательно",
            invalid_type_error: "Название обязательно",
        })
        .nonempty("Название обязательно")
        .max(25, "Название слишком длинное"),
});
