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
import MultiSelect from "primevue/multiselect";
import Fieldset from "primevue/fieldset";
import Menu from "primevue/menu";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue";

import { FilterMatchMode } from "@primevue/core/api";

import {
    applyLaravelErrors,
    applyZodErrors,
} from "@/Composables/useErrorHelpers.js";
import {
    aromaNoteStoreSchema,
    aromaNoteUpdateSchema,
} from "@/Validation/aromaNoteValidation.js";
import { resetObject } from "@/Composables/useResetObject.js";

import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import Dropzone from "@/Components/Dropzone.vue";
import { getCleanHTML } from "@/Composables/useQuillUtils.js";
import {
    noteGroupingStoreSchema,
    noteGroupingUpdateSchema,
} from "@/Validation/noteGroupingValidation.js";

const props = defineProps({
    aromaNotes: {
        type: Array,
        required: true,
        default: () => [],
    },
    noteGroupings: {
        type: Array,
        required: true,
        default: () => [],
    },
});
const { aromaNotes } = toRefs(props);
const aromaNotesList = ref([...aromaNotes.value]);
const { noteGroupings } = toRefs(props);
const noteGroupingsList = ref([...noteGroupings.value]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    note_grouping_id: { value: null, matchMode: FilterMatchMode.IN },
});
const aromaNoteModalVisible = ref(false);
const noteGroupingModalVisible = ref(false);
const isLoading = ref(false);
const isEditing = ref(false);

const aromaNoteForm = reactive({
    id: null,
    title_ru: "",
    title_en: "",
    color: "",
    short_description: "",
    seo_description: "",
    note_grouping_id: "",
    files: [],
});
const aromaNoteErrors = reactive({
    title_ru: null,
    title_en: null,
    color: null,
    short_description: null,
    seo_description: null,
    note_grouping_id: null,
    files: null,
});
const noteGroupingForm = reactive({
    id: null,
    title: "",
});
const noteGroupingErrors = reactive({
    title: null,
});

const toast = useToast();
const confirm = useConfirm();

const tabValues = ref({
    aromaNote: "aromaNote",
    noteGrouping: "noteGrouping",
});
const activeTab = ref(tabValues.value.aromaNote);

const menu = ref();
const menuItems = ref([
    {
        label: "Ноты ароматов",
        command: () => {
            aromaNoteModalVisible.value = true;
        },
    },
    {
        label: "Группировка нот",
        command: () => {
            noteGroupingModalVisible.value = true;
        },
    },
]);

const toggle = (event) => {
    isEditing.value = false;
    menu.value.toggle(event);
};

function resetAromaNoteForm() {
    resetObject(aromaNoteErrors);
    resetObject(aromaNoteForm);
}

function resetNoteGroupingForm() {
    resetObject(noteGroupingErrors);
    resetObject(noteGroupingForm);
}

function onCancelAromaNoteForm() {
    resetAromaNoteForm();
    aromaNoteModalVisible.value = false;
    isEditing.value = false;
}

function onCancelNoteGroupingForm() {
    resetNoteGroupingForm();
    noteGroupingModalVisible.value = false;
    isEditing.value = false;
}

async function submitAromaNote() {
    resetObject(aromaNoteErrors);

    const result = isEditing.value
        ? aromaNoteUpdateSchema.safeParse(aromaNoteForm)
        : aromaNoteStoreSchema.safeParse(aromaNoteForm);

    if (!result.success) {
        applyZodErrors(result, aromaNoteErrors);
        return;
    }

    aromaNoteForm.seo_description = getCleanHTML(aromaNoteForm.seo_description);

    isEditing.value
        ? await updateAromaNote(result.data)
        : await storeAromaNote(result.data);
}

async function submitNoteGrouping() {
    resetObject(noteGroupingErrors);

    const result = isEditing.value
        ? noteGroupingUpdateSchema.safeParse(noteGroupingForm)
        : noteGroupingStoreSchema.safeParse(noteGroupingForm);

    if (!result.success) {
        applyZodErrors(result, noteGroupingErrors);
        return;
    }

    isEditing.value
        ? await updateNoteGrouping(result.data)
        : await storeNoteGrouping(result.data);
}

