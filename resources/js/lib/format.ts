export function formatPricePerM2(from: number | null, to: number | null): string {
    if (!from) {
return 'Chưa công bố';
}

    const fmt = (n: number) => (n / 1_000_000).toLocaleString('vi-VN', { maximumFractionDigits: 1 });
    const unit = 'triệu/m²';

    if (!to || to === from) {
return `${fmt(from)} ${unit}`;
}

    return `${fmt(from)} - ${fmt(to)} ${unit}`;
}

export function formatArea(from: number | null, to: number | null): string {
    if (!from) {
return '—';
}

    if (!to || to === from) {
return `${from} m²`;
}

    return `${from} - ${to} m²`;
}

export function formatDate(iso: string | null): string {
    if (!iso) {
return '—';
}

    return new Date(iso).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}
