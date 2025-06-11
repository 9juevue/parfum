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
import Breadcrumb from "primevue/breadcrumb";

import { ref, onMounted, reactive, toRefs, computed, onBeforeMount } from "vue";

import { resetObject } from "@/Composables/useResetObject.js";
import Dropzone from "@/Components/Dropzone.vue";
import {
    giftSetStoreSchema,
    giftSetUpdateSchema,
    parfumBoxStoreSchema,
    parfumBoxUpdateSchema,
    productStoreSchema,
    productUpdateSchema,
} from "@/Validation/productValidation.js";
import {
    applyLaravelErrors,
    applyZodErrors,
} from "@/Composables/useErrorHelpers.js";
import axios from "axios";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue";

import { Inertia } from "@inertiajs/inertia";
import {
    brandStoreSchema,
    brandUpdateSchema,
} from "@/Validation/brandValidation.js";
import { getCleanHTML } from "@/Composables/useQuillUtils.js";
import {
    productSubtypeStoreSchema,
    productSubtypeUpdateSchema,
} from "@/Validation/productSubtypeValidation.js";

const props = defineProps({
    product: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    breadcrumbs: {
        type: Array,
        required: true,
        default: () => [],
    },
    productSubtypeTypes: {
        type: Object,
        required: true,
        default: () => ({}),
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
    productSubtypeFormatTypes: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    productSubtypeFormats: {
        type: Array,
        required: true,
        default: () => [],
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
    product,
    productCategories,
    productSubtypeTypes,
    productSubtypeCategories,
    productSubtypeFormats,
    productSubtypeFormatTypes,
    brands,
    perfumers,
    genders,
    aromaNotes,
    aromaGroups,
    aromaChords,
    breadcrumbs,
} = toRefs(props);
const productLocal = ref(JSON.parse(JSON.stringify(product.value)));
const productCategoriesLocal = ref(
    JSON.parse(JSON.stringify(productCategories.value)),
);
const productCategoriesArray = ref(Object.values(productSubtypeTypes.value));
const productSubtypeFormatsLocal = ref(
    JSON.parse(JSON.stringify(productSubtypeFormats.value)),
);
const productSubtypeFormatsObject = ref(
    JSON.parse(JSON.stringify(productSubtypeFormatTypes.value)),
);
const productSubtypeTypesLocal = ref(
    JSON.parse(JSON.stringify(productCategories.value)),
);
const productSubtypeTypesArray = ref(Object.values(productSubtypeTypes.value));
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

const remainderItem = ref(null);
const setItem = ref(null);

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

const toast = useToast();
const confirm = useConfirm();

const subtypeModalVisible = ref(false);
const subtypeEditing = ref(false);
const subtypeLoading = ref(false);

const productForm = reactive({
    id: null,
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
const productErrors = reactive({
    slug: null,
    title: null,
    sku: null,
    tag: null,
    description: null,
    brand_id: null,
    year: null,
    gender_type: null,
    video: null,
    longevity: null,
    sillage: null,
    season: null,
    time_of_day: null,
    subtype_category_type: null,
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
const subtypeForm = reactive({
    id: null,
    product_id: null,
    product_subtype_type: null,
    price: null,
    qty: null,
    popularity: null,
    product_subtype_format_id: null,
    volume: null,
    bottle_volume: null,
    volume_text: null,
});
const subtypeErrors = reactive({
    product_id: null,
    product_subtype_type: null,
    price: null,
    qty: null,
    popularity: null,
    product_subtype_format_id: null,
    volume: null,
    bottle_volume: null,
    volume_text: null,
});

const currentYear = new Date().getFullYear();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

onBeforeMount(() => {
    Object.assign(productForm, productLocal.value);
    productForm.files = productForm.images.map((src) => ({ url: src }));
    productForm.perfumers = productForm.perfumers.map((p) => p.id);
    productForm.aroma_chords = productForm.aroma_chords.map((p) => p.id);
    productForm.aroma_groups = productForm.aroma_groups.map((p) => p.id);
    productForm.base_notes = productForm.base_notes.map((p) => p.id);
    productForm.middle_notes = productForm.middle_notes.map((p) => p.id);
    productForm.top_notes = productForm.top_notes.map((p) => p.id);

    const remainderValue = productSubtypeFormatsObject.value.remainder?.value;

    remainderItem.value =
        productSubtypeFormats.value.find(
            (item) => item.product_subtype_format === remainderValue,
        ) || null;

    const setValue = productSubtypeFormatsObject.value.set?.value;

    setItem.value =
        productSubtypeFormats.value.find(
            (item) => item.product_subtype_format === setValue,
        ) || null;
});

function addSynonym() {
    productForm.synonyms.push({
        title: "",
    });
}

function deleteSynonym(index) {
    productForm.synonyms.splice(index, 1);
}

function confirmDeleteProduct() {
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
            deleteProduct(productLocal.value);
        },
    });
}

async function submitProduct() {
    let result;

    resetObject(productErrors);

    switch (productLocal.value.category_type) {
        case productCategoriesLocal.value.parfumbox.value:
            result = parfumBoxUpdateSchema.safeParse(productForm);
            break;
        case productCategoriesLocal.value.giftset.value:
            result = giftSetUpdateSchema.safeParse(productForm);
            break;
        default:
            result = productUpdateSchema.safeParse(productForm);
            break;
    }

    if (!result.success) {
        applyZodErrors(result, productErrors);
        return;
    }

    await updateProduct(result.data);
}

async function updateProduct(product) {
    try {
        const response = await axios.patch(
            route("admin.products.update", product),
            product,
        );

        productLocal.value = JSON.parse(JSON.stringify(response.data));

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Продукт сохранен",
            life: 3000,
        });
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, productErrors);
    }
}

async function deleteProduct(product) {
    axios
        .delete(route("admin.products.destroy", product))
        .then((res) => {
            Inertia.visit(route("admin.products.index"));

            toast.add({
                severity: "success",
                summary: "Информация",
                detail: "Запись успешно удалена!",
                life: 3000,
            });
        })
        .catch((error) => {
            console.log(error);
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось удалить запись!",
                life: 3000,
            });
        });
}

