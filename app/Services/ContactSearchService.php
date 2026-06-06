<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ContactSearchService
{
    public function handle(Request $request): Builder
    {
        $query = Contact::with('category');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $keyword = $request->input('keyword');
            $q->where(function ($subQuery) use ($keyword) {
                $subQuery->where('last_name', 'like', '%'.$keyword.'%')
                    ->orWhere('first_name', 'like', '%'.$keyword.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%');
            });
        });

        $query->when($request->filled('gender') && $request->input('gender') !== '0', function ($q) use ($request) {
            $q->where('gender', $request->input('gender'));
        });

        $query->whereMiss = $query->when($request->filled('category_id'), function ($q) use ($request) {
            $q->where('category_id', $request->input('category_id'));
        });

        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('created_at', $request->input('date'));
        });

        return $query;
    }
}
