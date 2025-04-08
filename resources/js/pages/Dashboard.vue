<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    auth: object;
    user: string;
    email: string;
    subscriptions: string[];
    music: MusicItem[];
}

export interface MusicItem {
    title: string;
    artist: string;
    album: string;
    year?: number;
    img_url?: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: `Welcome, ${props.user}`,
        href: '/dashboard',
    },
];

const subscribe = (song: MusicItem) => {
    // //alert(song.title)
    // if (props.subscriptions.includes(song.title)) {
    //     alert('Music has already been added.');
    //     return;
    // }
    const data = {
        title: song.title,
        album: song.album,
        year: song.year,
        img_url: song.img_url,
        artist: song.artist,
    };

    // Posting data using Inertia's post method
    // router.post('/subscribe', data);
    router.post('/subscribe', data, {
        onSuccess: () => {
            alert(`${song.title} subscribed successfully!`);
        },
        onError: () => {
            alert(`Failed to subscribe to ${song.title}`);
        },
    });
};

const form = useForm({
    title: '',
    year: '',
    artist: '',
    album: '',
});

const isSubmitAllowed = ref(false);

const checkInput = () => {
    if (form.title.trim() !== '') {
        isSubmitAllowed.value = true;
        return;
    }
    if (form.year.trim() !== '') {
        isSubmitAllowed.value = true;
        return;
    }
    if (form.artist.trim() !== '') {
        isSubmitAllowed.value = true;
        return;
    }
    if (form.album.trim() !== '') {
        isSubmitAllowed.value = true;
        return;
    }
    isSubmitAllowed.value = false;
};
const submit = () => {
    form.get(route('query'), {});
};
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="p-4">
            <!-- <h1 class="ext-2xl font-bold">Welcome, {{ props.user }}</h1> -->
            <h1 class="mb-4 text-2xl font-bold">Discoveries</h1>
            <div class="mb-6 rounded-lg p-6 shadow-lg">
                <div>
                    <h1 class="mb-3 text-3xl font-semibold">Search Your Favorite Music</h1>
                </div>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-2 gap-4">
                        <Input @change="checkInput" id="title" type="text" autofocus :tabindex="1" placeholder="Title" v-model="form.title" />
                        <Input @change="checkInput" id="year" type="number" autofocus :tabindex="1" placeholder="Year" v-model="form.year" />
                        <Input @change="checkInput" id="artist" type="text" autofocus :tabindex="1" placeholder="Artist" v-model="form.artist" />
                        <Input @change="checkInput" id="album" type="text" autofocus :tabindex="1" placeholder="Album" v-model="form.album" />
                    </div>
                    <div class="flex">
                        <Button type="submit" class="ml-auto mt-4" :tabindex="4" :disabled="!isSubmitAllowed">
                            <Search />
                            Search
                        </Button>
                    </div>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div v-for="(song, index) in props.music" :key="index" class="rounded-xl border p-4 shadow">
                    <img v-if="song.img_url" :src="song.img_url" alt="Cover" class="mb-2 h-40 w-full rounded object-cover" />
                    <h2 class="font-semibold">{{ song.title }}</h2>
                    <p class="text-sm text-gray-600">{{ song.artist }}</p>
                    <p class="text-sm text-gray-500">{{ song.album }} ({{ song.year }})</p>
                    <button @click="() => subscribe(song)" class="mt-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700">Subscribe</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
