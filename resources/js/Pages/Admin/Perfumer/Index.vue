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
    perfumerStoreSchema,
    perfumerUpdateSchema,
} from "@/Validation/perfumerValidation.js";
import { resetObject } from "@/Composables/useResetObject.js";

import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import Dropzone from "@/Components/Dropzone.vue";
import { getCleanHTML } from "@/Composables/useQuillUtils.js";

const props = defineProps({
    perfumers: {
        type: Array,
        required: true,
        default: () => [],
    },
    countries: {
        type: Array,
        required: true,
        default: () => [],
    },
});
const { perfumers } = toRefs(props);
const perfumerList = ref([...perfumers.value]);
const { countries } = toRefs(props);
const countryList = ref([...countries.value]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const perfumerModalVisible = ref(false);
const isLoading = ref(false);
const isEditing = ref(false);

const form = reactive({
    id: null,
    title: "",
    slug: "",
    short_description: "",
    country_id: "",
    seo_title: "",
    seo_description: "",
    files: [],
    synonyms: [],
});
const errors = reactive({
    title: null,
    slug: null,
    short_description: null,
    country_id: null,
    seo_title: null,
    seo_description: null,
    files: null,
    synonyms: null,
});

const toast = useToast();
const confirm = useConfirm();

function resetForm() {
    resetObject(errors);
    resetObject(form);
}

function onCancel() {
    resetForm();
    perfumerModalVisible.value = false;
    isEditing.value = false;
}

function addSynonym() {
    form.synonyms.push({
        title: "",
    });
}

function deleteSynonym(index) {
    form.synonyms.splice(index, 1);
}

async function submit() {
    resetObject(errors);

    const result = isEditing.value
        ? perfumerUpdateSchema.safeParse(form)
        : perfumerStoreSchema.safeParse(form);

    if (!result.success) {
        applyZodErrors(result, errors);
        return;
    }

    form.seo_description = getCleanHTML(form.seo_description);

    isEditing.value
        ? await updatePerfumer(result.data)
        : await storePerfumer(result.data);
}

async function getPerfumer(perfumerId) {
    try {
        const response = await axios.get(
            route("admin.perfumers.show", perfumerId),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function editPerfumer(item) {
    isEditing.value = true;
    isLoading.value = true;
    perfumerModalVisible.value = true;
    resetForm();

    try {
        const perfumer = await getPerfumer(item.id);

        if (!perfumer) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные парфюмера",
                life: 3000,
            });
            return;
        }

        const copy = JSON.parse(JSON.stringify(perfumer));

        Object.keys(form).forEach((key) => {
            if (key in copy) {
                form[key] = copy[key];
            }
        });
        form.files = [
            {
                url: perfumer.image_url,
            },
        ];
    } catch (e) {
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные парфюмера",
            life: 3000,
        });
    } finally {
        isLoading.value = false;
    }
}

function confirmDelete(perfumer) {
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
            deletePerfumer(perfumer);
        },
    });
}

