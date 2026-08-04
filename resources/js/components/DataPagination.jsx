import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Pagination, PaginationContent, PaginationItem } from '@/components/ui/pagination';

export default function DataPagination({ links }) {
    if (links.length <= 3) {
        return null;
    }

    return (
        <Pagination className="mt-4 justify-end">
            <PaginationContent>
                {links.map((link, index) => (
                    <PaginationItem key={index}>
                        <Button
                            variant={link.active ? 'outline' : 'ghost'}
                            size="sm"
                            disabled={!link.url}
                            render={link.url ? <Link href={link.url} /> : undefined}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    </PaginationItem>
                ))}
            </PaginationContent>
        </Pagination>
    );
}
