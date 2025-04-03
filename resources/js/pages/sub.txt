<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { MusicItem } from '@/pages/Dashboard.vue';

interface SubscriptionItem {
    title: string;
    artist: string;
    album: string;
    year?: number;
    img_url?: string;
}

interface Props {
    subscriptions: SubscriptionItem[];
    user: string;
}

const props = defineProps<Props>();
const unsubscribe = (song:MusicItem)=>{
    //alert(song.title)

    const data = {
        title: song.title,
        album: song.album,
        year: song.year,
        img_url: song.img_url,
        artist: song.artist,

    };

    // Posting data using Inertia's post method
    router.post('/unsubscribe', data);
}

</script>

<template>
    <Head title="My Subscriptions" />

    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Hi {{ props.user }}, your subscriptions:</h1>

            <div v-if="props.subscriptions.length > 0" class="grid gap-4 md:grid-cols-3">
                <div
                    v-for="(item, index) in props.subscriptions"
                    :key="index"
                    class="rounded-xl border p-4 shadow"
                >
                    <img
                        v-if="item.img_url"
                        :src="item.img_url"
                        alt="Cover"
                        class="mb-2 w-full h-40 object-cover rounded"
                    />
                    <h2 class="font-semibold">{{ item.title }}</h2>
                    <p class="text-sm text-gray-600">{{ item.artist }}</p>
                    <p class="text-sm text-gray-500">{{ item.album }} ({{ item.year }})</p>
                    <button
                        @click="()=>unsubscribe(item)"
                        class="mt-2 rounded bg-blue-600 px-3 py-1 text-white hover:bg-blue-700"
                    >
                        Unsubscribe
                    </button>
                </div>

            </div>

            <div v-else class="text-gray-500">You don't have any subscriptions yet.</div>
        </div>
    </AppLayout>
</template>
