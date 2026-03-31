<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    LayoutGrid, 
    Users, 
    HardHat, 
    FileText, 
    Briefcase, 
    Package, 
    CreditCard, 
    TrendingDown 
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
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
import { index as clientsIndex } from '@/routes/clients';
import { index as chantiersIndex } from '@/routes/chantiers';
import { index as devisIndex } from '@/routes/devis';
import { index as prestatairesIndex } from '@/routes/prestataires';
import { index as servicesIndex } from '@/routes/services';
import { index as contratsIndex } from '@/routes/contrats';
import { index as paiementsIndex } from '@/routes/paiements';
import { index as depensesIndex } from '@/routes/depenses';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clients',
        href: clientsIndex(),
        icon: Users,
    },
    {
        title: 'Chantiers',
        href: chantiersIndex(),
        icon: HardHat,
    },
    {
        title: 'Devis',
        href: devisIndex(),
        icon: FileText,
    },
    {
        title: 'Prestataires',
        href: prestatairesIndex(),
        icon: Briefcase,
    },
    {
        title: 'Services',
        href: servicesIndex(),
        icon: Package,
    },
    {
        title: 'Contrats',
        href: contratsIndex(),
        icon: FileText,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Paiements',
        href: paiementsIndex(),
        icon: CreditCard,
    },
    {
        title: 'Dépenses',
        href: depensesIndex(),
        icon: TrendingDown,
    },
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
