import { Link, usePage } from '@inertiajs/react';
import { LayoutDashboard, FlaskConical, FileText, ClipboardList, LogOut } from 'lucide-react';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarInset,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import { Separator } from '@/components/ui/separator';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { TooltipProvider } from '@/components/ui/tooltip';

function useNavItems() {
    return [
        { title: 'Dashboard', href: route('dashboard.index'), icon: LayoutDashboard },
        { title: 'Amostras', href: route('amostras.index'), icon: FlaskConical },
        { title: 'Laudos', href: route('laudos.index'), icon: FileText },
        { title: 'Ordens de Serviço', href: route('ordens_servicos.index'), icon: ClipboardList },
    ];
}

function userInitials(name) {
    if (!name) return '?';
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

export default function AppLayout({ children }) {
    const { props, url } = usePage();
    const user = props.auth?.user;
    const currentPath = url.split('?')[0];
    const navItems = useNavItems();

    return (
        <TooltipProvider>
            <SidebarProvider>
                <Sidebar collapsible="icon">
                    <SidebarHeader>
                        <div className="flex items-center gap-2 px-2 py-1.5">
                            <span className="font-heading text-sm font-semibold group-data-[collapsible=icon]:hidden">
                                SCA
                            </span>
                        </div>
                    </SidebarHeader>
                    <SidebarContent>
                        <SidebarGroup>
                            <SidebarGroupLabel>Navegação</SidebarGroupLabel>
                            <SidebarGroupContent>
                                <SidebarMenu>
                                    {navItems.map((item) => (
                                        <SidebarMenuItem key={item.href}>
                                            <SidebarMenuButton
                                                tooltip={item.title}
                                                isActive={currentPath.startsWith(new URL(item.href).pathname)}
                                                render={<Link href={item.href} />}
                                            >
                                                <item.icon />
                                                <span>{item.title}</span>
                                            </SidebarMenuButton>
                                        </SidebarMenuItem>
                                    ))}
                                </SidebarMenu>
                            </SidebarGroupContent>
                        </SidebarGroup>
                    </SidebarContent>
                    <SidebarFooter>
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <DropdownMenu>
                                    <DropdownMenuTrigger
                                        render={
                                            <SidebarMenuButton size="lg">
                                                <Avatar className="size-6 rounded-md">
                                                    <AvatarFallback className="rounded-md">
                                                        {userInitials(user?.name)}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <span className="truncate">{user?.name}</span>
                                            </SidebarMenuButton>
                                        }
                                    />
                                    <DropdownMenuContent side="top" align="start" className="w-56">
                                        <DropdownMenuLabel className="truncate">
                                            {user?.email}
                                        </DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuGroup>
                                            <DropdownMenuItem render={<Link href="/logout" />}>
                                                <LogOut data-icon="inline-start" />
                                                Sair
                                            </DropdownMenuItem>
                                        </DropdownMenuGroup>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarFooter>
                </Sidebar>
                <SidebarInset>
                    <header className="flex h-14 shrink-0 items-center gap-2 border-b px-4">
                        <SidebarTrigger />
                        <Separator orientation="vertical" className="h-4" />
                    </header>
                    <main className="flex flex-1 flex-col gap-4 p-4">{children}</main>
                </SidebarInset>
            </SidebarProvider>
        </TooltipProvider>
    );
}