function resetSubtypeForm() {
    resetObject(subtypeErrors);
    resetObject(subtypeForm);
}

function onSubtypeCancel() {
    resetSubtypeForm();
    subtypeModalVisible.value = false;
    subtypeEditing.value = false;
}

async function getProductSubtype(id) {
    try {
        const response = await axios.get(
            route("admin.product-subtypes.show", id),
        );
        return response.data;
    } catch (error) {
        return null;
    }
}

async function editProductSubtype(item) {
    subtypeEditing.value = true;
    subtypeLoading.value = true;
    subtypeModalVisible.value = true;
    resetSubtypeForm();

    try {
        const productSubtype = await getProductSubtype(item.id);

        if (!productSubtype) {
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: "Не удалось загрузить данные подвида",
                life: 3000,
            });
            return;
        }

        const copy = JSON.parse(JSON.stringify(productSubtype));

        Object.keys(subtypeForm).forEach((key) => {
            if (key in copy) {
                subtypeForm[key] = copy[key];
            }
        });
    } catch (e) {
        toast.add({
            severity: "error",
            summary: "Ошибка",
            detail: "Не удалось загрузить данные подвида",
            life: 3000,
        });
    } finally {
        subtypeLoading.value = false;
    }
}

function confirmDeleteProductSubtype(productSubtype) {
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
            deleteProductSubtype(productSubtype);
        },
    });
}