async function getAromaNote(aromaNoteId) {
    try {
        const response = await axios.get(
            route("admin.aroma-notes.show", aromaNoteId),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function getNoteGrouping(noteGroupingId) {
    try {
        const response = await axios.get(
            route("admin.note-groupings.show", noteGroupingId),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function editNoteGrouping(item) {
    isEditing.value = true;
    isLoading.value = true;
    noteGroupingModalVisible.value = true;
    resetAromaNoteForm();

    try {
        const noteGrouping = await getNoteGrouping(item.id);

        if (!noteGrouping) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные группировки нот",
                life: 3000,
            });
            return;
        }

        const copy = JSON.parse(JSON.stringify(noteGrouping));

        Object.keys(noteGroupingForm).forEach((key) => {
            if (key in copy) {
                noteGroupingForm[key] = copy[key];
            }
        });
    } catch (e) {
        console.log(e);
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные ноты ароматов",
            life: 3000,
        });
    } finally {
        isLoading.value = false;
    }
}

async function editAromaNote(item) {
    isEditing.value = true;
    isLoading.value = true;
    aromaNoteModalVisible.value = true;
    resetNoteGroupingForm();

    try {
        const aromaNote = await getAromaNote(item.id);

        if (!aromaNote) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные ноты ароматов",
                life: 3000,
            });
            return;
        }

        const copy = JSON.parse(JSON.stringify(aromaNote));

        Object.keys(aromaNoteForm).forEach((key) => {
            if (key in copy) {
                aromaNoteForm[key] = copy[key];
            }
        });
        aromaNoteForm.files = [
            {
                url: aromaNote.image_url,
            },
        ];
    } catch (e) {
        console.log(e);
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные ноты ароматов",
            life: 3000,
        });
    } finally {
        isLoading.value = false;
    }
}

function confirmAromaNoteDelete(aromaNote) {
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
            deleteAromaNote(aromaNote);
        },
    });
}

function confirmNoteGroupingDelete(noteGrouping) {
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
            deleteNoteGrouping(noteGrouping);
        },
    });
}

async function storeAromaNote(aromaNote) {
    try {
        const response = await axios.post(
            route("admin.aroma-notes.store"),
            aromaNote,
        );

        aromaNotesList.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Ноты ароматов сохранена",
            life: 3000,
        });
        onCancelAromaNoteForm();
        activeTab.value = tabValues.value.aromaNote;
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, aromaNoteErrors);
    }
}

async function storeNoteGrouping(noteGrouping) {
    try {
        const response = await axios.post(
            route("admin.note-groupings.store"),
            noteGrouping,
        );

        noteGroupingsList.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Группировка нот сохранена",
            life: 3000,
        });
        onCancelNoteGroupingForm();
        activeTab.value = tabValues.value.noteGrouping;
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, noteGroupingErrors);
    }
}

async function updateAromaNote(aromaNote) {
    try {
        const response = await axios.patch(
            route("admin.aroma-notes.update", aromaNote),
            aromaNote,
        );

        if (response.data) {
            const idx = aromaNotesList.value.findIndex(
                (item) => item.id === response.data.id,
            );

            if (idx !== -1) {
                aromaNotesList.value[idx] = response.data;
            }
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Ноты ароматов обновлены",
            life: 3000,
        });
        onCancelAromaNoteForm();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, aromaNoteErrors);
    }
}

async function updateNoteGrouping(noteGrouping) {
    try {
        const response = await axios.patch(
            route("admin.note-groupings.update", noteGrouping),
            noteGrouping,
        );

        if (response.data) {
            const idx = noteGroupingsList.value.findIndex(
                (item) => item.id === response.data.id,
            );

            if (idx !== -1) {
                noteGroupingsList.value[idx] = response.data;
            }

            aromaNotesList.value.forEach((aroma) => {
                if (aroma.note_grouping.id === response.data.id) {
                    aroma.note_grouping = { ...response.data };
                }
            });
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Группировка нот обновлена",
            life: 3000,
        });
        onCancelNoteGroupingForm();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, noteGroupingErrors);
    }
}

