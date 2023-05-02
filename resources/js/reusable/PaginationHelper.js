export default function paginatorHelper(data)
{
    return data.per_page() * (data.current_page() - 1);
}
