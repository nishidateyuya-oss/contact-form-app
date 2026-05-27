<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use App\Http\Requests\ExportContactRequest;
use App\Services\ContactSearchService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(StoreContactRequest $request) 
    {
        $validated = $request->validated();

        $tagIds = $request->input('tag_ids', []);

        $category = Category::find($validated['category_id']);
        $tags = Tag::whereIn('id', $tagIds)->get();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
    }

    public function store(StoreContactRequest $request) 
    {
        $contact = Contact::create($request->validated());
        if($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }

        return redirect('/thanks');
    }

    public function thanks() 
    {
        return view('contact.thanks');
    }

    public function export(ExportContactRequest $request, ContactSearchService $searchService): StreamedResponse
    {
        // 【条件1】 検索サービスを使って、条件に一致するクエリビルダを取得
        // （※サービス側の引数は一般的な「Illuminate\Http\Request」にしておいてください）
        $query = $searchService->handle($request);

        // 【条件2】 フィルタ未指定時も含め、全件を新着順（created_atの降順）で取得
        $contacts = $query->latest()->get();

        // レンスポンスヘッダーの設定
        $filename = 'contacts_export_' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // ストリームレスポンスを返す
        return response()->stream(function () use ($contacts) {
            $stream = fopen('php://output', 'w');

            // 【条件1】 Excel文字化け防止の「BOM」を出力
            fwrite($stream, "\xEF\xBB\xBF");

            // 【条件3】 1行目にヘッダーを出力（指定の列順）
            fputcsv($stream, [
                'ID', '氏名', '性別', 'メール', '電話', 
                '住所', '建物', 'カテゴリ', '内容', '作成日時'
            ]);

            // 性別の数値 $\rightarrow$ 文字列マッピング
            $genderMap = [1 => '男性', 2 => '女性', 3 => 'その他'];

            // データの書き込み
            foreach ($contacts as $contact) {
                // 【条件3】 指定された列順でデータを配列にする
                fputcsv($stream, [
                    $contact->id,
                    $contact->last_name . ' ' . $contact->first_name,       // 氏名（結合）
                    $genderMap[$contact->gender] ?? '不明',                 // 性別（文字列に変換）
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category ? $contact->category->content : '', // カテゴリ（文字列に変換）
                    $contact->detail,                                       // 内容
                    $contact->created_at->format('Y-m-d H:i:s'),            // 作成日時
                ]);
            }

            fclose($stream);
        }, 200, $headers);
    }
}
