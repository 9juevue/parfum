<script setup lang="js">
import { onMounted, reactive, ref, toRefs, watch } from "vue";
import axios from "axios";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import IconField from "primevue/iconfield";
import Dialog from "primevue/dialog";
import Message from "primevue/message";
import Skeleton from "primevue/skeleton";
import Textarea from "primevue/textarea";
import Editor from "primevue/editor";
import Select from "primevue/select";
import Fieldset from "primevue/fieldset";

import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

import {
    applyLaravelErrors,
    applyZodErrors,
} from "@/Composables/useErrorHelpers.js";
import {
    productSubtypeFormatStoreSchema,
    productSubtypeFormatUpdateSchema,
} from "@/Validation/productSubtypeFormatValidation.js";
import { resetObject } from "@/Composables/useResetObject.js";

import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import Dropzone from "@/Components/Dropzone.vue";
import { getCleanHTML } from "@/Composables/useQuillUtils.js";
import MultiSelect from "primevue/multiselect";

const props = defineProps({
    productSubtypeFormats: {
        type: Array,
        required: true,
        default: () => [],
    },
    productSubtypeFormatTypes: {
        type: Object,
        required: true,
        default: () => ({}),
    },
});
const { productSubtypeFormats, productSubtypeFormatTypes } = toRefs(props);
const productSubtypeFormatList = ref(
    JSON.parse(JSON.stringify(productSubtypeFormats.value)),
);
const productSubtypeFormatTypesArray = ref(
    Object.values(productSubtypeFormatTypes.value),
);
const availableSubtypeTypes = ref([]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const productSubtypeFormatModalVisible = ref(false);
const isLoading = ref(false);
const isEditing = ref(false);

const form = reactive({
    id: null,
    product_subtype_format: "",
    description: "",
    hint: "",
    files: [],
});
const errors = reactive({
    product_subtype_format: null,
    description: null,
    hint: null,
    files: null,
});

const toast = useToast();
const confirm = useConfirm();

watch(
    productSubtypeFormatModalVisible,
    (newVal) => {
        computeAvailable(null);
    },
    { immediate: true },
);

function computeAvailable(currentId = null) {
    const used = productSubtypeFormatList.value
        .filter((x) => currentId === null || x.id !== currentId)
        .map((x) => x.product_subtype_format);

    availableSubtypeTypes.value = productSubtypeFormatTypesArray.value.filter(
        (opt) =>
            currentId !== null
                ? opt.value === form.id || !used.includes(opt.value)
                : !used.includes(opt.value),
    );
}

function resetForm() {
    resetObject(errors);
    resetObject(form);
}

function onCancel() {
    resetForm();
    productSubtypeFormatModalVisible.value = false;
    isEditing.value = false;
}

async function submit() {
    resetObject(errors);

    const result = isEditing.value
        ? productSubtypeFormatUpdateSchema.safeParse(form)
        : productSubtypeFormatStoreSchema.safeParse(form);

    if (!result.success) {
        applyZodErrors(result, errors);
        return;
    }

    form.seo_description = getCleanHTML(form.seo_description);

    isEditing.value
        ? await updateProductSubtypeFormat(result.data)
        : await storeProductSubtypeFormat(result.data);
}

async function getProductSubtypeFormat(productSubtypeFormatId) {
    try {
        const response = await axios.get(
            route("admin.product-subtype-formats.show", productSubtypeFormatId),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function editProductSubtypeFormat(item) {
    isEditing.value = true;
    isLoading.value = true;
    productSubtypeFormatModalVisible.value = true;
    resetForm();

    try {
        const productSubtypeFormat = await getProductSubtypeFormat(item.id);

        if (!productSubtypeFormat) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные бренда",
                life: 3000,
            });
            return;
        }

        const copy = JSON.parse(JSON.stringify(productSubtypeFormat));

        Object.keys(form).forEach((key) => {
            if (key in copy) {
                form[key] = copy[key];
            }
        });
        form.files = [
            {
                url: productSubtypeFormat.image_url,
            },
        ];

        computeAvailable(copy.id);
    } catch (e) {
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные бренда",
            life: 3000,
        });
    } finally {
        isLoading.value = false;
    }
}

function confirmDelete(productSubtypeFormat) {
    confirm.require({
        message: "Вы действительно хотите удалить эту запись?",
        header: "Подтверждение удаления",
        icon: "pi pi-info-circle",
        rejectLabel: "Отмена",
        rejectProps: {
            label: "Отмена",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Удалить",
            severity: "danger",
        },
        accept: () => {
            deleteProductSubtypeFormat(productSubtypeFormat);
        },
    });
}