async function submitSubtype() {
    resetObject(subtypeErrors);

    subtypeForm.product_id = productLocal.value.id;

    const requireSubtypeFormat =
        productLocal.value.category_type ===
        productCategoriesLocal.value.product.value;
    const requireBottleVolume =
        productLocal.value.category_type ===
            productCategoriesLocal.value.product.value &&
        subtypeForm.product_subtype_format_id !== null &&
        subtypeForm.product_subtype_format_id === remainderItem?.value?.id;
    const requireTextVolume =
        productLocal.value.category_type ===
            productCategoriesLocal.value.product.value &&
        subtypeForm.product_subtype_format_id !== null &&
        subtypeForm.product_subtype_format_id === setItem?.value?.id;

    const result = subtypeEditing.value
        ? productSubtypeUpdateSchema(
              requireSubtypeFormat,
              requireBottleVolume,
              requireTextVolume,
          ).safeParse(subtypeForm)
        : productSubtypeStoreSchema(
              requireSubtypeFormat,
              requireBottleVolume,
              requireTextVolume,
          ).safeParse(subtypeForm);

    if (!result.success) {
        applyZodErrors(result, subtypeErrors);
        return;
    }

    subtypeEditing.value
        ? await updateProductSubtype(result.data)
        : await storeProductSubtype(result.data);
}

async function storeProductSubtype(productSubtype) {
    try {
        const response = await axios.post(
            route("admin.product-subtypes.store"),
            productSubtype,
        );

        productLocal.value.subtypes.push(response.data);

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Продукт сохранен",
            life: 3000,
        });
        onSubtypeCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, subtypeErrors);
    }
}

async function updateProductSubtype(productSubtype) {
    try {
        const response = await axios.patch(
            route("admin.product-subtypes.update", productSubtype.id),
            productSubtype,
        );

        const idx = productLocal.value.subtypes.findIndex(
            (sub) => sub.id === response.data.id,
        );

        if (idx !== -1) {
            productLocal.value.subtypes.splice(idx, 1, response.data);
        }

        toast.add({
            severity: "success",
            summary: "Успех",
            detail: "Продукт сохранен",
            life: 3000,
        });
        onSubtypeCancel();
    } catch (e) {
        applyLaravelErrors(e.response.data.errors, subtypeErrors);
    }
}

