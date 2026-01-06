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
import { index as articleIndex } from '@/routes/article';
import { index as questionIndex } from '@/routes/question';
import { index } from '@/routes/quiz';
import { popular_quizzes, questions_report } from '@/routes/reports';
import { type NavItem, type NavItemGroup } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    FileQuestion,
    LayoutGrid,
    MessageCircleQuestion,
    Newspaper,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
const page = usePage();
const auth = computed(() => page.props.auth);

const mainNavItems: NavItemGroup[] = [
    {
        title: 'Platform',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
                can: true,
            },
            {
                title: 'Quiz',
                href: index(),
                icon: MessageCircleQuestion,
                can: auth.value.role === 'Admin',
            },
            {
                title: 'Question',
                href: questionIndex(),
                icon: FileQuestion,
                can: auth.value.role === 'Admin',
            },
            {
                title: 'Article',
                href: articleIndex(),
                icon: Newspaper,
                can: auth.value.role === 'Admin',
            },
        ],
    },
    {
        title: 'Reports',
        items: [
            {
                title: 'Popular Quizzes',
                href: popular_quizzes(),
                icon: LayoutGrid,
                can: auth.value.role === 'Admin',
            },
            {
                title: 'Questions Report',
                href: questions_report(),
                icon: LayoutGrid,
                can: auth.value.role === 'Admin',
            },
        ],
    },
];

const filteredMainNavItems = computed(() => {
    const tempNavItems: NavItemGroup[] = [];
    mainNavItems.forEach((group) => {
        const temp: NavItem[] = [];
        group.items.forEach((item) => {
            if (item.can) {
                temp.push(item);
            }
        });
        if (temp.length > 0) {
            tempNavItems.push({
                title: group.title,
                items: temp,
            });
        }
    });
    return tempNavItems;
});

const footerNavItems: NavItem[] = [];
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
