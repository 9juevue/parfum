import { z } from "zod";

export function productSubtypeStoreSchema(
    requireSubtypeFormat = false,
    requireBottleVolume = false,
    requireTextVolume = false,
) {
    return z.object({
        product_id: z.number({
            required_error: "Id продукта обязательна",
            invalid_type_error: "Id продукта обязательна",
        }),
        product_subtype_type: z.number({
            required_error: "Тип подвида обязателен",
            invalid_type_error: "Тип подвида обязателен",
        }),
        price: z
            .number({
                required_error: "Стоимость обязательна",
                invalid_type_error: "Стоимость обязательна",
            })
            .min(0, "Значение должно быть больше или равно 0"),
        qty: z
            .number({
                required_error: "Наличие на складе обязательно",
                invalid_type_error: "Наличие на складе обязательно",
            })
            .min(0, "Значение должно быть больше или равно 0"),
        product_subtype_format_id: requireSubtypeFormat
            ? z.number({
                  required_error: "Формат подвида обязателен",
                  invalid_type_error: "Формат подвида обязателен",
              })
            : z.any(),
        volume: requireSubtypeFormat
            ? z.number({
                  required_error: "Объём обязателен",
                  invalid_type_error: "Объём обязателен",
              })
            : z.any(),
        bottle_volume: requireBottleVolume
            ? z.number({
                  required_error: "Объём обязателен ",
                  invalid_type_error: "Объём обязателен",
              })
            : z.any(),
        volume_text: requireTextVolume
            ? z
                  .string({
                      required_error: "Объём обязателен ",
                      invalid_type_error: "Объём обязателен",
                  })
                  .nonempty("Объём обязателен")
            : z.any(),
    });
}

export function productSubtypeUpdateSchema(
    requireSubtypeFormat = false,
    requireBottleVolume = false,
    requireTextVolume = false,
) {
    return z.object({
        id: z.any(),
        product_id: z.number({
            required_error: "Id продукта обязательна",
            invalid_type_error: "Id продукта обязательна",
        }),
        product_subtype_type: z.number({
            required_error: "Тип подвида обязателен",
            invalid_type_error: "Тип подвида обязателен",
        }),
        price: z
            .number({
                required_error: "Стоимость обязательна",
                invalid_type_error: "Стоимость обязательна",
            })
            .min(0, "Значение должно быть больше или равно 0"),
        qty: z
            .number({
                required_error: "Наличие на складе обязательно",
                invalid_type_error: "Наличие на складе обязательно",
            })
            .min(0, "Значение должно быть больше или равно 0"),
        product_subtype_format_id: requireSubtypeFormat
            ? z.number({
                  required_error: "Формат подвида обязателен",
                  invalid_type_error: "Формат подвида обязателен",
              })
            : z.any(),
        volume: requireSubtypeFormat
            ? z.number({
                  required_error: "Объём обязателен",
                  invalid_type_error: "Объём обязателен",
              })
            : z.any(),
        bottle_volume: requireBottleVolume
            ? z.number({
                  required_error: "Объём обязателен ",
                  invalid_type_error: "Объём обязателен",
              })
            : z.any(),
        volume_text: requireTextVolume
            ? z
                  .string({
                      required_error: "Объём обязателен ",
                      invalid_type_error: "Объём обязателен",
                  })
                  .nonempty("Объём обязателен")
            : z.any(),
    });
}
