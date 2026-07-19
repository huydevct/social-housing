export interface ProjectCard {
    name: string;
    slug: string;
    district: string | null;
    address: string | null;
    province: string | null;
    province_slug: string | null;
    status: string;
    status_label: string;
    price_from: number | null;
    price_to: number | null;
    area_from: number | null;
    area_to: number | null;
    application_end_at: string | null;
    cover: string | null;
}

export interface ProjectDetail extends ProjectCard {
    former_address: string | null;
    total_units: number | null;
    description: string | null;
    application_guide: string | null;
    application_start_at: string | null;
    investor: { name: string; website: string | null; phone: string | null } | null;
    images: { url: string }[];
    source_name: string | null;
    source_url: string | null;
    meta_title: string | null;
    meta_description: string | null;
}

export interface GuideCard {
    title: string;
    slug: string;
    excerpt: string | null;
}

export interface ProvinceLink {
    name: string;
    slug: string;
    projects_count?: number;
}

export interface StatusOption {
    value: string;
    label: string;
}

export interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
    total: number;
}
