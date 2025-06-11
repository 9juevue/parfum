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
    aromaGroupStoreSchema,
    aromaGroupUpdateSchema,
} from "@/Validation/aromaGroupValidation.js";
import { resetObject } from "@/Composables/useResetObject.js";

import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import Dropzone from "@/Components/Dropzone.vue";
import { getCleanHTML } from "@/Composables/useQuillUtils.js";

const props = defineProps({
    aromaGroups: {
        type: Array,
        required: true,
        default: () => [],
    },
});
const { aromaGroups } = toRefs(props);
const aromaGroupsList = ref([...aromaGroups.value]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const aromaGroupModalVisible = ref(false);
const isLoading = ref(false);
const isEditing = ref(false);

const form = reactive({
    id: null,
    title: "",
    color: "",
    short_description: "",
    seo_title: "",
    seo_description: "",
    files: [],
});
const errors = reactive({
    title: null,
    color: null,
    short_description: null,
    seo_title: null,
    seo_description: null,
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
    aromaGroupModalVisible.value = false;
    isEditing.value = false;
}

async function submit() {
    resetObject(errors);

    const result = isEditing.value
        ? aromaGroupUpdateSchema.safeParse(form)
        : aromaGroupStoreSchema.safeParse(form);

    if (!result.success) {
        applyZodErrors(result, errors);
        return;
    }

    form.seo_description = getCleanHTML(form.seo_description);

    isEditing.value
        ? await updateAromaGroup(result.data)
        : await storeAromaGroup(result.data);
}

async function getAromaGroup(aromaGroupId) {
    try {
        const response = await axios.get(
            route("admin.aroma-groups.show", aromaGroupId),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function editAromaGroup(item) {
    isEditing.value = true;
    isLoading.value = true;
    aromaGroupModalVisible.value = true;
    resetForm();

    try {
        const aromaGroup = await getAromaGroup(item.id);

        if (!aromaGroup) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные группы ароматов",
                life: 3000,
            });
            return;
        }

        const copy = JSON.parse(JSON.stringify(aromaGroup));

        Object.keys(form).forEach((key) => {
            if (key in copy) {
                form[key] = copy[key];
            }
        });
        form.files = [
            {
                url: aromaGroup.image_url,
            },
        ];
    } catch (e) {
        console.log(e);
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные группы ароматов",
            life: 3000,
        });
    } finally {
        isLoading.value = false;
    }
}

function confirmDelete(aromaGroup) {
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
            deleteAromaGroup(aromaGroup);
        },
    });
}

async function storeAromaGroup(aromaGroup) {
    try {
        const response = await axios.post(
            route("admin.aroma-groups.store"),
            aromaGroup,
        );

        aromaGroupsList.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Группа ароматов сохранена",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function updateAromaGroup(aromaGroup) {
    try {
        const response = await axios.patch(
            route("admin.aroma-groups.update", aromaGroup),
            aromaGroup,
        );

        if (response.data) {
            const idx = aromaGroupsList.value.findIndex(
                (item) => item.id === response.data.id,
            );

            if (idx !== -1) {
                aromaGroupsList.value[idx] = response.data;
            }
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Группа ароматов обновлена",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function deleteAromaGroup(aromaGroup) {
    axios
        .delete(route("admin.aroma-groups.destroy", aromaGroup))
        .then((res) => {
            let idx = aromaGroupsList.value.findIndex(
                (item) => item.id === aromaGroup.id,
            );

            if (idx !== -1) {
                aromaGroupsList.value.splice(idx, 1);
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
                Группы ароматов
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <DataTable
                        :value="aromaGroupsList"
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
                                        >Группы ароматов</span
                                    >
                                    <Button
                                        icon="pi pi-plus"
                                        label="Добавить"
                                        @click="
                                            aromaGroupModalVisible = true;
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
                        <Column field="color" header="Цвет">
                            <template #body="slotProps">
                                <div
                                    class="h-8 w-8 rounded"
                                    :style="{
                                        backgroundColor: slotProps.data.color,
                                    }"
                                ></div>
                            </template>
                        </Column>
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
                                        @click="editAromaGroup(slotProps.data)"
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
                        v-model:visible="aromaGroupModalVisible"
                        modal
                        :header="
                            isEditing
                                ? 'Редактирование группы ароматов'
                                : 'Создание группы ароматов'
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
                            <label
                                for="image"
                                class="required-label font-semibold"
                                >Цвет</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="4rem"
                                ></Skeleton>
                                <ColorPicker
                                    v-model:pureColor="form.color"
                                    format="rgb"
                                    shape="circle"
                                    picker-type="chrome"
                                    disable-history
                                    disable-alpha
                                />
                                <Message
                                    v-if="errors.color"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.color }}
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
                            <label
                                for="short_description"
                                class="required-label font-semibold"
                                >Краткое описание группы ароматов</label
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
