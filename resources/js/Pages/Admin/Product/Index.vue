<script setup lang="js">
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FilterMatchMode } from "@primevue/core/api";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Button from "primevue/button";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputNumber from "primevue/inputnumber";
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
import Slider from "primevue/slider";
import PickList from "primevue/picklist";

import { ref, onMounted, reactive, toRefs, computed } from "vue";

import { resetObject } from "@/Composables/useResetObject.js";
import Dropzone from "@/Components/Dropzone.vue";
import {
    giftSetStoreSchema,
    parfumBoxStoreSchema,
    productStoreSchema,
} from "@/Validation/productValidation.js";
import {
    applyLaravelErrors,
    applyZodErrors,
} from "@/Composables/useErrorHelpers.js";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue";

import debounce from "lodash/debounce";

const props = defineProps({
    products: {
        type: Array,
        required: true,
        default: () => [],
    },
    productCategories: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    productSubtypeCategories: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    brands: {
        type: Array,
        required: true,
        default: () => [],
    },
    perfumers: {
        type: Array,
        required: true,
        default: () => [],
    },
    genders: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    aromaNotes: {
        type: Array,
        required: true,
        default: () => [],
    },
    aromaGroups: {
        type: Array,
        required: true,
        default: () => [],
    },
    aromaChords: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const {
    products,
    productCategories,
    productSubtypeCategories,
    brands,
    perfumers,
    genders,
    aromaNotes,
    aromaGroups,
    aromaChords,
} = toRefs(props);
const productsLocal = ref(JSON.parse(JSON.stringify(products.value)));
const productCategoriesLocal = ref(
    JSON.parse(JSON.stringify(productCategories.value)),
);
const productCategoriesArray = ref(
    Object.values(productSubtypeCategories.value),
);
const productSubtypeCategoriesLocal = ref(
    JSON.parse(JSON.stringify(productSubtypeCategories.value)),
);
const productSubtypeCategoriesArray = ref(
    Object.values(productSubtypeCategories.value),
);
const gendersArray = ref(Object.values(genders.value));
const brandsLocal = ref([...brands.value]);
const perfumersLocal = ref([...perfumers.value]);
const aromaNotesLocal = ref([...aromaNotes.value]);
const aromaGroupsLocal = ref([...aromaGroups.value]);
const aromaChordsLocal = ref([...aromaChords.value]);

const toast = useToast();
const confirm = useConfirm();

const currentProductCategory = ref(null);
const menu = ref();
const menuItems = ref([
    {
        label: "Продукт",
        command: () => {
            modalVisible.value = true;
            currentProductCategory.value =
                productCategoriesLocal.value.product.value;
            form.category_type = currentProductCategory.value;
        },
    },
    {
        label: "Подарочный набор",
        command: () => {
            modalVisible.value = true;
            currentProductCategory.value =
                productCategoriesLocal.value.giftset.value;
            form.category_type = currentProductCategory.value;
        },
    },
    {
        label: "Parfum Box",
        command: () => {
            modalVisible.value = true;
            currentProductCategory.value =
                productCategoriesLocal.value.parfumbox.value;
            form.category_type = currentProductCategory.value;
        },
    },
]);
const toggleMenu = (event) => {
    menu.value.toggle(event);
};

const form = reactive({
    slug: "",
    title: "",
    sku: "",
    tag: "",
    description: "",
    brand_id: "",
    year: "",
    gender_type: "",
    category_type: "",
    subtype_category_type: "",
    video: "",
    longevity: "",
    sillage: "",
    season: "",
    time_of_day: "",
    gender: "",
    synonyms: [],
    perfumers: [],
    aroma_groups: [],
    aroma_chords: [],
    base_notes: [],
    middle_notes: [],
    top_notes: [],
    parfum_box_subtypes: [],
    files: [],
});
const errors = reactive({
    slug: null,
    title: null,
    sku: null,
    tag: null,
    description: null,
    brand_id: null,
    year: null,
    gender_type: null,
    subtype_category_type: null,
    video: null,
    longevity: null,
    sillage: null,
    season: null,
    time_of_day: null,
    gender: null,
    synonyms: null,
    perfumers: null,
    aroma_groups: null,
    aroma_chords: null,
    base_notes: null,
    middle_notes: null,
    top_notes: null,
    parfum_box_subtypes: null,
    files: null,
});

const modalVisible = ref(false);

const groupedNotes = computed(() => {
    const notes = aromaNotesLocal.value;

    if (!Array.isArray(notes) || notes.length === 0) {
        return [];
    }

    const temp = {};

    notes.forEach((note) => {
        const groupTitle = note.note_grouping?.title || "Без группы";

        if (!temp[groupTitle]) {
            temp[groupTitle] = [];
        }

        temp[groupTitle].push({
            id: note.id,
            title_ru: note.title_ru,
            color: note.color,
        });
    });

    return Object.entries(temp).map(([label, items]) => ({
        label,
        items,
    }));
});

const currentYear = new Date().getFullYear();

const filters = ref({
    category_type: {
        value: null,
        matchMode: FilterMatchMode.IN,
    },

    brand_id: {
        value: null,
        matchMode: FilterMatchMode.STARTS_WITH,
    },
});

const lazyParams = reactive({
    page: 1,
    rows: 5,
    total: 4,
    currentPage: 1,
    search: null,
    category_type: null,
    brand_id: null,
    sortField: null,
    sortOrder: null,
});

const onSearch = debounce(() => {
    lazyParams.page = 1;
    loadProducts();
}, 300);

function onPage(event) {
    lazyParams.page = event.page + 1;
    lazyParams.rows = event.rows;
    loadProducts();
}

function onSort(event) {
    lazyParams.sortField = event.sortField;
    lazyParams.sortOrder = event.sortOrder;
    lazyParams.page = 1;
    loadProducts();
}

function onLazyLoad() {
    loadProducts();
}

async function loadProducts() {
    const params = {
        page: lazyParams.page,
        rows: lazyParams.rows,
        search: lazyParams.search,
        category_type: lazyParams.category_type,
        brand_id: lazyParams.brand_id,
        sortField: lazyParams.sortField,
        sortOrder: lazyParams.sortOrder,
    };

    const { data } = await axios.get(route("admin.products.filter"), {
        params,
    });

    productsLocal.value = data.data;
    lazyParams.total = data.meta.total;
    lazyParams.currentPage = data.meta.current_page;
}

function resetForm() {
    resetObject(form);
    resetObject(errors);
}

function onCancel() {
    resetForm();
    modalVisible.value = false;
}

function confirmDelete(product) {
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
            deleteProduct(product);
        },
    });
}

