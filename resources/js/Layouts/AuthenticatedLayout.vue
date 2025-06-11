<script setup>
import { onMounted, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";

import "primeicons/primeicons.css";

const showingNavigationDropdown = ref(false);
const showArchiveLinks = ref(false);

function toggleArchiveLinks() {
    showArchiveLinks.value = !showArchiveLinks.value;
}

onMounted(() => {
    if (
        route().current("admin.countries.index") ||
        route().current("admin.brands.index") ||
        route().current("admin.perfumers.index") ||
        route().current("admin.aroma-groups.index") ||
        route().current("admin.aroma-chords.index") ||
        route().current("admin.aroma-notes.index") ||
        route().current("admin.product-subtype-formats.index")
    ) {
        showArchiveLinks.value = true;
    }
});
</script>

<template>
    <div class="relative flex min-h-screen">
        <!-- sidebar -->
        <div class="w-64 space-y-6 bg-[#1c1b22] px-2 py-6 text-cyan-100">
            <Link
                class="flex items-center space-x-2 px-4"
                :href="route('admin.products.index')"
            >
                <ApplicationLogo
                    class="block h-9 w-auto fill-current text-white"
                />
                <span class="text-2xl font-extrabold text-white"
                    >Parfummaniac</span
                >
            </Link>
            <hr class="rounded border-[#2d2f36]" />
            <nav>
                <a
                    :href="route('admin.products.index')"
                    class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 text-white transition duration-200 hover:bg-[#2d2f36]"
                    :class="[
                        { 'bg-[#2d2f36]': route().current('admin.catalog') },
                    ]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                        />
                    </svg>
                    <span class="text-white"> Каталог </span>
                </a>
                <button
                    class="group mb-1 flex w-full items-center justify-between rounded-xl px-4 py-3 text-white transition duration-200 hover:bg-[#2d2f36]"
                    @click="toggleArchiveLinks"
                >
                    <div class="flex items-center gap-x-2">
                        <svg
                            v-if="showArchiveLinks"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776"
                            />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 13.5H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"
                            />
                        </svg>
                        Справочник
                    </div>
                </button>
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 scale-y-95"
                    enter-to-class="transform opacity-100 scale-y-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="transform opacity-100 scale-y-100"
                    leave-to-class="transform opacity-0 scale-y-95"
                >
                    <div v-show="showArchiveLinks" class="origin-top">
                        <a
                            :href="route('admin.aroma-chords.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]': route().current(
                                        'admin.aroma-chords.index',
                                    ),
                                },
                            ]"
                        >
                            <span class="text-white"> Аккорды ароматов </span>
                        </a>

                        <a
                            :href="route('admin.aroma-groups.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]': route().current(
                                        'admin.aroma-groups.index',
                                    ),
                                },
                            ]"
                        >
                            <span class="text-white"> Группы ароматов </span>
                        </a>

                        <a
                            :href="route('admin.aroma-notes.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]': route().current(
                                        'admin.aroma-notes.index',
                                    ),
                                },
                            ]"
                        >
                            <span class="text-white"> Ноты ароматов </span>
                        </a>

                        <a
                            :href="route('admin.product-subtype-formats.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]': route().current(
                                        'admin.product-subtype-formats.index',
                                    ),
                                },
                            ]"
                        >
                            <span class="text-white"> Форматы подвидов </span>
                        </a>

                        <a
                            :href="route('admin.brands.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]':
                                        route().current('admin.brands.index'),
                                },
                            ]"
                        >
                            <span class="text-white"> Бренды </span>
                        </a>

                        <a
                            :href="route('admin.perfumers.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]': route().current(
                                        'admin.perfumers.index',
                                    ),
                                },
                            ]"
                        >
                            <span class="text-white"> Парфюмеры </span>
                        </a>

                        <a
                            :href="route('admin.countries.index')"
                            class="group mb-1 flex items-center space-x-2 rounded-xl px-4 py-3 pl-8 text-white transition duration-200 hover:bg-[#2d2f36]"
                            :class="[
                                {
                                    'bg-[#2d2f36]': route().current(
                                        'admin.countries.index',
                                    ),
                                },
                            ]"
                        >
                            <span class="text-white"> Страны </span>
                        </a>
                    </div>
                </transition>
            </nav>
        </div>
        <!-- main content -->
        <div class="flex-1 bg-[#edeef0]">
            <!-- header-->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>
            <!--content-->
            <main>
                <slot />
                <Toast />
                <ConfirmDialog></ConfirmDialog>
            </main>
        </div>
    </div>
</template>
