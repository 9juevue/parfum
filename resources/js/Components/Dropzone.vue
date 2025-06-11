<script setup>
import {
    ref,
    watch,
    onMounted,
    onBeforeMount,
    onBeforeUnmount,
    toRefs,
    getCurrentInstance,
} from "vue";
import Dropzone from "dropzone";
import Sortable from "sortablejs";
import "dropzone/dist/dropzone.css";
import { useToast } from "primevue/usetoast";

const props = defineProps({
    files: {
        type: Object,
        required: true,
    },
    maxFiles: {
        type: Number,
        default: 15,
    },
    acceptedFiles: {
        type: String,
        default: ".jpg, .jpeg, .png",
    },
    hasFavoritePhoto: {
        type: Boolean,
        default: false,
    },
    minify: {
        type: Boolean,
        default: false,
    },
    invalid: {
        type: Boolean,
        default: false,
    },
});

const { files } = toRefs(props);

const toast = useToast();

const dropzone = ref(null);
const sortable = ref(null);
const sortIndex = ref(0);
const fileCount = ref(0);

const dropzoneContainer = ref(null);
const previewsContainer = ref(null);
const dzDefaultMessage = ref(null);

function toggleDropzoneMessage(imgs) {
    if (dzDefaultMessage.value && imgs) {
        dzDefaultMessage.value.style.display =
            imgs.length > 0 ? "none" : "block";
    }
}

watch(files, (val) => toggleDropzoneMessage(val), { deep: true });

function addImage(image) {
    if (Array.isArray(files.value)) {
        files.value.push(image);
    } else {
        files.value = [image];
    }
}

function deleteImage(file) {
    const idx = files.value.findIndex(
        (fileData) => fileData.url === file.upload.url,
    );

    files.value.forEach((fileData) => {
        if (fileData.sort > idx) fileData.sort -= 1;
    });
    if (idx !== -1) {
        files.value.splice(idx, 1);
        if (sortIndex.value > 0) sortIndex.value--;
    }
    if (fileCount.value > 0) fileCount.value--;
}

function setFavoriteImage(file) {
    if (!props.hasFavoritePhoto) return;

    files.value.forEach((fileData) => {
        fileData.is_favorite =
            fileData.url === file.upload.url ? !fileData.is_favorite : false;
    });
    document
        .querySelectorAll(".dz-favorite")
        .forEach((btn) => btn.classList.remove("active"));
    const previewElement = file.previewElement;
    const favoriteBtn = previewElement?.querySelector(".dz-favorite");
    if (favoriteBtn) favoriteBtn.classList.toggle("active");
}

async function preloadFileFromUrl(imageUrl) {
    try {
        const res = await fetch(imageUrl);
        if (!res.ok) return null;

        const blob = await res.blob();
        const filename = imageUrl.split("/").pop() || "image.jpg";
        return new File([blob], filename, { type: blob.type });
    } catch {
        return null;
    }
}

onBeforeMount(async () => {
    if (props.minify) {
        await import("../../css/dropzone-minify.css");
    }
});

