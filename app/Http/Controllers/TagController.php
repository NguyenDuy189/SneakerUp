<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name|max:50'
        ]);

        Tag::create(['name' => $request->name]);
        return redirect()->route('tags.index')->with('success', 'Thêm tag thành công!');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|max:50|unique:tags,name,' . $tag->id
        ]);

        $tag->update(['name' => $request->name]);
        return redirect()->route('tags.index')->with('success', 'Cập nhật tag thành công!');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Xóa tag thành công!');
    }
}