async function storeProductSubtypeFormat(productSubtypeFormat) {
    try {
        const response = await axios.post(
            route("admin.product-subtype-formats.store"),
            productSubtypeFormat,
        );

        productSubtypeFormatList.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Бренд сохранена",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function updateProductSubtypeFormat(productSubtypeFormat) {
    try {
        const response = await axios.patch(
            route(
                "admin.product-subtype-formats.update",
                productSubtypeFormat.id,
            ),
            productSubtypeFormat,
        );

        if (response.data) {
            const idx = productSubtypeFormatList.value.findIndex(
                (item) => item.id === response.data.id,
            );

            if (idx !== -1) {
                productSubtypeFormatList.value[idx] = response.data;
            }
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Бренд обновлен",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function deleteProductSubtypeFormat(productSubtypeFormat) {
    axios
        .delete(
            route(
                "admin.product-subtype-formats.destroy",
                productSubtypeFormat,
            ),
        )
        .then((res) => {
            let idx = productSubtypeFormatList.value.findIndex(
                (item) => item.id === productSubtypeFormat.id,
            );

            if (idx !== -1) {
                productSubtypeFormatList.value.splice(idx, 1);
            }

            toast.add({
                severity: "success",
                summary: "Информация",
                detail: "Запись успешно удалена!",
                life: 3000,
            });
        })
        .catch((error) => {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось удалить запись!",
                life: 3000,
            });
        });
}
</script>

<template>
    <Head title="Каталог" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Формат подвида товаров
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <DataTable
                        :value="productSubtypeFormatList"
                        paginator
                        :rows="5"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        tableStyle="min-width: 50rem"
                        v-model:filters="filters"
                        :global-filter-fields="['title']"
                    >
                        <template #header>
                            <div
                                class="col mb-4 flex items-center justify-between"
                            >
                                <div
                                    class="flex w-full flex-wrap items-center justify-between gap-2"
                                >
                                    <span class="text-xl font-bold"
                                        >Формат подвида товаров</span
                                    >
                                    <Button
                                        icon="pi pi-plus"
                                        label="Добавить"
                                        @click="
                                            productSubtypeFormatModalVisible = true;
                                            isEditing = false;
                                        "
                                    />
                                </div>
                            </div>
                            <div class="flex">
                                <IconField class="w-full">
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Поиск"
                                        class="w-full"
                                    />
                                    <InputIcon class="pi pi-search" />
                                </IconField>
                            </div>
                        </template>
                        <Column
                            field="title"
                            header="Название"
                            sortable
                        ></Column>
                        <Column header="Изображение">
                            <template #body="slotProps">
                                <div
                                    class="flex h-20 w-20 items-center justify-center bg-white"
                                >
                                    <img
                                        :src="slotProps.data.image_url"
                                        :alt="slotProps.data.title"
                                        class="max-h-full max-w-full object-contain"
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column style="width: 10rem">
                            <template #body="slotProps">
                                <div class="flex flex-wrap gap-2">
                                    <Button
                                        type="button"
                                        icon="pi pi-pencil"
                                        severity="info"
                                        variant="text"
                                        rounded
                                        as="a"
                                        @click="
                                            editProductSubtypeFormat(
                                                slotProps.data,
                                            )
                                        "
                                    />
                                    <Button
                                        type="button"
                                        icon="pi pi-trash"
                                        severity="danger"
                                        variant="text"
                                        rounded
                                        @click="confirmDelete(slotProps.data)"
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                    <Dialog
                        v-model:visible="productSubtypeFormatModalVisible"
                        modal
                        :header="
                            isEditing
                                ? 'Редактирование бренда'
                                : 'Создание бренда'
                        "
                        style="width: 70rem"
                        @hide="onCancel"
                    >
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="title"
                                class="required-label font-semibold"
                                >Название</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Select
                                    v-model="form.product_subtype_format"
                                    id="product_subtype_format"
                                    class="flex-auto"
                                    autocomplete="off"
                                    :invalid="!!errors.product_subtype_format"
                                    :options="availableSubtypeTypes"
                                    option-label="title"
                                    option-value="value"
                                    placeholder="Выберите название"
                                    empty-message="Выберите название"
                                    :show-toggle-all="false"
                                />
                                <Message
                                    v-if="errors.product_subtype_format"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.product_subtype_format }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="description"
                                class="required-label font-semibold"
                                >Описание (для модального окна в карточке
                                товара)</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Textarea
                                    v-else
                                    v-model="form.description"
                                    id="description"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите текст"
                                    :invalid="!!errors.description"
                                    :rows="5"
                                />
                                <Message
                                    v-if="errors.description"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.description }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="hint"
                                class="required-label font-semibold"
                                >Текст о типе</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Textarea
                                    v-else
                                    v-model="form.hint"
                                    id="hint"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите текст"
                                    :invalid="!!errors.hint"
                                    :rows="5"
                                />
                                <Message
                                    v-if="errors.hint"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.hint }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="image"
                                class="required-label font-semibold"
                                >Изображение</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="4rem"
                                ></Skeleton>
                                <Dropzone
                                    v-else
                                    v-model:files="form.files"
                                    :max-files="1"
                                    :invalid="!!errors.files"
                                    accepted-files=".svg"
                                />
                                <Message
                                    v-if="errors.files"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.files }}
                                </Message>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <Button
                                type="button"
                                label="Отменить"
                                severity="secondary"
                                @click="onCancel"
                            ></Button>
                            <Button
                                type="button"
                                label="Сохранить"
                                @click="submit"
                            ></Button>
                        </div>
                    </Dialog>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