async function deleteProductSubtype(productSubtype) {
    axios
        .delete(route("admin.product-subtypes.destroy", productSubtype))
        .then((res) => {
            console.log(res);

            const idx = productLocal.value.subtypes.findIndex(
                (sub) => sub.id === productSubtype.id,
            );

            if (idx !== -1) {
                productLocal.value.subtypes.splice(idx, 1);
            }

            toast.add({
                severity: "success",
                summary: "Информация",
                detail: "Запись успешно удалена!",
                life: 3000,
            });
        })
        .catch((error) => {
            console.log(error);
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
                {{ productLocal.title }} -
                {{ productLocal.category_type_title }}
            </h2>
            <Breadcrumb :model="breadcrumbs" class="!pl-0">
                <template #item="{ item, props }">
                    <a :href="item.url" class="p-breadcrumb-item-link">
                        <span class="p-breadcrumb-item-label text-sm">{{
                            item.label
                        }}</span>
                    </a>
                </template>
                <template #separator> /</template>
            </Breadcrumb>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <Tabs value="main">
                        <TabList>
                            <Tab value="main">Главная</Tab>
                            <Tab value="subtypes">Подвиды</Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel value="main">
                                <form @submit.prevent="submitProduct">
                                    <div class="flex w-full space-x-8">
                                        <div
                                            class="mb-8 flex w-full flex-col gap-1"
                                        >
                                            <label
                                                for="title"
                                                class="required-label font-semibold"
                                                >Название</label
                                            >
                                            <div class="flex w-full flex-col">
                                                <InputText
                                                    v-model="productForm.title"
                                                    id="title"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    placeholder="Введите название"
                                                    :maxlength="78"
                                                    :invalid="
                                                        !!productErrors.title
                                                    "
                                                />
                                                <Message
                                                    v-if="productErrors.title"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.title }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            class="mb-8 flex w-full flex-col gap-1"
                                        >
                                            <label
                                                for="slug"
                                                class="font-semibold"
                                                >Уникальный пользовательский
                                                URL</label
                                            >
                                            <div class="flex w-full flex-col">
                                                <InputText
                                                    v-model="productForm.slug"
                                                    id="slug"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    placeholder="Введите url"
                                                    :invalid="
                                                        !!productErrors.slug
                                                    "
                                                />
                                                <Message
                                                    v-if="productErrors.slug"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.slug }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full space-x-8">
                                        <div
                                            class="mb-8 flex w-full flex-col gap-1"
                                        >
                                            <label
                                                for="sku"
                                                class="required-label font-semibold"
                                                >Артикул</label
                                            >
                                            <div class="flex w-full flex-col">
                                                <InputText
                                                    v-model="productForm.sku"
                                                    id="sku"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    placeholder="Введите артикул"
                                                    :maxlength="25"
                                                    :invalid="
                                                        !!productErrors.sku
                                                    "
                                                />
                                                <Message
                                                    v-if="productErrors.sku"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.sku }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            class="mb-8 flex w-full flex-col gap-1"
                                        >
                                            <label
                                                for="tag"
                                                class="font-semibold"
                                                >Тег</label
                                            >
                                            <div class="flex w-full flex-col">
                                                <InputText
                                                    v-model="productForm.tag"
                                                    id="tag"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    placeholder="Введите тег"
                                                    :maxlength="25"
                                                    :invalid="
                                                        !!productErrors.tag
                                                    "
                                                />
                                                <Message
                                                    v-if="productErrors.tag"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.tag }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                productLocal.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.subtype_category_type
                                                    "
                                                    id="subtype_category_type"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.subtype_category_type
                                                    "
                                                    :options="
                                                        productSubtypeCategoriesArray
                                                    "
                                                    option-label="title"
                                                    option-value="value"
                                                    placeholder="Выберите категорию"
                                                    empty-message="Выберите категорию"
                                                />
                                                <Message
                                                    v-if="
                                                        productErrors.subtype_category_type
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.subtype_category_type
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full space-x-8">
                                        <div
                                            v-if="
                                                product.category_type ===
                                                    productCategoriesLocal
                                                        .product.value ||
                                                product.category_type ===
                                                    productCategoriesLocal
                                                        .giftset.value
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
                                                    v-model="
                                                        productForm.brand_id
                                                    "
                                                    id="brand_id"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.brand_id
                                                    "
                                                    :options="brandsLocal"
                                                    option-label="title"
                                                    option-value="id"
                                                    placeholder="Выберите бренд"
                                                    empty-message="Выберите бренд"
                                                />
                                                <Message
                                                    v-if="
                                                        productErrors.brand_id
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.brand_id }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="productForm.year"
                                                    id="year"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.year
                                                    "
                                                    :min="1900"
                                                    :max="currentYear"
                                                    placeholder="Введите год создания"
                                                />
                                                <Message
                                                    v-if="productErrors.year"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.year }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.gender_type
                                                    "
                                                    id="gender_type"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.gender_type
                                                    "
                                                    :options="gendersArray"
                                                    option-label="title"
                                                    option-value="value"
                                                    placeholder="Выберите бренд"
                                                    empty-message="Выберите бренд"
                                                />
                                                <Message
                                                    v-if="
                                                        productErrors.gender_type
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.gender_type
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full space-x-8">
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.perfumers
                                                    "
                                                    id="perfumers"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.perfumers
                                                    "
                                                    :options="perfumersLocal"
                                                    option-label="title"
                                                    option-value="id"
                                                    placeholder="Выберите парфюмеров"
                                                    empty-message="Выберите парфюмеров"
                                                    :show-toggle-all="false"
                                                />
                                                <Message
                                                    v-if="
                                                        productErrors.perfumers
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.perfumers
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.aroma_groups
                                                    "
                                                    id="aromaGroups"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.aroma_groups
                                                    "
                                                    :options="aromaGroupsLocal"
                                                    option-label="title"
                                                    filter
                                                    option-value="id"
                                                    placeholder="Выберите группы"
                                                    empty-message="Выберите группы"
                                                    :show-toggle-all="false"
                                                />
                                                <Message
                                                    v-if="
                                                        productErrors.aroma_groups
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.aroma_groups
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.aroma_chords
                                                    "
                                                    id="aromaChords"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.aroma_chords
                                                    "
                                                    :options="aromaChordsLocal"
                                                    option-label="title"
                                                    option-value="id"
                                                    filter
                                                    placeholder="Выберите аккорды"
                                                    empty-message="Выберите аккорды"
                                                    :show-toggle-all="false"
                                                />
                                                <Message
                                                    v-if="
                                                        productErrors.aroma_chords
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.aroma_chords
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full space-x-8">
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.top_notes
                                                    "
                                                    id="topNotes"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.top_notes
                                                    "
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
                                                    <template
                                                        #optiongroup="{
                                                            option,
                                                        }"
                                                    >
                                                        <div
                                                            class="flex items-center rounded-md bg-gray-100 px-2 py-1"
                                                        >
                                                            <span
                                                                class="font-semibold text-gray-700"
                                                                >{{
                                                                    option.label
                                                                }}</span
                                                            >
                                                        </div>
                                                    </template>
                                                </MultiSelect>
                                                <Message
                                                    v-if="
                                                        productErrors.top_notes
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.top_notes
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.middle_notes
                                                    "
                                                    id="middleNotes"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.middle_notes
                                                    "
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
                                                    <template
                                                        #optiongroup="{
                                                            option,
                                                        }"
                                                    >
                                                        <div
                                                            class="flex items-center rounded-md bg-gray-100 px-2 py-1"
                                                        >
                                                            <span
                                                                class="font-semibold text-gray-700"
                                                                >{{
                                                                    option.label
                                                                }}</span
                                                            >
                                                        </div>
                                                    </template>
                                                </MultiSelect>
                                                <Message
                                                    v-if="
                                                        productErrors.middle_notes
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.middle_notes
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                    v-model="
                                                        productForm.base_notes
                                                    "
                                                    id="baseNotes"
                                                    class="flex-auto"
                                                    autocomplete="off"
                                                    :invalid="
                                                        !!productErrors.base_notes
                                                    "
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
                                                    <template
                                                        #optiongroup="{
                                                            option,
                                                        }"
                                                    >
                                                        <div
                                                            class="flex items-center rounded-md bg-gray-100 px-2 py-1"
                                                        >
                                                            <span
                                                                class="font-semibold text-gray-700"
                                                                >{{
                                                                    option.label
                                                                }}</span
                                                            >
                                                        </div>
                                                    </template>
                                                </MultiSelect>
                                                <Message
                                                    v-if="
                                                        productErrors.base_notes
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.base_notes
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex w-full space-x-8">
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                        :invalid="
                                                            !!productErrors.longevity
                                                        "
                                                        v-model="
                                                            productForm.longevity
                                                        "
                                                    />
                                                    <Slider
                                                        id="longevity"
                                                        :invalid="
                                                            !!productErrors.longevity
                                                        "
                                                        v-model="
                                                            productForm.longevity
                                                        "
                                                        :min="1"
                                                        :max="10"
                                                    />
                                                </div>
                                                <Message
                                                    v-if="
                                                        productErrors.longevity
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.longevity
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                        :invalid="
                                                            !!productErrors.sillage
                                                        "
                                                        v-model="
                                                            productForm.sillage
                                                        "
                                                    />
                                                    <Slider
                                                        id="sillage"
                                                        v-model="
                                                            productForm.sillage
                                                        "
                                                        :min="1"
                                                        :max="10"
                                                        :invalid="
                                                            !!productErrors.sillage
                                                        "
                                                    />
                                                </div>
                                                <Message
                                                    v-if="productErrors.sillage"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.sillage }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                        :invalid="
                                                            !!productErrors.season
                                                        "
                                                        v-model="
                                                            productForm.season
                                                        "
                                                    />
                                                    <Slider
                                                        id="season"
                                                        :invalid="
                                                            !!productErrors.season
                                                        "
                                                        v-model="
                                                            productForm.season
                                                        "
                                                        :min="1"
                                                        :max="10"
                                                    />
                                                </div>
                                                <Message
                                                    v-if="productErrors.season"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.season }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                        :invalid="
                                                            !!productErrors.time_of_day
                                                        "
                                                        v-model="
                                                            productForm.time_of_day
                                                        "
                                                    />
                                                    <Slider
                                                        id="time_of_day"
                                                        :invalid="
                                                            !!productErrors.time_of_day
                                                        "
                                                        v-model="
                                                            productForm.time_of_day
                                                        "
                                                        :min="1"
                                                        :max="10"
                                                    />
                                                </div>
                                                <Message
                                                    v-if="
                                                        productErrors.time_of_day
                                                    "
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{
                                                        productErrors.time_of_day
                                                    }}
                                                </Message>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                product.category_type ===
                                                productCategoriesLocal.product
                                                    .value
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
                                                        :invalid="
                                                            !!productErrors.gender
                                                        "
                                                        v-model="
                                                            productForm.gender
                                                        "
                                                    />
                                                    <Slider
                                                        id="gender"
                                                        :invalid="
                                                            !!productErrors.gender
                                                        "
                                                        v-model="
                                                            productForm.gender
                                                        "
                                                        :min="1"
                                                        :max="10"
                                                    />
                                                </div>
                                                <Message
                                                    v-if="productErrors.gender"
                                                    severity="error"
                                                    size="small"
                                                    variant="simple"
                                                >
                                                    {{ productErrors.gender }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            product.category_type ===
                                            productCategoriesLocal.product.value
                                        "
                                        class="mb-8 flex w-full flex-col gap-1"
                                    >
                                        <label for="video" class="font-semibold"
                                            >Видео-обзор</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Textarea
                                                v-model="productForm.video"
                                                id="video"
                                                class="flex-auto"
                                                autocomplete="off"
                                                placeholder="Введите iframe код"
                                                :invalid="!!productErrors.video"
                                                :rows="5"
                                            />
                                            <Message
                                                v-if="productErrors.video"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ productErrors.video }}
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
                                                    <span
                                                        v-if="
                                                            productForm.synonyms
                                                                .length === 0
                                                        "
                                                        >Здесь пока пусто!</span
                                                    >

                                                    <div
                                                        v-for="(
                                                            synonym, index
                                                        ) in productForm.synonyms"
                                                        class="flex flex-col"
                                                    >
                                                        <div
                                                            class="flex w-full flex-row gap-1"
                                                        >
                                                            <div
                                                                class="flex w-full flex-col"
                                                            >
                                                                <InputText
                                                                    v-model="
                                                                        synonym.title
                                                                    "
                                                                    class="w-full"
                                                                    :invalid="
                                                                        !!productErrors
                                                                            ?.synonyms?.[
                                                                            index
                                                                        ]?.title
                                                                    "
                                                                    placeholder="Введите синоним"
                                                                />
                                                            </div>
                                                            <div
                                                                class="flex items-end"
                                                            >
                                                                <Button
                                                                    icon="pi pi-trash"
                                                                    variant="text"
                                                                    rounded
                                                                    severity="danger"
                                                                    @click="
                                                                        deleteSynonym(
                                                                            index,
                                                                        )
                                                                    "
                                                                ></Button>
                                                            </div>
                                                        </div>
                                                        <div class="mb-4">
                                                            <Message
                                                                v-if="
                                                                    productErrors.synonyms &&
                                                                    productErrors
                                                                        ?.synonyms?.[
                                                                        index
                                                                    ]?.title
                                                                "
                                                                severity="error"
                                                                size="small"
                                                                variant="simple"
                                                            >
                                                                {{
                                                                    productErrors
                                                                        .synonyms?.[
                                                                        index
                                                                    ]?.title
                                                                }}
                                                            </Message>
                                                        </div>
                                                    </div>
                                                </div>
                                            </Fieldset>
                                            <Message
                                                v-if="
                                                    productErrors.synonyms &&
                                                    productForm.synonyms
                                                        .length === 0
                                                "
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ productErrors.synonyms }}
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
                                                v-model="
                                                    productForm.description
                                                "
                                                id="description"
                                                class="flex-auto"
                                                autocomplete="off"
                                                placeholder="Введите текст"
                                                :invalid="
                                                    !!productErrors.description
                                                "
                                                :rows="5"
                                            />
                                            <Message
                                                v-if="productErrors.description"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ productErrors.description }}
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
                                                v-model:files="
                                                    productForm.files
                                                "
                                                :max-files="15"
                                                :invalid="!!productErrors.files"
                                                :has-favorite-photo="true"
                                            />
                                            <Message
                                                v-if="productErrors.files"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ productErrors.files }}
                                            </Message>
                                        </div>
                                    </div>
                                    <div class="flex gap-4">
                                        <Button
                                            type="button"
                                            severity="danger"
                                            label="Удалить"
                                            @click="confirmDeleteProduct()"
                                        />
                                        <Button
                                            type="submit"
                                            label="Сохранить"
                                        />
                                    </div>
                                </form>
                            </TabPanel>
                            <TabPanel value="subtypes">
                                <DataTable
                                    :value="productLocal.subtypes"
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
                                                    >Подвиды товаров</span
                                                >
                                                <Button
                                                    icon="pi pi-plus"
                                                    label="Добавить"
                                                    @click="
                                                        subtypeModalVisible = true;
                                                        subtypeEditing = false;
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="flex">
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
                                        </div>
                                    </template>
                                    <Column
                                        field="product_subtype_type_title"
                                        header="Тип подвида"
                                        sortable
                                    ></Column>
                                    <Column field="price" header="Цена">
                                        <template #body="slotProps">
                                            {{ slotProps.data.price }} ₽
                                        </template>
                                    </Column>
                                    <Column field="qty" header="Склад">
                                        <template #body="slotProps">
                                            {{ slotProps.data.qty }} шт.
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
                                                        editProductSubtype(
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
                                                        confirmDeleteProductSubtype(
                                                            slotProps.data,
                                                        )
                                                    "
                                                />
                                            </div>
                                        </template>
                                    </Column>
                                </DataTable>
                                <Dialog
                                    v-model:visible="subtypeModalVisible"
                                    modal
                                    :header="
                                        subtypeEditing
                                            ? 'Редактирование подвида'
                                            : 'Создание подвида'
                                    "
                                    style="width: 70rem"
                                    @hide="onSubtypeCancel"
                                >
                                    <div class="mb-8 flex flex-col gap-1">
                                        <label
                                            for="product_subtype_type"
                                            class="required-label font-semibold"
                                            >Тип подвида</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <Select
                                                v-model="
                                                    subtypeForm.product_subtype_type
                                                "
                                                id="product_subtype_format"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="
                                                    !!subtypeErrors.product_subtype_type
                                                "
                                                :options="
                                                    productSubtypeTypesArray
                                                "
                                                option-label="title"
                                                option-value="value"
                                                placeholder="Выберите тип подвида"
                                                empty-message="Выберите тип подвида"
                                                :show-toggle-all="false"
                                            />
                                            <Message
                                                v-if="
                                                    subtypeErrors.product_subtype_type
                                                "
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{
                                                    subtypeErrors.product_subtype_type
                                                }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div class="mb-8 flex flex-col gap-1">
                                        <label
                                            for="price"
                                            class="required-label font-semibold"
                                            >Стоимость</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <InputNumber
                                                v-model="subtypeForm.price"
                                                id="price"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="!!subtypeErrors.price"
                                                placeholder="Введите стоимость"
                                                mode="currency"
                                                currency="RUB"
                                                locale="ru-RU"
                                                :min="0"
                                            />
                                            <Message
                                                v-if="subtypeErrors.price"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ subtypeErrors.price }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div class="mb-8 flex flex-col gap-1">
                                        <label
                                            for="qty"
                                            class="required-label font-semibold"
                                            >Наличие на складе</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <InputNumber
                                                v-model="subtypeForm.qty"
                                                id="qty"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="!!subtypeErrors.qty"
                                                placeholder="Введите стоимость"
                                                :min="0"
                                            />
                                            <Message
                                                v-if="subtypeErrors.qty"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ subtypeErrors.qty }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            productLocal.category_type ===
                                            productCategoriesLocal.product.value
                                        "
                                        class="mb-8 flex w-full flex-col gap-1"
                                    >
                                        <label
                                            for="product_subtype_type"
                                            class="required-label font-semibold"
                                            >Формат подвида</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <Select
                                                v-else
                                                v-model="
                                                    subtypeForm.product_subtype_format_id
                                                "
                                                id="subtype_category_type"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="
                                                    !!subtypeErrors.product_subtype_format_id
                                                "
                                                :options="
                                                    productSubtypeFormatsLocal
                                                "
                                                option-label="title"
                                                option-value="id"
                                                placeholder="Выберите формат"
                                                empty-message="Выберите формат"
                                            />
                                            <Message
                                                v-if="
                                                    subtypeErrors.product_subtype_format_id
                                                "
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{
                                                    subtypeErrors.product_subtype_format_id
                                                }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            productLocal.category_type ===
                                            productCategoriesLocal.product.value
                                        "
                                        class="mb-8 flex w-full flex-col gap-1"
                                    >
                                        <label
                                            for="volume"
                                            class="required-label font-semibold"
                                            >Объём (мл)</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <InputNumber
                                                v-else
                                                v-model="subtypeForm.volume"
                                                id="volume"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="
                                                    !!subtypeErrors.volume
                                                "
                                                placeholder="Введите объём"
                                                :min="0"
                                            />
                                            <Message
                                                v-if="subtypeErrors.volume"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ subtypeErrors.volume }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            productLocal.category_type ===
                                                productCategoriesLocal.product
                                                    .value &&
                                            subtypeForm.product_subtype_format_id !==
                                                null &&
                                            subtypeForm.product_subtype_format_id ===
                                                remainderItem?.id
                                        "
                                        class="mb-8 flex w-full flex-col gap-1"
                                    >
                                        <label
                                            for="bottle_volume"
                                            class="required-label font-semibold"
                                            >Объём флакона (мл)</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <InputNumber
                                                v-else
                                                v-model="
                                                    subtypeForm.bottle_volume
                                                "
                                                id="bottle_volume"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="
                                                    !!subtypeErrors.bottle_volume
                                                "
                                                placeholder="Введите объём"
                                                :min="0"
                                            />
                                            <Message
                                                v-if="
                                                    subtypeErrors.bottle_volume
                                                "
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{
                                                    subtypeErrors.bottle_volume
                                                }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            productLocal.category_type ===
                                                productCategoriesLocal.product
                                                    .value &&
                                            subtypeForm.product_subtype_format_id !==
                                                null &&
                                            subtypeForm.product_subtype_format_id ===
                                                setItem?.id
                                        "
                                        class="mb-8 flex w-full flex-col gap-1"
                                    >
                                        <label
                                            for="volume_text"
                                            class="required-label font-semibold"
                                            >Текст вместо объёма (мл)</label
                                        >
                                        <div class="flex w-full flex-col">
                                            <Skeleton
                                                v-if="subtypeLoading"
                                                height="2rem"
                                            ></Skeleton>
                                            <InputText
                                                v-else
                                                v-model="
                                                    subtypeForm.volume_text
                                                "
                                                id="volume_text"
                                                class="flex-auto"
                                                autocomplete="off"
                                                :invalid="
                                                    !!subtypeErrors.volume_text
                                                "
                                                placeholder="Введите объём"
                                            />
                                            <Message
                                                v-if="subtypeErrors.volume_text"
                                                severity="error"
                                                size="small"
                                                variant="simple"
                                            >
                                                {{ subtypeErrors.volume_text }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-2">
                                        <Button
                                            type="button"
                                            label="Отменить"
                                            severity="secondary"
                                            @click="onSubtypeCancel"
                                        ></Button>
                                        <Button
                                            type="button"
                                            label="Сохранить"
                                            @click="submitSubtype"
                                        ></Button>
                                    </div>
                                </Dialog>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
