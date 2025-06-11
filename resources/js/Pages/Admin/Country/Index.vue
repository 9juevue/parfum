<script setup lang="js">
import { reactive, ref, toRefs } from "vue";
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

import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

import {
    applyLaravelErrors,
    applyZodErrors,
} from "@/Composables/useErrorHelpers.js";
import {
    countryStoreSchema,
    countryUpdateSchema,
} from "@/Validation/countryValidation.js";
import { resetObject } from "@/Composables/useResetObject.js";

import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import Dropzone from "@/Components/Dropzone.vue";

const props = defineProps({
    countries: {
        type: Array,
        required: true,
        default: () => [],
    },
});
const { countries } = toRefs(props);
const countryList = ref([...countries.value]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const countryModalVisible = ref(false);
const isLoading = ref(false);
const isEditing = ref(false);

const form = reactive({
    id: null,
    title: "",
    files: [],
});
const errors = reactive({
    title: null,
    files: null,
});

const toast = useToast();
const confirm = useConfirm();

function resetForm() {
    resetObject(errors);
    resetObject(form);
}

function onCancel() {
    resetForm();
    countryModalVisible.value = false;
    isEditing.value = false;
}

async function submit() {
    resetObject(errors);

    const result = isEditing.value
        ? countryUpdateSchema.safeParse(form)
        : countryStoreSchema.safeParse(form);

    if (!result.success) {
        applyZodErrors(result, errors);
        return;
    }

    isEditing.value
        ? await updateCountry(result.data)
        : await storeCountry(result.data);
}

async function getCountry(countryId) {
    try {
        const response = await axios.get(
            route("admin.countries.show", countryId),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function editCountry(item) {
    isEditing.value = true;
    isLoading.value = true;
    countryModalVisible.value = true;
    resetForm();

    try {
        const country = await getCountry(item.id);

        if (!country) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные страны",
                life: 3000,
            });
            return;
        }

        form.id = country.id;
        form.title = country.title;
        form.files = [
            {
                url: country.image_url,
            },
        ];
    } catch (e) {
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные страны",
            life: 3000,
        });
    } finally {
        isLoading.value = false;
    }
}

function confirmDelete(country) {
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
            deleteCountry(country);
        },
    });
}

async function storeCountry(country) {
    console.log(country);
    try {
        const response = await axios.post(
            route("admin.countries.store"),
            country,
        );

        countryList.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Страна сохранена",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function updateCountry(country) {
    try {
        const response = await axios.patch(
            route("admin.countries.update", country),
            country,
        );

        if (response.data) {
            const idx = countryList.value.findIndex(
                (item) => item.id === response.data.id,
            );

            if (idx !== -1) {
                countryList.value[idx] = response.data;
            }
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Страна обновлена",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function deleteCountry(country) {
    axios
        .delete(route("admin.countries.destroy", country))
        .then((res) => {
            let idx = countryList.value.findIndex(
                (item) => item.id === country.id,
            );

            if (idx !== -1) {
                countryList.value.splice(idx, 1);
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
                Страны
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <DataTable
                        :value="countryList"
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
                                        >Страны</span
                                    >
                                    <Button
                                        icon="pi pi-plus"
                                        label="Добавить"
                                        @click="
                                            countryModalVisible = true;
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
                                        @click="editCountry(slotProps.data)"
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
                        v-model:visible="countryModalVisible"
                        modal
                        :header="
                            isEditing
                                ? 'Редактирование страны'
                                : 'Создание страны'
                        "
                        style="width: 35rem"
                        @hide="onCancel"
                    >
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="title"
                                class="required-label w-24 font-semibold"
                                >Название</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <InputText
                                    v-else
                                    v-model="form.title"
                                    id="title"
                                    class="flex-auto"
                                    autocomplete="off"
                                    maxlength="25"
                                    placeholder="Введите название"
                                    :invalid="!!errors.title"
                                />
                                <Message
                                    v-if="errors.title"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.title }}
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
