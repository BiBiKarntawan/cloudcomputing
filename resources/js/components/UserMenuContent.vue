<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { router } from '@inertiajs/vue3';
import { LogOut } from 'lucide-vue-next';
import Button from './ui/button/Button.vue';

interface Props {
    user: User;
}

defineProps<Props>();

const handleLogout = () => {
    localStorage.removeItem('login');
    router.post(route('logout'));
};
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />

    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Button class="flex w-full" @click="handleLogout" type="button">
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Button>
    </DropdownMenuItem>
</template>
