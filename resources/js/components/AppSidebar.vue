<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index } from '@/routes/quiz'
import { index as questionIndex } from '@/routes/question'
import { computed } from 'vue';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { MessageCircleQuestion, FileQuestion, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
const page = usePage();
const auth = computed(() => page.props.auth);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,        
        can: true
    },
    {
        title: 'Quiz',
        href: index(),
        icon: MessageCircleQuestion,
        can: auth.value.role === 'Admin'
    },
    {
        title: 'Question',
        href: questionIndex(),
        icon: FileQuestion,
        can: auth.value.role === 'Admin'
    },
];

const filteredMainNavItems = computed(() => {
    return mainNavItems.filter(item => item.can);
});

const footerNavItems: NavItem[] = [    
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredMainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