async function deleteAromaNote(aromaNote) {
    axios
        .delete(route("admin.aroma-notes.destroy", aromaNote))
        .then((res) => {
            let idx = aromaNotesList.value.findIndex(
                (item) => item.id === aromaNote.id,
            );

            if (idx !== -1) {
                aromaNotesList.value.splice(idx, 1);
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

async function deleteNoteGrouping(noteGrouping) {
    axios
        .delete(route("admin.note-groupings.destroy", noteGrouping))
        .then((res) => {
            let idx = noteGroupingsList.value.findIndex(
                (item) => item.id === noteGrouping.id,
            );

            if (idx !== -1) {
                noteGroupingsList.value.splice(idx, 1);
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
                Ноты ароматов
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <Tabs v-model:value="activeTab">
                        <TabList>
                            <Tab :value="tabValues.aromaNote"
                                >Ноты ароматов</Tab
                            >
                            <Tab :value="tabValues.noteGrouping"
                                >Группировка нот</Tab
                            >
                        </TabList>
                        <TabPanels>
                            <TabPanel :value="tabValues.aromaNote">
                                <DataTable
                                    :value="aromaNotesList"
                                    paginator
                                    :rows="5"
                                    :rowsPerPageOptions="[5, 10, 20, 50]"
                                    tableStyle="min-width: 50rem"
                                    v-model:filters="filters"
                                    filterDisplay="menu"
                                    :global-filter-fields="[
                                        'title_ru',
                                        'title_en',
                                        'noteGrouping.title',
                                    ]"
                                >
                                    <template #header>
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <IconField class="w-full">
                                                <InputText
                                                    v-model="
                                                        filters['global'].value
                                                    "
                                                    placeholder="Поиск"
                                                    class="w-full"
                                                />
                                                <InputIcon
                                                    class="pi pi-search"
                                                />
                                            </IconField>
                                            <Button
                                                class="ml-10 w-max"
                                                icon="pi pi-plus"
                                                label="Добавить"
                                                @click="toggle"
                                            />
                                        </div>
                                    </template>
                                    <Column
                                        field="title_ru"
                                        header="Название"
                                        sortable
                                    >
                                        <template #body="slotProps">
                                            <div class="flex flex-col">
                                                <strong class="">
                                                    {{
                                                        slotProps.data.title_ru
                                                    }}
                                                </strong>
                                                <span class="">
                                                    {{
                                                        slotProps.data.title_en
                                                    }}
                                                </span>
                                            </div>
                                        </template>
                                    </Column>
                                    <Column
                                        field="note_grouping_id"
                                        header="Группа"
                                        sortable
                                        filter
                                        filterField="note_grouping_id"
                                        :showFilterMatchModes="false"
                                    >
                                        <template #body="slotProps">
                                            {{
                                                slotProps.data.note_grouping
                                                    .title
                                            }}
                                        </template>
                                        <template #filter="{ filterModel }">
                                            <MultiSelect
                                                :show-toggle-all="false"
                                                v-model="filterModel.value"
                                                :options="noteGroupingsList"
                                                optionLabel="title"
                                                optionValue="id"
                                                placeholder="Выберите группу"
                                                empty-message="Выберите группу"
                                            >
                                                <template #option="slotProps">
                                                    <div
                                                        class="flex items-center gap-2"
                                                    >
                                                        <span>{{
                                                            slotProps.option
                                                                .title
                                                        }}</span>
                                                    </div>
                                                </template>
                                            </MultiSelect>
                                        </template>

                                        <template
                                            #filterapply="{ filterCallback }"
                                        >
                                            <Button
                                                type="button"
                                                label="Применить"
                                                icon="pi pi-check"
                                                class="p-button-sm ml-2"
                                                @click="filterCallback"
                                            />
                                        </template>
                                        <template
                                            #filterclear="{ filterCallback }"
                                        >
                                            <Button
                                                type="button"
                                                label="Очистить"
                                                icon="pi pi-times"
                                                class="p-button-secondary p-button-sm"
                                                @click="filterCallback"
                                            />
                                        </template>
                                    </Column>
                                    <Column field="color" header="Цвет">
                                        <template #body="slotProps">
                                            <div
                                                class="h-8 w-8 rounded"
                                                :style="{
                                                    backgroundColor:
                                                        slotProps.data.color,
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
                                                    :src="
                                                        slotProps.data.image_url
                                                    "
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
                                                        editAromaNote(
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
                                                    @click="
                                                        confirmAromaNoteDelete(
                                                            slotProps.data,
                                                        )
                                                    "
                                                />
                                            </div>
                                        </template>
                                    </Column>
                                </DataTable>
                            </TabPanel>
                            <TabPanel :value="tabValues.noteGrouping">
                                <DataTable
                                    :value="noteGroupingsList"
                                    paginator
                                    :rows="5"
                                    :rowsPerPageOptions="[5, 10, 20, 50]"
                                    tableStyle="min-width: 50rem"
                                    v-model:filters="filters"
                                    filterDisplay="menu"
                                    :global-filter-fields="['title']"
                                >
                                    <template #header>
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <IconField class="w-full">
                                                <InputText
                                                    v-model="
                                                        filters['global'].value
                                                    "
                                                    placeholder="Поиск"
                                                    class="w-full"
                                                />
                                                <InputIcon
                                                    class="pi pi-search"
                                                />
                                            </IconField>
                                            <Button
                                                class="ml-10 w-max"
                                                icon="pi pi-plus"
                                                label="Добавить"
                                                @click="toggle"
                                            />
                                        </div>
                                    </template>
                                    <Column
                                        field="title"
                                        header="Название"
                                        sortable
                                    ></Column>

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
                                                        editNoteGrouping(
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
                                                    @click="
                                                        confirmNoteGroupingDelete(
                                                            slotProps.data,
                                                        )
                                                    "
                                                />
                                            </div>
                                        </template>
                                    </Column>
                                </DataTable>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                    <Dialog
                        v-model:visible="aromaNoteModalVisible"
                        modal
                        :header="
                            isEditing
                                ? 'Редактирование ноты ароматов'
                                : 'Создание ноты ароматов'
                        "
                        style="width: 70rem"
                        @hide="onCancelAromaNoteForm"
                    >
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="title_ru"
                                class="required-label font-semibold"
                                >Название на русском</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <InputText
                                    v-else
                                    v-model="aromaNoteForm.title_ru"
                                    id="title"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите название"
                                    :invalid="!!aromaNoteErrors.title_ru"
                                />
                                <Message
                                    v-if="aromaNoteErrors.title_ru"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.title_ru }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="title_en"
                                class="required-label font-semibold"
                                >Название на английском</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <InputText
                                    v-else
                                    v-model="aromaNoteForm.title_en"
                                    id="title"
                                    class="flex-auto"
                                    autocomplete="off"
                                    maxlength="25"
                                    placeholder="Введите название"
                                    :invalid="!!aromaNoteErrors.title_en"
                                />
                                <Message
                                    v-if="aromaNoteErrors.title_en"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.title_en }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="note_grouping_id"
                                class="required-label font-semibold"
                                >Группировка нот</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Select
                                    v-else
                                    v-model="aromaNoteForm.note_grouping_id"
                                    id="country_id"
                                    class="flex-auto"
                                    autocomplete="off"
                                    :invalid="
                                        !!aromaNoteErrors.note_grouping_id
                                    "
                                    :options="noteGroupingsList"
                                    option-label="title"
                                    option-value="id"
                                    placeholder="Выберите группировку"
                                    empty-message="Выберите группировку"
                                />
                                <Message
                                    v-if="aromaNoteErrors.note_grouping_id"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.note_grouping_id }}
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
                                    v-model:pureColor="aromaNoteForm.color"
                                    format="rgb"
                                    shape="circle"
                                    picker-type="chrome"
                                    disable-history
                                    disable-alpha
                                />
                                <Message
                                    v-if="aromaNoteErrors.color"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.color }}
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
                                    v-model:files="aromaNoteForm.files"
                                    :max-files="1"
                                    :invalid="!!aromaNoteErrors.files"
                                />
                                <Message
                                    v-if="aromaNoteErrors.files"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.files }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label
                                for="short_description"
                                class="required-label font-semibold"
                                >Краткое описание ноты ароматов</label
                            >
                            <div class="flex w-full flex-col">
                                <Skeleton
                                    v-if="isLoading"
                                    height="2rem"
                                ></Skeleton>
                                <Textarea
                                    v-else
                                    v-model="aromaNoteForm.short_description"
                                    id="short_description"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите текст"
                                    :invalid="
                                        !!aromaNoteErrors.short_description
                                    "
                                    :rows="5"
                                />
                                <Message
                                    v-if="aromaNoteErrors.short_description"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.short_description }}
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
                                    v-model="aromaNoteForm.seo_description"
                                    id="seo_description"
                                    placeholder="Введите текст"
                                    editorStyle="height: 320px"
                                    :invalid="!!aromaNoteErrors.seo_description"
                                />
                                <Message
                                    v-if="aromaNoteErrors.seo_description"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ aromaNoteErrors.seo_description }}
                                </Message>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <Button
                                type="button"
                                label="Отменить"
                                severity="secondary"
                                @click="onCancelAromaNoteForm"
                            ></Button>
                            <Button
                                type="button"
                                label="Сохранить"
                                @click="submitAromaNote"
                            ></Button>
                        </div>
                    </Dialog>
                    <Dialog
                        v-model:visible="noteGroupingModalVisible"
                        modal
                        :header="
                            isEditing
                                ? 'Редактирование группировки нот'
                                : 'Создание группировки нот'
                        "
                        style="width: 70rem"
                        @hide="onCancelNoteGroupingForm"
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
                                    v-model="noteGroupingForm.title"
                                    id="title"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите название"
                                    :invalid="!!noteGroupingErrors.title"
                                />
                                <Message
                                    v-if="noteGroupingErrors.title"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ noteGroupingErrors.title }}
                                </Message>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <Button
                                type="button"
                                label="Отменить"
                                severity="secondary"
                                @click="onCancelNoteGroupingForm"
                            ></Button>
                            <Button
                                type="button"
                                label="Сохранить"
                                @click="submitNoteGrouping"
                            ></Button>
                        </div>
                    </Dialog>
                    <Menu ref="menu" :popup="true" :model="menuItems" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