onMounted(async () => {
    let _this = this;

    const csrf =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";
    const dz = new Dropzone(dropzoneContainer.value, {
        url: "/admin/upload",
        previewsContainer: previewsContainer.value,
        maxFiles: props.maxFiles,
        acceptedFiles: props.acceptedFiles,
        headers: {
            "X-CSRF-TOKEN": csrf,
        },
    });
    dropzone.value = dz;

    dz.on("sending", (file, xhr, formData) => {
        formData.append("sort", sortIndex.value.toString());
        sortIndex.value += 1;
    });

    dz.on("success", (file, response) => {
        file.upload.url = response.url;
        addImage({
            sort: files.value.length,
            url: response.url,
        });
    });

    dz.on("error", (file, errorMessage) => {
        const acceptedFilesErrorMessage =
            "You can't upload files of this type.";
        if (errorMessage === acceptedFilesErrorMessage) {
            dz.removeFile(file);
            deleteImage(file);
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: `Неверный формат файла. Пожалуйста, загрузите изображение в формате: ${props.acceptedFiles}`,
                life: 3000,
            });
        }
    });

    dz.on("maxfilesexceeded", (file) => {
        if (!file._maxFilesExceeded) {
            file._maxFilesExceeded = true;
            dz.removeFile(file);
            toast.add({
                severity: "error",
                summary: "Ошибка",
                detail: `Максимальное число загружаемых файлов: ${props.maxFiles}`,
                life: 3000,
            });
        }
    });

    dz.on("addedfile", (file) => {
        if (fileCount.value >= props.maxFiles && !file._maxFilesExceeded) {
            dz.emit("maxfilesexceeded", file);
            file._maxFilesExceeded = true;
            return;
        }

        const removeButton = Dropzone.createElement(
            "<button class='dz-remove'/>",
        );
        removeButton.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            dz.removeFile(file);
            deleteImage(file);
        });
        file.previewElement
            ?.querySelector(".dz-details")
            ?.appendChild(removeButton);

        if (props.hasFavoritePhoto) {
            const favoriteButton = Dropzone.createElement(
                "<button class='dz-favorite'/>",
            );
            if (file.upload?.is_favorite)
                favoriteButton.classList.add("active");
            favoriteButton.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                setFavoriteImage(file);
            });
            file.previewElement
                ?.querySelector(".dz-details")
                ?.appendChild(favoriteButton);
        }

        fileCount.value += 1;
    });

    if (files.value && files.value.length > 0) {
        files.value
            .sort((a, b) => a.sort - b.sort)
            .forEach((img, idx) => {
                img.sort = idx;
            });

        for (const img of files.value) {
            const preloadedFile = await preloadFileFromUrl(img.url);

            const mock = {
                name: preloadedFile.name,
                size: preloadedFile.size,
                type: preloadedFile.type,
            };
            mock.upload = {
                url: img.url,
                sort: img.sort,
                is_favorite: img.is_favorite,
            };
            dz.displayExistingFile(mock, img.url, null, "anonymous");
        }
    }

    toggleDropzoneMessage(files.value);

    sortable.value = Sortable.create(previewsContainer.value, {
        draggable: ".dz-preview",
        filter: ".dz-error",
        animation: 150,
        onEnd(event) {
            const { oldIndex, newIndex } = event;
            if (oldIndex === newIndex) return;

            const moved = files.value.find((img) => img.sort === oldIndex);
            files.value.forEach((img) => {
                if (oldIndex > newIndex) {
                    if (img.sort <= oldIndex && img.sort >= newIndex)
                        img.sort += 1;
                } else if (img.sort >= oldIndex && img.sort <= newIndex) {
                    img.sort -= 1;
                }
            });
            if (moved) moved.sort = newIndex;
        },
    });
});

onBeforeUnmount(() => {
    dropzone.value?.destroy?.();
    sortable.value?.destroy?.();
});
</script>

<template>
    <div
        ref="dropzoneContainer"
        class="dropzone"
        :class="[{ error: invalid, 'dropzone-min': minify }]"
    >
        <div
            ref="previewsContainer"
            class="custom-preview-container"
            @click="dropzoneContainer && dropzoneContainer.click()"
        ></div>
        <div ref="dzDefaultMessage" class="dz-message" data-dz-message>
            <span>Перетащите файлы для загрузки</span>
        </div>
    </div>
</template>

<style scoped>
::v-deep(.dz-remove::after) {
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    content: "\00d7";
    right: 0;
    top: 0;
    width: 30px;
    height: 30px;
    font-size: 20px;
    background-color: #f34444;
    color: white;
    border-radius: 50%;
    z-index: 999;
}

::v-deep(.dz-remove:hover::after) {
    background-color: #d33c3c;
}

::v-deep(.dz-favorite::after) {
    content: "★";
    font-size: 30px;
    color: #48463e;
    transition: color 0.3s ease;
    left: 0;
    top: 0;
    position: absolute;
    cursor: pointer;
    text-align: center;
    margin: 4px 0 0 4px;
}

::v-deep(.dz-favorite:hover::after) {
    color: #ffe200;
}

::v-deep(.dz-favorite.active) {
    background-color: #ffe200;
}

::v-deep(.dz-favorite.active::after) {
    color: #ffe200;
}

::v-deep(.dz-favorite.active svg) {
    fill: #ffe200;
}

.dropzone {
    border: 2px dashed #10b981 !important;
}

.custom-preview-container {
    cursor: pointer;
}

.dropzone.error {
    border: 2px dashed #ff0000 !important;
}

.dropzone.error .dz-message {
    color: #ff0000 !important;
}
</style>
