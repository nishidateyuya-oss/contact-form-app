<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\IndexContactRequest;
use App\Services\ContactSearchService;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;

class AdminController extends Controller
{

    public function index(IndexContactRequest $request, ContactSearchService $searchService) 
    {
        $query = $searchService->handle($request);

        $contacts = $query->latest()->paginate(7);
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }

    public function show(Contact $contact) 
    {
        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect('/admin');
    }

}
