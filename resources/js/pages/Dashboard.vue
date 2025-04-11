<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Auth, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Search } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    auth: Auth;
    user: string;
    email: string;
    subscriptions: string[];
    music: MusicItem[];
    subscriptions_list: SubscriptionItem[];
}

export interface MusicItem {
    title: string;
    artist: string;
    album: string;
    year?: number;
    img_url?: string;
}

interface SubscriptionItem {
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
    const data = {
        title: song.title,
        album: song.album,
        year: song.year,
        img_url: song.img_url,
        artist: song.artist,
        email: props.auth.user.email,
    };

    // router.post('/subscribe', data, {
    //     onSuccess: () => {
    //         alert(`${song.title} subscribed successfully!`);
    //     },
    //     onError: () => {
    //         alert(`Failed to subscribe to ${song.title}`);
    //     },
    // });

    axios
        .put(
            'https://a2vwrk4t8f.execute-api.us-east-1.amazonaws.com/default/myMusicLambdaFunction/subscribe',
            data, // Gets the current data
            {
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            },
        )
        .then((response) => {
            //subscribe
            console.log(response);

            alert('Subscribe successfully.');
            router.get(route('dashboard'));
        })
        .catch((error) => {
            console.error('Subscribe error:', error);
            alert('Subscribe error.');
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
    if (form.year !== '') {
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

const unsubscribe = (song: MusicItem) => {
    //alert(song.title)

    const data = {
        title: song.title,
        album: song.album,
        year: song.year,
        img_url: song.img_url,
        artist: song.artist,
        email: props.auth.user.email,
    };

    // router.post('/unsubscribe', data);

    axios
        .put(
            'https://a2vwrk4t8f.execute-api.us-east-1.amazonaws.com/default/myMusicLambdaFunction/unsubscribe',
            data, // Gets the current data
            {
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
            },
        )
        .then((response) => {
            //subscribe
            console.log(response);

            alert('Unsubscribe successfully.');
            router.get(route('dashboard'));
        })
        .catch((error) => {
            console.error('Unsubscribe error:', error);
            alert('Unsubscribe error.');
        });
};
</script>

<template>
    <Head title="Music Now" />
    <AppLayout>
        <div class="bg-yellow-300 p-4">
            <!-- <h1 class="ext-2xl font-bold">Welcome, {{ props.user }}</h1> -->
            <h1 class="mb-4 text-2xl font-bold">Discoveries</h1>
            <div class="mb-6 rounded-lg bg-yellow-300 p-6 shadow-lg">
                <h1 class="mb-4 text-3xl font-semibold">Manage your subscriptions</h1>

                <div v-if="props.subscriptions_list && props.subscriptions_list.length > 0" class="grid gap-4 md:grid-cols-3">
                    <div v-for="(item, index) in props.subscriptions_list" :key="index" class="rounded-xl border bg-white p-4 shadow">
                        <img v-if="item.img_url" :src="item.img_url" alt="Cover" class="mb-2 h-40 w-full rounded object-cover" />
                        <h2 class="font-semibold">{{ item.title }}</h2>
                        <p class="text-sm text-gray-600">{{ item.artist }}</p>
                        <p class="text-sm text-gray-500">{{ item.album }} ({{ item.year }})</p>
                        <button
                            @click="() => unsubscribe(item)"
                            class="mt-2 rounded bg-yellow-300 px-3 py-1 font-semibold text-black hover:bg-yellow-400"
                        >
                            Unsubscribe
                        </button>
                    </div>
                </div>

                <div v-else class="text-gray-500">You don't have any subscriptions yet.</div>
            </div>
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
                        <Button type="submit" class="ml-auto mt-4 font-semibold" :tabindex="4" :disabled="!isSubmitAllowed">
                            <Search />
                            Search
                        </Button>
                    </div>
                </form>
            </div>

            <div v-if="props.music && props.music.length === 0" class="mb-6 rounded-lg p-6 text-center shadow-lg">
                <p class="text-lg font-medium text-yellow-800">No result is retrieved. Please query again.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div v-for="(song, index) in props.music" :key="index" class="rounded-xl border bg-white p-4 shadow">
                    <img v-if="song.img_url" :src="song.img_url" alt="Cover" class="mb-2 h-40 w-full rounded object-cover" />
                    <h2 class="font-semibold">{{ song.title }}</h2>
                    <p class="text-sm text-gray-600">{{ song.artist }}</p>
                    <p class="text-sm text-gray-500">{{ song.album }} ({{ song.year }})</p>
                    <button @click="() => subscribe(song)" class="mt-2 rounded bg-yellow-300 px-3 py-1 font-semibold text-black hover:bg-yellow-400">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