async function storePerfumer(perfumer) {
    try {
        const response = await axios.post(
            route("admin.perfumers.store"),
            perfumer,
        );

        perfumerList.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Парфюмер сохранена",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function updatePerfumer(perfumer) {
    try {
        const response = await axios.patch(
            route("admin.perfumers.update", perfumer),
            perfumer,
        );

        if (response.data) {
            const idx = perfumerList.value.findIndex(
                (item) => item.id === response.data.id,
            );

            if (idx !== -1) {
                perfumerList.value[idx] = response.data;
            }
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Парфюмер обновлен",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function deletePerfumer(perfumer) {
    axios
        .delete(route("admin.perfumers.destroy", perfumer))
        .then((res) => {
            let idx = perfumerList.value.findIndex(
                (item) => item.id === perfumer.id,
            );

            if (idx !== -1) {
                perfumerList.value.splice(idx, 1);
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
                Парфюмеры
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <DataTable
                        :value="perfumerList"
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
                                        >Список парфюмеров</span
                                    >
                                    <Button
                                        icon="pi pi-plus"
                                        label="Добавить"
                                        @click="
                                            perfumerModalVisible = true;
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
                                        @click="editPerfumer(slotProps.data)"
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
                        v-model:visible="perfumerModalVisible"
                        modal
                        :header="
                            isEditing
                                ? 'Редактирование парфюмера'
                                : 'Создание парфюмера'
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
                            <label for="slug" class="font-semibold"
                                >Уникальный пользовательский URL</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <InputText
                                    v-else
                                    v-model="form.slug"
                                    id="slug"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите url"
                                    :invalid="!!errors.slug"
                                />
                                <Message
                                    v-if="errors.slug"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.slug }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="country_id"
                                class="required-label font-semibold"
                                >Страна</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Select
                                    v-else
                                    v-model="form.country_id"
                                    id="country_id"
                                    class="flex-auto"
                                    autocomplete="off"
                                    :invalid="!!errors.country_id"
                                    :options="countryList"
                                    option-label="title"
                                    option-value="id"
                                    placeholder="Выберите страну"
                                    empty-message="Выберите страну"
                                />
                                <Message
                                    v-if="errors.country_id"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.country_id }}
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
                        <div class="mb-8 flex flex-col gap-1">
                            <label for="image" class="font-semibold"
                                >Синонимы названия для поиска</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="4rem"
                                ></Skeleton>
                                <Fieldset>
                                    <template #legend>
                                        <Button
                                            icon="pi pi-plus"
                                            label="Добавить"
                                            size="small"
                                            severity="contrast"
                                            @click="addSynonym"
                                        />
                                    </template>
                                    <div ref="synonymsContainer">
                                        <span v-if="form.synonyms.length === 0"
                                            >Здесь пока пусто!</span
                                        >

                                        <div
                                            v-for="(
                                                synonym, index
                                            ) in form.synonyms"
                                            class="flex flex-col"
                                        >
                                            <div
                                                class="flex w-full flex-row gap-1"
                                            >
                                                <div
                                                    class="flex w-full flex-col"
                                                >
                                                    <InputText
                                                        v-model="synonym.title"
                                                        class="w-full"
                                                        :invalid="
                                                            !!errors
                                                                ?.synonyms?.[
                                                                index
                                                            ]?.title
                                                        "
                                                        placeholder="Введите синоним"
                                                    />
                                                </div>
                                                <div class="flex items-end">
                                                    <Button
                                                        icon="pi pi-trash"
                                                        variant="text"
                                                        rounded
                                                        severity="danger"
                                                        @click="
                                                            deleteSynonym(index)
                                                        "
                                                    ></Button>
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <Message
                                                    v-if="
                                                        errors.synonyms &&
                                                        errors?.synonyms?.[
                                                            index
                                                        ]?.title
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        errors.synonyms?.[index]
                                                            ?.title
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>
                                </Fieldset>
                                <Message
                                    v-if="
                                        errors.synonyms &&
                                        form.synonyms.length === 0
                                    "
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.synonyms }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="short_description"
                                class="required-label font-semibold"
                                >Краткое описание парфюмера (для карточки
                                товара)</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Textarea
                                    v-else
                                    v-model="form.short_description"
                                    id="short_description"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите текст"
                                    :invalid="!!errors.short_description"
                                    :rows="5"
                                />
                                <Message
                                    v-if="errors.short_description"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.short_description }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label for="seo_title" class="font-semibold"
                                >Заголовок SEO-блока</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <InputText
                                    v-else
                                    v-model="form.seo_title"
                                    id="seo_title"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите заголовок"
                                    :invalid="!!errors.seo_title"
                                />
                                <Message
                                    v-if="errors.seo_title"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.seo_title }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label for="seo_description" class="font-semibold"
                                >Текст SEO-блока</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Editor
                                    v-else
                                    v-model="form.seo_description"
                                    id="seo_description"
                                    placeholder="Введите текст"
                                    editorStyle="height: 320px"
                                    :invalid="!!errors.seo_description"
                                />
                                <Message
                                    v-if="errors.seo_description"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.seo_description }}
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
