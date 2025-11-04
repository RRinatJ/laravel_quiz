import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    role: string;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    can?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Quiz {
    id: number;
    title: string;
    is_work: boolean;
    timer_count?: number;    
    image: string | null;
    created_at?: string;
    fifty_fifty_hint?: boolean;
    can_skip?: boolean;
}

export interface PaginatedResourceResponse<T = unknown> {
    data: T[];
    links: {
        first: string,
        last: string,
        prev: string | null,
        next: string | null
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    }
}

export interface PaginatedResponse<T = unknown> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type PaginateData = PaginatedResponse | PaginatedResourceResponse

export interface Answer {
    id: number | string;
    text: string;    
    image: string;
    is_correct: boolean;
    question_id?: number;
}

export interface Question {
    id: number;
    question: string | null;
    image: string | null;    
    audio: string | null;
    quizzes_ids?: number[];
    quizzes?: Quiz[];
    answers?: Answer[];
    created_at?: string;
}

export interface AnswerInGame {
    id: number;
    text: string;
    image: string;
}
export interface Game {
    id: number;
    current_question_id: number;
    correct_count: number;
    quiz: Quiz;
    question?: Question;
    question_row: number[];
    created_at?: string;
    fifty_fifty_hint: boolean;
    can_skip: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
