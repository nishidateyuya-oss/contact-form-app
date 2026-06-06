<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexContactRequest $request)
    {
        // 1. Eager Loading を適用してN+1問題を回避
        $query = Contact::with(['category', 'tags']);

        // 2. keyword 検索（first_name / last_name / email の orWhere 部分一致）
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', '%'.$keyword.'%')
                    ->orWhere('last_name', 'like', '%'.$keyword.'%')
                    ->orWhere('email', 'like', '%'.$keyword.'%');
            });
        }

        // 3. gender フィルタ（パラメータ省略時は全件対象）
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // 4. category_id 絞り込み
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 5. 作成日フィルタ（date）
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 6. ページネーション件数の決定（デフォルト20、最大100）
        $perPage = $request->input('per_page', 20);

        // 新着順で取得して、APIリソースコレクションとして返却
        $paginator = $query->latest()->paginate($perPage);

        return ContactResource::collection($paginator)->additional([
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }
        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->additional(['message' => 'タスクを作成しました'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update($request->validated());
        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(['message' => 'お問い合わせを削除しました',
            'data' => $contact], 200);
    }
}