async function submit() {
    let result;

    resetObject(errors);

    switch (currentProductCategory.value) {
        case productCategoriesLocal.value.parfumbox.value:
            result = parfumBoxStoreSchema.safeParse(form);
            break;
        case productCategoriesLocal.value.giftset.value:
            result = giftSetStoreSchema.safeParse(form);
            break;
        default:
            result = productStoreSchema.safeParse(form);
            break;
    }

    if (!result.success) {
        applyZodErrors(result, errors);
        return;
    }

    await storeProduct(result.data);
}

function addSynonym() {
    form.synonyms.push({
        title: "",
    });
}

function deleteSynonym(index) {
    form.synonyms.splice(index, 1);
}

async function storeProduct(product) {
    try {
        const response = await axios.post(
            route("admin.products.store"),
            product,
        );

        productsLocal.value.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Продукт сохранен",
            life: 3000,
        });
        onCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, errors);
    }
}

async function deleteProduct(product) {
    axios
        .delete(route("admin.products.destroy", product))
        .then((res) => {
            let idx = productsLocal.value.findIndex(
                (item) => item.id === product.id,
            );

            if (idx !== -1) {
                productsLocal.value.splice(idx, 1);
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

    onMounted(() => {});
}

onMounted(() => {});
</script>

<template>
    <Head title="Каталог" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Каталог
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <DataTable
                        v-model:filters="filters"
                        :value="productsLocal"
                        table-style="min-width: 50rem"
                        filter-display="menu"
                        :lazy="true"
                        :rowsPerPageOptions="[5, 10, 15, 20]"
                        paginator
                        @lazy-load="onLazyLoad"
                        @page="onPage"
                        @sort="onSort"
                        :rows="lazyParams.rows"
                        :total-records="lazyParams.total"
                        :first="(lazyParams.currentPage - 1) * lazyParams.rows"
                        :sort-field="lazyParams.sortField"
                        :sort-order="lazyParams.sortOrder"
                        :removable-sort="true"
                    >
                        <template #header>
                            <div
                                class="col mb-4 flex items-center justify-between"
                            >
                                <div
                                    class="flex w-full flex-wrap items-center justify-between gap-2"
                                >
                                    <span class="text-xl font-bold"
                                        >Товары</span
                                    >
                                    <Button
                                        icon="pi pi-plus"
                                        label="Добавить"
                                        @click="toggleMenu"
                                    />
                                </div>
                            </div>
                            <div class="flex">
                                <IconField class="w-full">
                                    <InputText
                                        v-model="lazyParams.search"
                                        placeholder="Поиск"
                                        class="w-full"
                                        @input="onSearch"
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
                                        :src="slotProps.data.favorite_image_url"
                                        :alt="slotProps.data.title"
                                        class="max-h-full max-w-full object-contain"
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column
                            field="category_type_title"
                            header="Категория"
                            filter
                            filterField="category_type"
                            :showFilterMatchModes="false"
                        >
                            <template #body="{ data }">
                                <div class="flex items-center gap-2">
                                    <span>{{ data.category_type_title }}</span>
                                </div>
                            </template>
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="lazyParams.category_type"
                                    :options="productCategoriesArray"
                                    optionLabel="title"
                                    optionValue="value"
                                    placeholder="Выберите категорию"
                                    :show-toggle-all="false"
                                >
                                    <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{
                                                slotProps.option.title
                                            }}</span>
                                        </div>
                                    </template>
                                </MultiSelect>
                            </template>

                            <template #filterapply="{ filterCallback }">
                                <Button
                                    type="button"
                                    label="Применить"
                                    icon="pi pi-check"
                                    class="p-button-success p-button-sm ml-2"
                                    @click="onLazyLoad"
                                />
                            </template>

                            <template #filterclear="{ filterCallback }">
                                <Button
                                    type="button"
                                    label="Очистить"
                                    icon="pi pi-times"
                                    class="p-button-secondary p-button-sm"
                                    @click="
                                        lazyParams.category_type = null;
                                        onLazyLoad();
                                        filterCallback();
                                    "
                                />
                            </template>
                        </Column>
                        <Column
                            field="brands"
                            header="Бренд"
                            filter
                            filterField="brand_id"
                            :showFilterMatchModes="false"
                        >
                            <template #body="{ data }">
                                <div class="flex items-center gap-2">
                                    <span>{{ data.brands }}</span>
                                </div>
                            </template>
                            <template #filter="{ filterModel }">
                                <MultiSelect
                                    v-model="lazyParams.brand_id"
                                    :options="brandsLocal"
                                    optionLabel="title"
                                    optionValue="id"
                                    placeholder="Выберите бренд"
                                    :show-toggle-all="false"
                                >
                                    <template #option="slotProps">
                                        <div class="flex items-center gap-2">
                                            <span>{{
                                                slotProps.option.title
                                            }}</span>
                                        </div>
                                    </template>
                                </MultiSelect>
                            </template>

                            <template #filterapply="{ filterCallback }">
                                <Button
                                    type="button"
                                    label="Применить"
                                    icon="pi pi-check"
                                    class="p-button-success p-button-sm ml-2"
                                    @click="onLazyLoad"
                                />
                            </template>

                            <template #filterclear="{ filterCallback }">
                                <Button
                                    type="button"
                                    label="Очистить"
                                    icon="pi pi-times"
                                    class="p-button-secondary p-button-sm"
                                    @click="
                                        lazyParams.brand_id = null;
                                        onLazyLoad();
                                        filterCallback();
                                    "
                                />
                            </template>
                        </Column>
                        <Column field="is_active" header="Статус" sortable>
                            <template #body="slotProps">
                                <Tag
                                    v-if="slotProps.data.is_active"
                                    value="Активный"
                                    severity="success"
                                />
                                <Tag
                                    v-else
                                    value="Не активный"
                                    severity="danger"
                                />
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
                                        :href="
                                            route(
                                                'admin.products.edit',
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
                        v-model:visible="modalVisible"
                        modal
                        header="Создание товара"
                        style="width: 90rem"
                        @hide="onCancel"
                    >
                        <div class="flex w-full space-x-8">
                            <div class="mb-8 flex w-full flex-col gap-1">
                                <label
                                    for="title"
                                    class="required-label font-semibold"
                                    >Название</label
                                >
                                <div class="flex w-full flex-col">
                                    <InputText
                                        v-model="form.title"
                                        id="title"
                                        class="flex-auto"
                                        autocomplete="off"
                                        placeholder="Введите название"
                                        :maxlength="78"
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
                            <div class="mb-8 flex w-full flex-col gap-1">
                                <label for="slug" class="font-semibold"
                                    >Уникальный пользовательский URL</label
                                >
                                <div class="flex w-full flex-col">
                                    <InputText
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
                        </div>

                        <div class="flex w-full space-x-8">
                            <div class="mb-8 flex w-full flex-col gap-1">
                                <label
                                    for="sku"
                                    class="required-label font-semibold"
                                    >Артикул</label
                                >
                                <div class="flex w-full flex-col">
                                    <InputText
                                        v-model="form.sku"
                                        id="sku"
                                        class="flex-auto"
                                        autocomplete="off"
                                        placeholder="Введите артикул"
                                        :maxlength="25"
                                        :invalid="!!errors.sku"
                                    />
                                    <Message
                                        v-if="errors.sku"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.sku }}
                                    </Message>
                                </div>
                            </div>
                            <div class="mb-8 flex w-full flex-col gap-1">
                                <label for="tag" class="font-semibold"
                                    >Тег</label
                                >
                                <div class="flex w-full flex-col">
                                    <InputText
                                        v-model="form.tag"
                                        id="tag"
                                        class="flex-auto"
                                        autocomplete="off"
                                        placeholder="Введите тег"
                                        :maxlength="25"
                                        :invalid="!!errors.tag"
                                    />
                                    <Message
                                        v-if="errors.tag"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.tag }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="subtype_category_type"
                                    class="required-label font-semibold"
                                    >Категория типа подвидов</label
                                >
                                <div class="flex w-full flex-col">
                                    <Select
                                        v-model="form.subtype_category_type"
                                        id="subtype_category_type"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="
                                            !!errors.subtype_category_type
                                        "
                                        :options="productSubtypeCategoriesArray"
                                        option-label="title"
                                        option-value="value"
                                        placeholder="Выберите категорию"
                                        empty-message="Выберите категорию"
                                    />
                                    <Message
                                        v-if="errors.subtype_category_type"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.subtype_category_type }}
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                currentProductCategory ===
                                productCategoriesLocal.parfumbox.value
                            "
                            class="mb-8 flex w-full flex-col gap-1"
                        >
                            <label
                                for="parfum_box_subtypes"
                                class="required-label font-semibold"
                                >Связанные подвиды отливантов</label
                            >
                            <div class="flex w-full flex-col">
                                <PickList
                                    v-model="form.parfum_box_subtypes"
                                    data-key="id"
                                >
                                </PickList>
                                <Message
                                    v-if="errors.parfum_box_subtypes"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.parfum_box_subtypes }}
                                </Message>
                            </div>
                        </div>

                        <div class="flex w-full space-x-8">
                            <div
                                v-if="
                                    currentProductCategory ===
                                        productCategoriesLocal.product.value ||
                                    currentProductCategory ===
                                        productCategoriesLocal.giftset.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="brand_id"
                                    class="required-label font-semibold"
                                    >Бренд-производитель</label
                                >
                                <div class="flex w-full flex-col">
                                    <Select
                                        v-model="form.brand_id"
                                        id="brand_id"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.brand_id"
                                        :options="brandsLocal"
                                        option-label="title"
                                        option-value="id"
                                        placeholder="Выберите бренд"
                                        empty-message="Выберите бренд"
                                    />
                                    <Message
                                        v-if="errors.brand_id"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.brand_id }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="year"
                                    class="required-label font-semibold"
                                    >Год создания</label
                                >
                                <div class="flex w-full flex-col">
                                    <InputNumber
                                        v-model="form.year"
                                        id="year"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.year"
                                        :min="1900"
                                        :max="currentYear"
                                        placeholder="Введите год создания"
                                    />
                                    <Message
                                        v-if="errors.year"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.year }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="gender_type"
                                    class="required-label font-semibold"
                                    >Гендер</label
                                >
                                <div class="flex w-full flex-col">
                                    <Select
                                        v-model="form.gender_type"
                                        id="gender_type"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.gender_type"
                                        :options="gendersArray"
                                        option-label="title"
                                        option-value="value"
                                        placeholder="Выберите бренд"
                                        empty-message="Выберите бренд"
                                    />
                                    <Message
                                        v-if="errors.gender_type"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.gender_type }}
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full space-x-8">
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="perfumers"
                                    class="required-label font-semibold"
                                    >Парфюмеры</label
                                >
                                <div class="flex w-full flex-col">
                                    <MultiSelect
                                        v-model="form.perfumers"
                                        id="perfumers"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.perfumers"
                                        :options="perfumersLocal"
                                        option-label="title"
                                        option-value="id"
                                        placeholder="Выберите парфюмеров"
                                        empty-message="Выберите парфюмеров"
                                        :show-toggle-all="false"
                                    />
                                    <Message
                                        v-if="errors.perfumers"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.perfumers }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="aromaGroups"
                                    class="required-label font-semibold"
                                    >Группы аромата</label
                                >
                                <div class="flex w-full flex-col">
                                    <MultiSelect
                                        v-model="form.aroma_groups"
                                        id="aromaGroups"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.aroma_groups"
                                        :options="aromaGroupsLocal"
                                        option-label="title"
                                        filter
                                        option-value="id"
                                        placeholder="Выберите группы"
                                        empty-message="Выберите группы"
                                        :show-toggle-all="false"
                                    />
                                    <Message
                                        v-if="errors.aroma_groups"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.aroma_groups }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="aromaChords"
                                    class="required-label font-semibold"
                                    >Аккорды аромата</label
                                >
                                <div class="flex w-full flex-col">
                                    <MultiSelect
                                        v-model="form.aroma_chords"
                                        id="aromaChords"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.aroma_chords"
                                        :options="aromaChordsLocal"
                                        option-label="title"
                                        option-value="id"
                                        filter
                                        placeholder="Выберите аккорды"
                                        empty-message="Выберите аккорды"
                                        :show-toggle-all="false"
                                    />
                                    <Message
                                        v-if="errors.aroma_chords"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.aroma_chords }}
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full space-x-8">
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="topNotes"
                                    class="required-label font-semibold"
                                    >Верхние ноты аромата</label
                                >
                                <div class="flex w-full flex-col">
                                    <MultiSelect
                                        v-model="form.top_notes"
                                        id="topNotes"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.top_notes"
                                        :options="groupedNotes"
                                        optionLabel="title_ru"
                                        optionValue="id"
                                        filter
                                        optionGroupLabel="label"
                                        optionGroupChildren="items"
                                        placeholder="Выберите ноты"
                                        empty-message="Выберите ноты"
                                        :show-toggle-all="false"
                                    >
                                        <template #optiongroup="{ option }">
                                            <div
                                                class="flex items-center rounded-md bg-gray-100 px-2 py-1"
                                            >
                                                <span
                                                    class="font-semibold text-gray-700"
                                                    >{{ option.label }}</span
                                                >
                                            </div>
                                        </template>
                                    </MultiSelect>
                                    <Message
                                        v-if="errors.top_notes"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.top_notes }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="middleNotes"
                                    class="required-label font-semibold"
                                    >Средние ноты аромата</label
                                >
                                <div class="flex w-full flex-col">
                                    <MultiSelect
                                        v-model="form.middle_notes"
                                        id="middleNotes"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.middle_notes"
                                        :options="groupedNotes"
                                        optionLabel="title_ru"
                                        optionValue="id"
                                        filter
                                        optionGroupLabel="label"
                                        optionGroupChildren="items"
                                        placeholder="Выберите ноты"
                                        empty-message="Выберите ноты"
                                        :show-toggle-all="false"
                                    >
                                        <template #optiongroup="{ option }">
                                            <div
                                                class="flex items-center rounded-md bg-gray-100 px-2 py-1"
                                            >
                                                <span
                                                    class="font-semibold text-gray-700"
                                                    >{{ option.label }}</span
                                                >
                                            </div>
                                        </template>
                                    </MultiSelect>
                                    <Message
                                        v-if="errors.middle_notes"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.middle_notes }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="baseNotes"
                                    class="required-label font-semibold"
                                    >Базовые ноты аромата</label
                                >
                                <div class="flex w-full flex-col">
                                    <MultiSelect
                                        v-model="form.base_notes"
                                        id="baseNotes"
                                        class="flex-auto"
                                        autocomplete="off"
                                        :invalid="!!errors.base_notes"
                                        :options="groupedNotes"
                                        optionLabel="title_ru"
                                        optionValue="id"
                                        filter
                                        optionGroupLabel="label"
                                        optionGroupChildren="items"
                                        placeholder="Выберите ноты"
                                        empty-message="Выберите ноты"
                                        :show-toggle-all="false"
                                    >
                                        <template #optiongroup="{ option }">
                                            <div
                                                class="flex items-center rounded-md bg-gray-100 px-2 py-1"
                                            >
                                                <span
                                                    class="font-semibold text-gray-700"
                                                    >{{ option.label }}</span
                                                >
                                            </div>
                                        </template>
                                    </MultiSelect>
                                    <Message
                                        v-if="errors.base_notes"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.base_notes }}
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div class="flex w-full space-x-8">
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="longevity"
                                    class="required-label font-semibold"
                                    >Стойкость</label
                                >
                                <div class="flex w-full flex-col">
                                    <div
                                        class="mb-2 flex w-full flex-col space-y-6"
                                    >
                                        <InputText
                                            disabled
                                            :invalid="!!errors.longevity"
                                            v-model="form.longevity"
                                        />
                                        <Slider
                                            id="longevity"
                                            :invalid="!!errors.longevity"
                                            v-model="form.longevity"
                                            :min="1"
                                            :max="10"
                                        />
                                    </div>
                                    <Message
                                        v-if="errors.longevity"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.longevity }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="sillage"
                                    class="required-label font-semibold"
                                    >Шлейф</label
                                >
                                <div class="flex w-full flex-col">
                                    <div
                                        class="mb-2 flex w-full flex-col space-y-6"
                                    >
                                        <InputText
                                            disabled
                                            :invalid="!!errors.sillage"
                                            v-model="form.sillage"
                                        />
                                        <Slider
                                            id="sillage"
                                            v-model="form.sillage"
                                            :min="1"
                                            :max="10"
                                            :invalid="!!errors.sillage"
                                        />
                                    </div>
                                    <Message
                                        v-if="errors.sillage"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.sillage }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="season"
                                    class="required-label font-semibold"
                                    >Сезон</label
                                >
                                <div class="flex w-full flex-col">
                                    <div
                                        class="mb-2 flex w-full flex-col space-y-6"
                                    >
                                        <InputText
                                            disabled
                                            :invalid="!!errors.season"
                                            v-model="form.season"
                                        />
                                        <Slider
                                            id="season"
                                            :invalid="!!errors.season"
                                            v-model="form.season"
                                            :min="1"
                                            :max="10"
                                        />
                                    </div>
                                    <Message
                                        v-if="errors.season"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.season }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="time_of_day"
                                    class="required-label font-semibold"
                                    >Время дня</label
                                >
                                <div class="flex w-full flex-col">
                                    <div
                                        class="mb-2 flex w-full flex-col space-y-6"
                                    >
                                        <InputText
                                            disabled
                                            :invalid="!!errors.time_of_day"
                                            v-model="form.time_of_day"
                                        />
                                        <Slider
                                            id="time_of_day"
                                            :invalid="!!errors.time_of_day"
                                            v-model="form.time_of_day"
                                            :min="1"
                                            :max="10"
                                        />
                                    </div>
                                    <Message
                                        v-if="errors.time_of_day"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.time_of_day }}
                                    </Message>
                                </div>
                            </div>
                            <div
                                v-if="
                                    currentProductCategory ===
                                    productCategoriesLocal.product.value
                                "
                                class="mb-8 flex w-full flex-col gap-1"
                            >
                                <label
                                    for="gender"
                                    class="required-label font-semibold"
                                    >Гендер</label
                                >
                                <div class="flex w-full flex-col">
                                    <div
                                        class="mb-2 flex w-full flex-col space-y-6"
                                    >
                                        <InputText
                                            disabled
                                            :invalid="!!errors.gender"
                                            v-model="form.gender"
                                        />
                                        <Slider
                                            id="gender"
                                            :invalid="!!errors.gender"
                                            v-model="form.gender"
                                            :min="1"
                                            :max="10"
                                        />
                                    </div>
                                    <Message
                                        v-if="errors.gender"
                                        severity="error"
                                        size="small"
                                        variant="simple"
                                    >
                                        {{ errors.gender }}
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                currentProductCategory ===
                                productCategoriesLocal.product.value
                            "
                            class="mb-8 flex w-full flex-col gap-1"
                        >
                            <label for="video" class="font-semibold"
                                >Видео-обзор</label
                            >
                            <div class="flex w-full flex-col">
                                <Textarea
                                    v-model="form.video"
                                    id="video"
                                    class="flex-auto"
                                    autocomplete="off"
                                    placeholder="Введите iframe код"
                                    :invalid="!!errors.video"
                                    :rows="5"
                                />
                                <Message
                                    v-if="errors.video"
                                    severity="error"
                                    size="small"
                                    variant="simple"
                                >
                                    {{ errors.video }}
                                </Message>
                            </div>
                        </div>
                        <div class="mb-8 flex flex-col gap-1">
                            <label for="image" class="font-semibold"
                                >Синонимы названия для поиска</label
                            >
                            <div class="flex w-full flex-col">
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
                                for="description"
                                class="required-label font-semibold"
                                >Описание товара</label
                            >
                            <div class="flex w-full flex-col">
                                <Textarea
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
                                for="image"
                                class="required-label font-semibold"
                                >Изображение</label
                            >
                            <div class="flex w-full flex-col">
                                <Dropzone
                                    v-model:files="form.files"
                                    :max-files="15"
                                    :invalid="!!errors.files"
                                    :has-favorite-photo="true"
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
                    <Menu ref="menu" :popup="true" :model="menuItems" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
