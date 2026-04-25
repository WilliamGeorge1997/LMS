<?php

if (!function_exists('getCaseCollection')) {
    /**
     * Return paginated or full collection based on request data.
     *
     * @param  mixed  $builder
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    function getCaseCollection($builder, array $data)
    {
        if ($data['paginated'] ?? null) {
            return $builder->paginate($data['paginated'] ?? 20);
        }

        return $builder->get();
    }
}
