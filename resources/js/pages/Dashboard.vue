<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface Props {
    user: string;
    email: string;
    subscriptions: string[];
    music: MusicItem[];
};


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

const subscribe = (song:MusicItem)=>{
    //alert(song.title)

    const data = {
        title: song.title,
        album: song.album,
        year: song.year,
        img_url: song.img_url,
        artist: song.artist,

    };

    // Posting data using Inertia's post method
    router.post('/subscribe', data);
}

</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="p-4">
            <h1 class="text-2xl font-bold mb-4">Welcome, {{ props.email }}</h1>


            <div class="grid gap-4 md:grid-cols-3">
                <div
                    v-for="(song, index) in props.music"
                    :key="index"
                    class="rounded-xl border p-4 shadow"
                >

                    <img
                        v-if="song.img_url"
                        :src="song.img_url"
                        alt="Cover"
                        class="mb-2 w-full h-40 object-cover rounded"
                    />
                    <h2 class="font-semibold">{{ song.title }}</h2>
                    <p class="text-sm text-gray-600">{{ song.artist }}</p>
                    <p class="text-sm text-gray-500">{{ song.album }} ({{ song.year }})</p>
                    <button
                        @click="()=>subscribe(song)"
                        class="mt-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                    >
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
